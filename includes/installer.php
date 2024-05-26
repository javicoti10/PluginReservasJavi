<?php
// Instalación del plugin
function rk_install() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'reservas';
    
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        nombre varchar(255) NOT NULL,
        email varchar(255) NOT NULL,
        telefono varchar(255) NOT NULL,
        fecha_reserva date NOT NULL,
        hora_reserva time NOT NULL,
        num_personas smallint NOT NULL,
        comentarios text NOT NULL,
        estado varchar(20) NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

//Maneja la instalación y configuración inicial del plugin.