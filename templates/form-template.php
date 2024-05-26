<?php
// Horarios permitidos
$horarios = rk_get_allowed_times(); // Esta función debe estar definida en 'utilities.php'

?>
<form action="" method="post" id="reservasForm">
    <input type="text" name="nombre" placeholder="Nombre completo" required maxlength="40">
    <input type="email" name="email" placeholder="Email" required pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$">
    <input type="tel" name="telefono" placeholder="Teléfono" required pattern="\d{9}">
    <input type="date" name="fecha_reserva" placeholder="Fecha de reserva" required>
    <select name="hora_reserva" required>
        <?php foreach ($horarios as $hora) : ?>
            <option value="<?php echo $hora; ?>"><?php echo $hora; ?></option>
        <?php endforeach; ?>
    </select>
    <input type="number" name="num_personas" placeholder="Número de personas" required min="1" max="10">
    <textarea name="comentarios" placeholder="Comentarios (alergias, intolerancias, etc.)" oninput="limitWords(this);" maxlength="500"></textarea>
    <input type="submit" value="Enviar Reserva">
</form>

<script>
function limitWords(textarea) {
    var maxWords = 100;
    var words = textarea.value.split(/\s+/);
    if (words.length > maxWords) {
        textarea.value = words.slice(0, maxWords).join(" ");
        alert('Solo puedes ingresar hasta 100 palabras en los comentarios.');
    }
}
</script>
