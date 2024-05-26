<?php
// /templates/single-reserva.php
get_header();

if (have_posts()) :
    while (have_posts()) : the_post();
        global $post;
        $reservation_id = $post->ID;
        $reservation_meta = get_post_meta($reservation_id);

        echo '<h2>Detalles de la Reserva</h2>';
        echo '<p>Nombre: ' . esc_html(get_the_title()) . '</p>';
        echo '<p>Email: ' . esc_html($reservation_meta['email'][0]) . '</p>';
        echo '<p>Teléfono: ' . esc_html($reservation_meta['telefono'][0]) . '</p>';
        echo '<p>Fecha: ' . esc_html($reservation_meta['fecha_reserva'][0]) . '</p>';
        echo '<p>Hora: ' . esc_html($reservation_meta['hora_reserva'][0]) . '</p>';
        echo '<p>Número de personas: ' . esc_html($reservation_meta['num_personas'][0]) . '</p>';
        echo '<p>Comentarios: ' . esc_html($reservation_meta['comentarios'][0]) . '</p>';
    endwhile;
endif;

get_footer();
