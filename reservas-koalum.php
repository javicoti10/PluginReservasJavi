<?php
/*
Plugin Name: Plugin Reservas Javi Cotilla
Plugin URI: https://koalum.com/pruebaplugin
Description: Motor de reservas para restaurantes.
Version: 1.0
Author: Javier Cotilla Segovia
Author URI: https://koalum.com
Text Domain: reservas-koalum
*/

// Registrar Custom Post Type 'Reservas'
function koalum_register_bookings_cpt() {
    $labels = array(
        'name'                  => _x('Reservas', 'Post type general name', 'textdomain'),
        'singular_name'         => _x('Reserva', 'Post type singular name', 'textdomain'),
        'menu_name'             => _x('Reservas', 'Admin Menu text', 'textdomain'),
        'name_admin_bar'        => _x('Reserva', 'Add New on Toolbar', 'textdomain'),
        'add_new'               => __('Añadir Nueva', 'textdomain'),
        'add_new_item'          => __('Añadir Nueva Reserva', 'textdomain'),
        'new_item'              => __('Nueva Reserva', 'textdomain'),
        'edit_item'             => __('Editar Reserva', 'textdomain'),
        'view_item'             => __('Ver Reserva', 'textdomain'),
        'all_items'             => __('Todas las Reservas', 'textdomain'),
        'search_items'          => __('Buscar Reservas', 'textdomain'),
        'parent_item_colon'     => __('Reservas Padre:', 'textdomain'),
        'not_found'             => __('No se encontraron Reservas.', 'textdomain'),
        'not_found_in_trash'    => __('No se encontraron Reservas en la papelera.', 'textdomain'),
        'featured_image'        => _x('Imagen Destacada', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'textdomain'),
        'set_featured_image'    => _x('Establecer imagen destacada', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'textdomain'),
        'remove_featured_image' => _x('Eliminar imagen destacada', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'textdomain'),
        'use_featured_image'    => _x('Usar como imagen destacada', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'textdomain'),
        'archives'              => _x('Archivo de Reservas', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'textdomain'),
        'insert_into_item'      => _x('Insertar en la reserva', 'Overrides the “Insert into post” phrase (used when inserting media into a post). Added in 4.4', 'textdomain'),
        'uploaded_to_this_item' => _x('Subido a esta reserva', 'Overrides the “Uploaded to this post” phrase (used when viewing media attached to a post). Added in 4.4', 'textdomain'),
        'filter_items_list'     => _x('Filtrar lista de reservas', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”. Added in 4.4', 'textdomain'),
        'items_list_navigation' => _x('Navegación de lista de reservas', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”. Added in 4.4', 'textdomain'),
        'items_list'            => _x('Lista de reservas', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”. Added in 4.4', 'textdomain'),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'reserva'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array('title', 'thumbnail'),
    );

    register_post_type('reserva', $args);
}
add_action('init', 'koalum_register_bookings_cpt');

// Registrar metaboxes personalizados
function koalum_register_meta_boxes() {
    add_meta_box('koalum_reserva_details', 'Detalles de la Reserva', 'koalum_booking_details_callback', 'reserva', 'normal', 'high');
}
add_action('add_meta_boxes', 'koalum_register_meta_boxes');

// Callback para mostrar los campos personalizados
function koalum_booking_details_callback($post) {
    wp_nonce_field('koalum_save_booking_details', 'koalum_reserva_details_nonce');
    
    $id = get_post_meta($post->ID, '_koalum_id', true);
    $nombre = get_post_meta($post->ID, '_koalum_nombre', true);
    $email = get_post_meta($post->ID, '_koalum_email', true);
    $telefono = get_post_meta($post->ID, '_koalum_telefono', true);
    $num_personas = get_post_meta($post->ID, '_koalum_num_personas', true);
    $fecha = get_post_meta($post->ID, '_koalum_fecha', true);
    $hora = get_post_meta($post->ID, '_koalum_hora', true);
    $comentarios = get_post_meta($post->ID, '_koalum_comentarios', true);

    echo '<style>
        .koalum-form-group {
            margin-bottom: 15px;
        }
        .koalum-form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .koalum-form-group input,
        .koalum-form-group textarea {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }
    </style>';

    echo '<div class="koalum-form-group">';
    echo '<label for="koalum_id">ID:</label>';
    echo '<input type="text" id="koalum_id" name="koalum_id" value="' . esc_attr($id) . '" readonly />';
    echo '</div>';

    echo '<div class="koalum-form-group">';
    echo '<label for="koalum_nombre">Nombre:</label>';
    echo '<input type="text" id="koalum_nombre" name="koalum_nombre" value="' . esc_attr($nombre) . '" required />';
    echo '</div>';

    echo '<div class="koalum-form-group">';
    echo '<label for="koalum_email">Email:</label>';
    echo '<input type="email" id="koalum_email" name="koalum_email" value="' . esc_attr($email) . '" required />';
    echo '</div>';

    echo '<div class="koalum-form-group">';
    echo '<label for="koalum_telefono">Teléfono:</label>';
    echo '<input type="text" id="koalum_telefono" name="koalum_telefono" value="' . esc_attr($telefono) . '" required />';
    echo '</div>';

    echo '<div class="koalum-form-group">';
    echo '<label for="koalum_num_personas">Número de personas:</label>';
    echo '<input type="number" id="koalum_num_personas" name="koalum_num_personas" value="' . esc_attr($num_personas) . '" min="1" required />';
    echo '</div>';

    echo '<div class="koalum-form-group">';
    echo '<label for="koalum_fecha">Fecha:</label>';
    echo '<input type="date" id="koalum_fecha" name="koalum_fecha" value="' . esc_attr($fecha) . '" required />';
    echo '</div>';

    echo '<div class="koalum-form-group">';
    echo '<label for="koalum_hora">Hora:</label>';
    echo '<input type="time" id="koalum_hora" name="koalum_hora" value="' . esc_attr($hora) . '" required />';
    echo '</div>';

    echo '<div class="koalum-form-group">';
    echo '<label for="koalum_comentarios">Comentarios:</label>';
    echo '<textarea id="koalum_comentarios" name="koalum_comentarios" rows="4" cols="50">' . esc_textarea($comentarios) . '</textarea>';
    echo '</div>';
}

// Guardar datos de metaboxes y validaciones
function koalum_save_booking_details($post_id) {
    if (!isset($_POST['koalum_reserva_details_nonce'])) {
        return $post_id;
    }

    $nonce = $_POST['koalum_reserva_details_nonce'];

    if (!wp_verify_nonce($nonce, 'koalum_save_booking_details')) {
        return $post_id;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    if ('reserva' != $_POST['post_type']) {
        return $post_id;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return $post_id;
    }

    $errors = [];
    
    if (empty($_POST['koalum_nombre'])) {
        $errors[] = 'El nombre es obligatorio.';
    }
    if (empty($_POST['koalum_email']) || !filter_var($_POST['koalum_email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'El email es obligatorio y debe ser válido.';
    }
    if (empty($_POST['koalum_telefono']) || !preg_match('/^[0-9\-\(\)\/\+\s]*$/', $_POST['koalum_telefono'])) {
        $errors[] = 'El teléfono es obligatorio y debe ser válido.';
    }
    if (empty($_POST['koalum_num_personas']) || !filter_var($_POST['koalum_num_personas'], FILTER_VALIDATE_INT)) {
        $errors[] = 'El número de personas es obligatorio y debe ser un número válido.';
    }
    if (empty($_POST['koalum_fecha'])) {
        $errors[] = 'La fecha es obligatoria.';
    }
    if (empty($_POST['koalum_hora'])) {
        $errors[] = 'La hora es obligatoria.';
    }

    if (!empty($errors)) {
        set_transient('koalum_reserva_errors', $errors, 30);
        remove_action('save_post', 'koalum_save_booking_details');
        wp_update_post(['ID' => $post_id, 'post_status' => 'draft']);
        add_action('save_post', 'koalum_save_booking_details');
        return $post_id;
    }

    $fields = [
        'koalum_nombre',
        'koalum_email',
        'koalum_telefono',
        'koalum_num_personas',
        'koalum_fecha',
        'koalum_hora',
        'koalum_comentarios',
    ];

    $data = [];
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            $data[$field] = sanitize_text_field($_POST[$field]);
            update_post_meta($post_id, '_' . $field, $data[$field]);
        }
    }

    // Guardar en la tabla personalizada
    global $wpdb;
    $table_name = $wpdb->prefix . 'reservas';

    $wpdb->replace(
        $table_name,
        [
            'ID' => $post_id,
            'nombre' => $data['koalum_nombre'],
            'email' => $data['koalum_email'],
            'telefono' => $data['koalum_telefono'],
            'num_personas' => $data['koalum_num_personas'],
            'fecha_reserva' => $data['koalum_fecha'],
            'hora_reserva' => $data['koalum_hora'],
            'comentarios' => $data['koalum_comentarios'],
            'estado' => 'pendiente',
        ]
    );

    // Enviar correo de confirmación de edición
    send_reservation_update_email($post_id, $data);
}
add_action('save_post', 'koalum_save_booking_details');

// Mostrar errores de validación
function koalum_show_booking_errors() {
    if ($errors = get_transient('koalum_reserva_errors')) {
        delete_transient('koalum_reserva_errors');
        echo '<div class="error"><ul>';
        foreach ($errors as $error) {
            echo '<li>' . esc_html($error) . '</li>';
        }
        echo '</ul></div>';
    }
}
add_action('admin_notices', 'koalum_show_booking_errors');

// Función para enviar correo electrónico con datos actualizados
function send_reservation_update_email($reservation_id, $data) {
    $to = $data['koalum_email'];
    $subject = 'Actualización de su reserva';
    $message = "
    <html>
    <head>
    <title>Actualización de su reserva</title>
    </head>
    <body>
    <h2>Su reserva ha sido modificada. Los datos actualizados son los siguientes:</h2>
    <p>Nombre: {$data['koalum_nombre']}</p>
    <p>Email: {$data['koalum_email']}</p>
    <p>Teléfono: {$data['koalum_telefono']}</p>
    <p>Fecha: {$data['koalum_fecha']}</p>
    <p>Hora: {$data['koalum_hora']}</p>
    <p>Número de personas: {$data['koalum_num_personas']}</p>
    <p>Comentarios: {$data['koalum_comentarios']}</p>
    </body>
    </html>";

    $headers = array('Content-Type: text/html; charset=UTF-8');

    wp_mail($to, $subject, $message, $headers);
}

// Agregar columnas personalizadas a la lista de reservas
function koalum_set_custom_edit_booking_columns($columns) {
    $columns['nombre'] = __('Nombre', 'textdomain');
    $columns['email'] = __('Email', 'textdomain');
    $columns['telefono'] = __('Teléfono', 'textdomain');
    $columns['num_personas'] = __('Número de Personas', 'textdomain');
    $columns['fecha_reserva'] = __('Fecha', 'textdomain');
    $columns['hora_reserva'] = __('Hora', 'textdomain');
    $columns['acciones'] = __('Acciones', 'textdomain');
    return $columns;
}
add_filter('manage_reserva_posts_columns', 'koalum_set_custom_edit_booking_columns');

// Mostrar los datos en las columnas personalizadas
function koalum_custom_booking_column($column, $post_id) {
    switch ($column) {
        case 'nombre':
            echo esc_html(get_post_meta($post_id, '_koalum_nombre', true));
            break;
        case 'email':
            echo esc_html(get_post_meta($post_id, '_koalum_email', true));
            break;
        case 'telefono':
            echo esc_html(get_post_meta($post_id, '_koalum_telefono', true));
            break;
        case 'num_personas':
            echo esc_html(get_post_meta($post_id, '_koalum_num_personas', true));
            break;
        case 'fecha_reserva':
            echo esc_html(get_post_meta($post_id, '_koalum_fecha', true));
            break;
        case 'hora_reserva':
            echo esc_html(get_post_meta($post_id, '_koalum_hora', true));
            break;
        case 'acciones':
            echo '<a href="' . get_edit_post_link($post_id) . '">Editar</a> | ';
            echo '<a href="' . get_delete_post_link($post_id) . '" onclick="return confirm(\'¿Estás seguro de que quieres borrar esta reserva?\')">Borrar</a>';
            break;
    }
}
add_action('manage_reserva_posts_custom_column', 'koalum_custom_booking_column', 10, 2);

// Incluir estilos y scripts
function rk_enqueue_scripts() {
    wp_enqueue_style('rk-reservas-style', plugin_dir_url(__FILE__) . 'assets/css/style.css');
    wp_enqueue_script('rk-reservas-script', plugin_dir_url(__FILE__) . 'assets/js/main.js', array('jquery'), false, true);
}
add_action('wp_enqueue_scripts', 'rk_enqueue_scripts');

// Incluir scripts y estilos para evo-calendar en la página de administración
function rk_enqueue_admin_scripts($hook) {
    if ($hook != 'toplevel_page_koalum') {
        return;
    }
    wp_enqueue_style('evo-calendar-css', plugins_url('/assets/css/evo-calendar.min.css', __FILE__));
    wp_enqueue_script('evo-calendar-js', plugins_url('/assets/js/evo-calendar.min.js', __FILE__), array('jquery'), '1.1.3', true);
}
add_action('admin_enqueue_scripts', 'rk_enqueue_admin_scripts');

// Reemplaza funciones obsoletas
remove_action('wp_print_styles', 'print_emoji_styles');
add_action('wp_enqueue_scripts', 'wp_enqueue_emoji_styles');

remove_action('wp_before_admin_bar_render', 'wp_admin_bar_header');
add_action('admin_enqueue_scripts', 'wp_enqueue_admin_bar_header_styles');

// Cargar archivos necesarios
include_once plugin_dir_path(__FILE__) . 'includes/form-handler.php';
include_once plugin_dir_path(__FILE__) . 'includes/installer.php';
include_once plugin_dir_path(__FILE__) . 'includes/utilities.php';
include_once plugin_dir_path(__FILE__) . 'includes/email-manager.php';
include_once plugin_dir_path(__FILE__) . 'includes/settings.php';

// Shortcode para insertar el formulario de reservas
function rk_reservas_form_shortcode() {
    ob_start();
    include(plugin_dir_path(__FILE__) . 'templates/form-template.php');
    return ob_get_clean();
}
add_shortcode('formulario_reservas', 'rk_reservas_form_shortcode');

// Hook para crear la tabla en la base de datos al activar el plugin
function rk_activate_plugin() {
    rk_install();
}
register_activation_hook(__FILE__, 'rk_activate_plugin');

// Añadir manejo para confirmación y cancelación de reservas
add_action('admin_post_confirm_reservation', 'rk_handle_confirm_reservation');
add_action('admin_post_cancel_reservation', 'rk_handle_cancel_reservation');

function rk_handle_confirm_reservation() {
    $reservation_id = isset($_GET['reservation_id']) ? intval($_GET['reservation_id']) : 0;
    if ($reservation_id) {
        rk_update_reservation_status($reservation_id, 'confirmed');
        wp_redirect('https://asesoran-cp23.wordpresstemporal.com/pruebaplugin/reserva-aceptada/?reservation_id=' . $reservation_id);
        exit;
    }
}

function rk_handle_cancel_reservation() {
    $reservation_id = isset($_GET['reservation_id']) ? intval($_GET['reservation_id']) : 0;
    if ($reservation_id) {
        rk_update_reservation_status($reservation_id, 'cancelled');
        wp_redirect('https://asesoran-cp23.wordpresstemporal.com/pruebaplugin/reserva-rechazada/?reservation_id=' . $reservation_id);
        exit;
    }
}

function rk_update_reservation_status($reservation_id, $new_status) {
    global $wpdb;
    $wpdb->update($wpdb->prefix . 'reservas', ['estado' => $new_status], ['ID' => $reservation_id]);

    // Obtener detalles de la reserva para enviar el email correcto
    $reservation = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}reservas WHERE ID = $reservation_id");
    $to = $reservation->email;
    $subject = '';
    $message = '';

    if ($new_status === 'confirmed') {
        $subject = 'Su reserva ha sido aceptada';
        $message = 'Estimado/a ' . $reservation->nombre . ",\n\nSu reserva para el " . $reservation->fecha_reserva . " a las " . $reservation->hora_reserva . " ha sido aceptada.\n\nGracias por reservar con nosotros.";
    } elseif ($new_status === 'cancelled') {
        $subject = 'Su reserva ha sido rechazada';
        $message = 'Estimado/a ' . $reservation->nombre . ",\n\nLamentamos informarle que su reserva para el " . $reservation->fecha_reserva . " a las " . $reservation->hora_reserva . " ha sido rechazada.\n\nPor favor, contacte con nosotros para más detalles.";
    }

    // Enviar el email
    if (!empty($subject) && !empty($message)) {
        wp_mail($to, $subject, $message);
    }
}

// Hook para registrar el widget
add_action('wp_dashboard_setup', 'reservas_hoy_dashboard_widget');

function reservas_hoy_dashboard_widget() {
    wp_add_dashboard_widget(
        'reservas_hoy_widget', // Widget slug.
        'Reservas Para Hoy', // Título.
        'mostrar_reservas_hoy' // Función que muestra el contenido.
    );
}

function mostrar_reservas_hoy() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'reservas';
    $hoy = date('Y-m-d');
    $estado = isset($_GET['estado']) ? sanitize_text_field($_GET['estado']) : '';

    $query = "SELECT * FROM $table_name WHERE DATE(fecha_reserva) = %s";
    $params = array($hoy);

    if ($estado) {
        $query .= " AND estado = %s";
        $params[] = $estado;
    }

    $reservas = $wpdb->get_results($wpdb->prepare($query, ...$params));

    if (!empty($reservas)) {
        echo '<ul class="reservas-list">';
        foreach ($reservas as $reserva) {
            $delete_url = wp_nonce_url(admin_url('admin-post.php?action=delete_reservation&reservation_id=' . $reserva->id), 'delete_reservation_' . $reserva->id);
            $email_url = admin_url('admin.php?page=koalum_historial&email=' . urlencode($reserva->email));
            echo '<li class="reserva-item">';
            echo '<span class="reserva-nombre">' . esc_html($reserva->nombre) . '</span>';
            echo ' - <span class="reserva-hora">' . esc_html($reserva->hora_reserva) . '</span>';
            echo ' - <span class="reserva-personas">' . esc_html($reserva->num_personas) . ' personas</span>';
            echo ' - <a href="' . $email_url . '" class="reserva-email">' . esc_html($reserva->email) . '</a>';
            echo ' <a href="' . get_edit_post_link($reserva->id) . '" class="reserva-editar">Editar</a>';
            echo ' | <a href="' . $delete_url . '" class="reserva-borrar" onclick="return confirm(\'¿Estás seguro de que quieres borrar esta reserva?\')">Borrar</a>';
            echo '</li>';
        }
        echo '</ul>';
    } else {
        echo 'No hay reservas para hoy.';
    }

    echo '<p><a href="' . admin_url('admin.php?page=koalum_historial') . '">Ver historial de reservas</a></p>';

    echo '<form method="get" class="reservas-filtro">';
    echo '<input type="hidden" name="post_type" value="reserva" />';
    echo '<select name="estado">';
    echo '<option value="">Todos los estados</option>';
    echo '<option value="pendiente">Pendientes</option>';
    echo '<option value="confirmed">Confirmados</option>';
    echo '<option value="cancelled">Cancelados</option>';
    echo '</select>';
    echo '<input type="submit" value="Filtrar" />';
    echo '</form>';
}

// Manejar la eliminación de la reserva
add_action('admin_post_delete_reservation', 'rk_handle_delete_reservation');

function rk_handle_delete_reservation() {
    if (!isset($_GET['reservation_id']) || !wp_verify_nonce($_GET['_wpnonce'], 'delete_reservation_' . $_GET['reservation_id'])) {
        wp_die('No tienes permiso para hacer esto');
    }

    $reservation_id = intval($_GET['reservation_id']);
    global $wpdb;
    $wpdb->delete($wpdb->prefix . 'reservas', ['id' => $reservation_id]);

    wp_redirect(wp_get_referer());
    exit;
}

// Agregar el handler de AJAX para administradores
add_action('wp_ajax_load_reservations', 'rk_load_reservations_callback');

function rk_load_reservations_callback() {
    check_ajax_referer('load_reservations_nonce', 'nonce');

    global $wpdb;
    $reservations = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}reservas");

    $events = array();
    foreach ($reservations as $reservation) {
        $events[] = array(
            'title' => $reservation->num_personas . ' personas - ' . $reservation->nombre,
            'start' => $reservation->fecha_reserva . 'T' . $reservation->hora_reserva,
            'allDay' => false // Define si el evento ocupa todo el día
        );
    }

    wp_send_json($events);
    wp_die(); // este llamado es necesario para terminar correctamente con las funciones de WordPress AJAX
}

// Incluir el archivo de shortcodes
include_once plugin_dir_path(__FILE__) . 'shortcodes.php';


// Actualización para la compatibilidad con WordPress 6.4.0
if (function_exists('wp_admin_bar_header')) {
    remove_action('wp_head', 'wp_admin_bar_header');
    add_action('wp_head', 'wp_enqueue_admin_bar_header_styles');
}
if (function_exists('wp_admin_bar_header')) {
    remove_action('admin_head', 'wp_admin_bar_header');
    add_action('admin_head', 'wp_enqueue_admin_bar_header_styles');
}