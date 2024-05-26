<?php
if (!defined('ABSPATH')) exit; // Protección contra acceso directo no autorizado

// Función para agregar menús y submenús al panel de administración
function koalum_add_admin_menu() {
    add_menu_page('Configuración de Plugin Reservas Javi', 'Plugin Reservas Javi', 'manage_options', 'koalum', 'koalum_options_page');
    
    add_submenu_page('koalum', 'Historial de Reservas', 'Historial de Reservas', 'manage_options', 'koalum_historial', 'koalum_history_page');
    add_submenu_page('koalum', 'Personalización de Emails', 'Emails', 'manage_options', 'koalum_emails', 'koalum_emails_page');
    add_submenu_page('koalum', 'Configuración de Horarios', 'Horarios', 'manage_options', 'koalum_horarios', 'koalum_schedules_page');
    add_submenu_page('koalum', 'Calendario de Reservas', 'Calendario', 'manage_options', 'koalum_calendario', 'koalum_calendar_page');
}
add_action('admin_menu', 'koalum_add_admin_menu');

// Función para redirigir al calendario
function koalum_calendar_page() {
    include plugin_dir_path(__FILE__) . 'calendario-reservas.php';
}

// Función para mostrar el historial de reservas
function koalum_history_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'reservas';

    if (isset($_GET['email'])) {
        $email = sanitize_email($_GET['email']);
        $reservas = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE email = %s", $email));

        if ($reservas) {
            echo '<h2>Historial de Reservas de ' . esc_html($email) . '</h2>';
            echo '<table class="widefat fixed" cellspacing="0">';
            echo '<thead><tr><th>ID</th><th>Nombre</th><th>Email</th><th>Teléfono</th><th>Fecha Reserva</th><th>Hora Reserva</th><th>Num. Personas</th><th>Comentarios</th><th>Estado</th></tr></thead>';
            echo '<tbody>';
            foreach ($reservas as $reserva) {
                $reserva_id = isset($reserva->id) ? esc_html($reserva->id) : '';  
                $nombre = isset($reserva->nombre) ? esc_html($reserva->nombre) : '';
                $email = isset($reserva->email) ? esc_html($reserva->email) : '';
                $telefono = isset($reserva->telefono) ? esc_html($reserva->telefono) : '';
                $fecha_reserva = isset($reserva->fecha_reserva) ? esc_html($reserva->fecha_reserva) : '';
                $hora_reserva = isset($reserva->hora_reserva) ? esc_html($reserva->hora_reserva) : '';
                $num_personas = isset($reserva->num_personas) ? esc_html($reserva->num_personas) : '';
                $comentarios = isset($reserva->comentarios) ? esc_html($reserva->comentarios) : '';
                $estado = isset($reserva->estado) ? esc_html($reserva->estado) : '';

                echo '<tr>';
                echo '<td>' . $reserva_id . '</td>';
                echo '<td>' . $nombre . '</td>';
                echo '<td>' . $email . '</td>';
                echo '<td>' . $telefono . '</td>';
                echo '<td>' . $fecha_reserva . '</td>';
                echo '<td>' . $hora_reserva . '</td>';
                echo '<td>' . $num_personas . '</td>';
                echo '<td>' . $comentarios . '</td>';
                echo '<td>' . $estado . '</td>';
                echo '</tr>';
            }
            echo '</tbody></table>';
            echo '<a href="' . admin_url('admin.php?page=koalum_historial') . '">Volver al listado</a>';
        } else {
            echo '<p>No se encontraron reservas para ' . esc_html($email) . '.</p>';
            echo '<a href="' . admin_url('admin.php?page=koalum_historial') . '">Volver al listado</a>';
        }
    } else {
        $emails = $wpdb->get_results("SELECT DISTINCT email, nombre FROM $table_name");

        if ($emails) {
            echo '<h2>Historial de Reservas</h2>';
            echo '<table class="widefat fixed" cellspacing="0">';
            echo '<thead><tr><th>Email</th><th>Nombre</th></tr></thead>';
            echo '<tbody>';
            foreach ($emails as $email) {
                echo '<tr>';
                echo '<td><a href="' . admin_url('admin.php?page=koalum_historial&email=' . esc_attr($email->email)) . '">' . esc_html($email->email) . '</a></td>';
                echo '<td>' . esc_html($email->nombre) . '</td>';
            }
            echo '</tbody></table>';
        } else {
            echo '<p>No se encontraron reservas.</p>';
        }
    }
}

