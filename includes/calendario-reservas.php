<?php
// Verificar si el usuario es administrador
if (!current_user_can('manage_options')) {
    wp_die(__('No tienes permisos para acceder a esta página.'));
}

// Incluir scripts y estilos para evo-calendar
wp_enqueue_script('evo-calendar-js', plugins_url('../assets/js/evo-calendar.min.js', __FILE__), array('jquery'), '1.1.3', true);
wp_enqueue_style('evo-calendar-css', plugins_url('../assets/css/evo-calendar.min.css', __FILE__), array(), '1.1.3');

// Obtener reservas del mes actual (asumiendo una función get_reservas_mes_actual() que retorna las reservas)
$reservas = get_reservas_mes_actual();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendario de Reservas</title>
    <?php wp_head(); ?>
</head>
<body>
    <div class="wrap">
        <h1>Calendario de Reservas</h1>
        <div id="calendar"></div>
    </div>
    <script>
        jQuery(document).ready(function($) {
            $("#calendar").evoCalendar({
                theme: "Royal Navy",
                eventListToggler: true,
                sidebarToggler: true,
                events: <?php echo json_encode($reservas); ?>
            });
        });
    </script>
    <?php wp_footer(); ?>
</body>
</html>
<?php
// Función para obtener las reservas del mes actual
function get_reservas_mes_actual() {
    global $wpdb;
    $mes_actual = date('m');
    $año_actual = date('Y');
    $tabla_reservas = $wpdb->prefix . 'reservas';
    $reservas = $wpdb->get_results($wpdb->prepare("
        SELECT * FROM $tabla_reservas
        WHERE MONTH(fecha_reserva) = %d AND YEAR(fecha_reserva) = %d
    ", $mes_actual, $año_actual));

    $eventos = array();
    foreach ($reservas as $reserva) {
        $eventos[] = array(
            'id' => $reserva->id,
            'name' => $reserva->nombre,
            'date' => $reserva->fecha_reserva . ' ' . $reserva->hora_reserva,
            'description' => $reserva->comentarios,
            'type' => 'event'
        );
    }

    return $eventos;
}
?>



