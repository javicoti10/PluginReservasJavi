<?php
function rk_display_reservation_details() {
    if (isset($_GET['reservation_id'])) {
        global $wpdb;
        $reservation_id = intval($_GET['reservation_id']);
        $reservation = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}reservas WHERE ID = %d", $reservation_id));

        if ($reservation) {
            ob_start();
            echo "<h2>Detalles de la Reserva</h2>";
            echo "<p>Nombre: {$reservation->nombre}</p>";
            echo "<p>Email: {$reservation->email}</p>";
            echo "<p>Teléfono: {$reservation->telefono}</p>";
            echo "<p>Fecha: {$reservation->fecha_reserva}</p>";
            echo "<p>Hora: {$reservation->hora_reserva}</p>";
            echo "<p>Número de personas: {$reservation->num_personas}</p>";
            echo "<p>Comentarios: {$reservation->comentarios}</p>";
            return ob_get_clean();
        } else {
            return "<p>Reserva no encontrada.</p>";
        }
    } else {
        return "<p>ID de reserva no proporcionado.</p>";
    }
}
add_shortcode('rk_reservation_details', 'rk_display_reservation_details');