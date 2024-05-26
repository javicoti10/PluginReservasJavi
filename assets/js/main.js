jQuery(document).ready(function($) {
    // Funcionalidad para comprobar la validez de la fecha de reserva
    $('#reservasForm').on('submit', function(e) {
        var fechaReserva = new Date($('input[name="fecha_reserva"]').val());
        var hoy = new Date();
        hoy.setHours(0, 0, 0, 0); // Eliminar la hora actual para comparar solo fechas

        // Comprobar si la fecha de reserva es al menos el día actual o posterior
        if (fechaReserva < hoy) {
            e.preventDefault(); // Detener el envío del formulario
            alert('Por favor, elige una fecha a partir de hoy.');
        }
    });
});

// Script para validaciones front-end y mejoras de interactividad