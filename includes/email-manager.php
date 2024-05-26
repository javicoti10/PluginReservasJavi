<?php
// Función para enviar el correo electrónico de confirmación al cliente
function rk_send_confirmation_email($customer_email, $reservation_id, $status, $reservation_details) {
    global $rk_reservation_details;
    $rk_reservation_details = $reservation_details;

    $subject = get_option('koalum_settings')['email_subject'] ?? 'Estado de su reserva';
    $headers = array('Content-Type: text/html; charset=UTF-8');

    // Elegir la plantilla de correo basada en el estado actual de la reserva
    switch ($status) {
        case 'confirmed':
            $template = get_option('rk_email_template_confirmed', 'Su reserva ha sido aceptada para [rk_date] a las [rk_time] para [rk_people] personas.');
            break;
        case 'cancelled':
            $template = get_option('rk_email_template_cancelled', 'Lamentamos informarle que su reserva para [rk_date] a las [rk_time] ha sido rechazada.');
            break;
        case 'pending': // Considerar 'pending' explícitamente para claridad
        default:
            $template = get_option('rk_email_template_pending', 'Su reserva está pendiente para [rk_date] a las [rk_time] para [rk_people] personas.');
            break;
    }

    // Reemplazar los placeholders en la plantilla
    $message = str_replace('[rk_date]', $reservation_details['date'], $template);
    $message = str_replace('[rk_time]', $reservation_details['time'], $message);
    $message = str_replace('[rk_people]', $reservation_details['people'], $message);

    wp_mail($customer_email, $subject, $message, $headers);
}

// Función para enviar un correo electrónico al administrador del sitio con enlaces para confirmar o cancelar la reserva
function rk_send_admin_notification_email($customer_email, $reservation_id) {
    global $wpdb;
    $admin_email = get_option('admin_email', get_bloginfo('admin_email'));
    $confirm_link = admin_url('admin-post.php?action=confirm_reservation&reservation_id=' . $reservation_id);
    $cancel_link = admin_url('admin-post.php?action=cancel_reservation&reservation_id=' . $reservation_id);

    // Obtener detalles de la reserva
    $reservation = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}reservas WHERE ID = %d", $reservation_id));
    if (!$reservation) {
        return; // No hacer nada si la reserva no existe
    }

    $message = "<p>Nueva reserva de {$customer_email}.</p>";
    $message .= "<p>Detalles de la reserva:</p>";
    $message .= "<ul>";
    $message .= "<li>Fecha: {$reservation->fecha_reserva}</li>";
    $message .= "<li>Hora: {$reservation->hora_reserva}</li>";
    $message .= "<li>Número de personas: {$reservation->num_personas}</li>";
    $message .= "</ul>";
    $message .= "<p><a href='{$confirm_link}'>Confirmar</a> | <a href='{$cancel_link}'>Cancelar</a></p>";

    // Usar el tipo de contenido HTML para el correo electrónico
    add_filter('wp_mail_content_type', function() { return 'text/html'; });

    wp_mail($admin_email, 'Nueva reserva recibida', $message);

    // Restaurar el tipo de contenido predeterminado
    remove_filter('wp_mail_content_type', 'set_html_content_type');
}

// Acción para confirmar la reserva
add_action('admin_post_confirm_reservation', 'rk_confirm_reservation');
function rk_confirm_reservation() {
    global $wpdb;

    if (!isset($_GET['reservation_id'])) {
        wp_redirect(admin_url());
        exit;
    }

    $reservation_id = intval($_GET['reservation_id']);
    // Actualizar el estado de la reserva a 'confirmed'
    $wpdb->update(
        "{$wpdb->prefix}reservas",
        array('estado' => 'confirmed'),
        array('id' => $reservation_id),
        array('%s'),
        array('%d')
    );

    // Obtener el correo del cliente y los detalles de la reserva para enviar el correo
    $reservation = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}reservas WHERE id = %d", $reservation_id));
    if ($reservation) {
        $customer_email = $reservation->email;
        $reservation_details = array(
            'date' => $reservation->fecha_reserva,
            'time' => $reservation->hora_reserva,
            'people' => $reservation->num_personas
        );
        rk_send_confirmation_email($customer_email, $reservation_id, 'confirmed', $reservation_details);
    }

    // Redirigir al administrador a la URL de confirmación
    wp_redirect('https://asesoran-cp23.wordpresstemporal.com/pruebaplugin/reserva-aceptada/');
    exit;
}

// Acción para cancelar la reserva
add_action('admin_post_cancel_reservation', 'rk_cancel_reservation');
function rk_cancel_reservation() {
    global $wpdb;

    if (!isset($_GET['reservation_id'])) {
        wp_redirect(admin_url());
        exit;
    }

    $reservation_id = intval($_GET['reservation_id']);
    // Actualizar el estado de la reserva a 'cancelled'
    $wpdb->update(
        "{$wpdb->prefix}reservas",
        array('estado' => 'cancelled'),
        array('id' => $reservation_id),
        array('%s'),
        array('%d')
    );

    // Obtener el correo del cliente y los detalles de la reserva para enviar el correo
    $reservation = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}reservas WHERE id = %d", $reservation_id));
    if ($reservation) {
        $customer_email = $reservation->email;
        $reservation_details = array(
            'date' => $reservation->fecha_reserva,
            'time' => $reservation->hora_reserva,
            'people' => $reservation->num_personas
        );
        rk_send_confirmation_email($customer_email, $reservation_id, 'cancelled', $reservation_details);
    }

    // Redirigir al administrador a la URL de cancelación
    wp_redirect('https://asesoran-cp23.wordpresstemporal.com/pruebaplugin/reserva-rechazada/');
    exit;
}

// Función para enviar correo de confirmación de edición o borrado
function send_email($to, $subject, $message) {
    wp_mail($to, $subject, $message);
}

function send_reserva_updated_email($post_id) {
    $to = get_post_meta($post_id, 'reserva_email', true);
    $subject = 'Tu reserva ha sido actualizada';
    $message = 'Los detalles de tu reserva han sido actualizados. Aquí están los nuevos detalles: ...';
    send_email($to, $subject, $message);
}

function send_reserva_deleted_email($post_id) {
    $to = get_post_meta($post_id, 'reserva_email', true);
    $subject = 'Tu reserva ha sido borrada';
    $message = 'Tu reserva con los siguientes detalles ha sido borrada: ...';
    send_email($to, $subject, $message);
}
