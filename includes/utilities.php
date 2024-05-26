<?php

function rk_get_allowed_times() {
    return [
        '12:30', '12:45', '13:00', '13:15', '13:30', '13:45', '14:00', '14:15', '14:30', '14:45',
        '15:00', '15:15', '15:30', '19:30', '19:45', '20:00', '20:15', '20:30', '20:45', '21:00', 
        '21:15', '21:30', '21:45', '22:00', '22:15', '22:30'
    ];
}

// Definir un espacio global para almacenar temporalmente los detalles de la reserva
global $rk_reservation_details;
$rk_reservation_details = [];

// Función para establecer los detalles de la reserva en la variable global
function rk_set_reservation_details($details) {
    global $rk_reservation_details;
    $rk_reservation_details = $details;
}

// Función para obtener un detalle específico de la reserva desde la variable global
function rk_get_reservation_detail($key) {
    global $rk_reservation_details;
    return isset($rk_reservation_details[$key]) ? $rk_reservation_details[$key] : '';
}

// Shortcode para mostrar la fecha de la reserva
function rk_shortcode_date($atts) {
    return rk_get_reservation_detail('date');
}

// Shortcode para mostrar la hora de la reserva
function rk_shortcode_time($atts) {
    return rk_get_reservation_detail('time');
}

// Shortcode para mostrar el número de personas de la reserva
function rk_shortcode_people($atts) {
    return rk_get_reservation_detail('people');
}

// Registrar los shortcodes en WordPress
add_shortcode('rk_date', 'rk_shortcode_date');
add_shortcode('rk_time', 'rk_shortcode_time');
add_shortcode('rk_people', 'rk_shortcode_people');