// Función para registrar configuraciones de emails
function koalum_register_email_settings() {
    register_setting('koalum_email_settings_group', 'koalum_email_settings', 'koalum_settings_validate');

    add_settings_section('koalum_email_settings', 'Personalización de Emails', 'koalum_settings_section_callback', 'koalum_emails');
    add_settings_field('koalum_email_subject', 'Asunto del Email de Confirmación', 'koalum_email_subject_field_callback', 'koalum_emails', 'koalum_email_settings', array('field' => 'email_subject'));
    add_settings_field('koalum_email_confirmed', 'Email de Reserva Confirmada', 'koalum_email_field_callback', 'koalum_emails', 'koalum_email_settings', array('field' => 'email_confirmed'));
    add_settings_field('koalum_email_subject_pending', 'Asunto del Email de Reserva Pendiente', 'koalum_email_subject_field_callback', 'koalum_emails', 'koalum_email_settings', array('field' => 'email_subject_pending'));
    add_settings_field('koalum_email_pending', 'Email de Reserva Pendiente', 'koalum_email_field_callback', 'koalum_emails', 'koalum_email_settings', array('field' => 'email_pending'));
    add_settings_field('koalum_email_subject_rejected', 'Asunto del Email de Reserva Rechazada', 'koalum_email_subject_field_callback', 'koalum_emails', 'koalum_email_settings', array('field' => 'email_subject_rejected'));
    add_settings_field('koalum_email_cancelled', 'Email de Reserva Cancelada', 'koalum_email_field_callback', 'koalum_emails', 'koalum_email_settings', array('field' => 'email_cancelled'));
}
add_action('admin_init', 'koalum_register_email_settings');

// Función para registrar configuraciones de horarios
function koalum_register_schedule_settings() {
    register_setting('koalum_schedule_settings_group', 'koalum_schedule_settings', 'koalum_settings_validate');

    add_settings_section('koalum_schedule_settings', 'Configuración de Horarios', 'koalum_settings_section_callback', 'koalum_horarios');
    $days = ['lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado', 'domingo'];
    foreach ($days as $day) {
        add_settings_field("{$day}_schedule", ucfirst($day), 'koalum_day_schedule_field_callback', 'koalum_horarios', 'koalum_schedule_settings', array('day' => $day));
    }
}
add_action('admin_init', 'koalum_register_schedule_settings');

function koalum_settings_section_callback() {
    echo '<p>Configura los detalles aquí.</p>';
}

function koalum_email_subject_field_callback($args) {
    $options = get_option('koalum_email_settings', array());
    $field = $args['field'];
    $value = isset($options[$field]) ? esc_attr($options[$field]) : '';
    echo "<input type='text' name='koalum_email_settings[$field]' value='$value'>";
}

function koalum_email_field_callback($args) {
    $options = get_option('koalum_email_settings', array());
    $field = $args['field'];
    $value = isset($options[$field]) ? esc_textarea($options[$field]) : '';
    echo "<textarea name='koalum_email_settings[$field]' rows='5' cols='50'>$value</textarea>";
}

function koalum_day_schedule_field_callback($args) {
    $options = get_option('koalum_schedule_settings', array());
    $day = $args['day'];
    $lunch_start = isset($options["{$day}_lunch_start"]) ? esc_attr($options["{$day}_lunch_start"]) : '';
    $lunch_end = isset($options["{$day}_lunch_end"]) ? esc_attr($options["{$day}_lunch_end"]) : '';
    $dinner_start = isset($options["{$day}_dinner_start"]) ? esc_attr($options["{$day}_dinner_start"]) : '';
    $dinner_end = isset($options["{$day}_dinner_end"]) ? esc_attr($options["{$day}_dinner_end"]) : '';
    echo "Almuerzo inicio: <input type='time' name='koalum_schedule_settings[{$day}_lunch_start]' value='$lunch_start'>";
    echo " Almuerzo fin: <input type='time' name='koalum_schedule_settings[{$day}_lunch_end]' value='$lunch_end'>";
    echo " Cena inicio: <input type='time' name='koalum_schedule_settings[{$day}_dinner_start]' value='$dinner_start'>";
    echo " Cena fin: <input type='time' name='koalum_schedule_settings[{$day}_dinner_end]' value='$dinner_end'>";
}

// Función para mostrar la página de configuración de emails
function koalum_emails_page() {
    ?>
    <div class="wrap">
        <h2>Personalización de Emails</h2>
        <form action="options.php" method="post">
            <?php
            settings_fields('koalum_email_settings_group');
            do_settings_sections('koalum_emails');
            submit_button('Guardar Cambios');
            ?>
        </form>
    </div>
    <?php
}

// Función para mostrar la página de configuración de horarios
function koalum_schedules_page() {
    ?>
    <div class="wrap">
        <h2>Configuración de Horarios</h2>
        <form action="options.php" method="post">
            <?php
            settings_fields('koalum_schedule_settings_group');
            do_settings_sections('koalum_horarios');
            submit_button('Guardar Cambios');
            ?>
        </form>
    </div>
    <?php
}

function koalum_settings_validate($input) {
    // Validación de los campos
    $validated = array();
    foreach ($input as $key => $value) {
        $validated[$key] = sanitize_text_field($value);
    }
    return $validated;
}





