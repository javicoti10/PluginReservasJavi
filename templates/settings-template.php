<?php
// Manejar la presentación y guardado de la configuración del correo electrónico de reserva
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Guardar la plantilla del correo electrónico actualizada
    update_option('rk_email_template', sanitize_textarea_field($_POST['email_template']));
    echo '<div>Configuraciones guardadas correctamente.</div>';
}
?>
<div class="wrap">
    <h2>Configuración Plugin Reservas Javi Cotilla</h2>
    <form method="post" action="">
        <textarea name="email_template">
            <?php echo get_option('rk_email_template', 'Su reserva está confirmada para [rk_date] a las [rk_time] para [rk_people] personas.'); ?>
        </textarea>
        <input type="submit" value="Guardar Configuración">
    </form>
</div>
