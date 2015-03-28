<h3>Cambiar contrase&ntilde;a de : <mark><?php echo $nombre_usuario; ?></mark></h3>
<?php
if ($this->input->get("respuesta")) :
    $respuesta = $this->input->get("respuesta");
    if ($respuesta === "ok") {
        $class = "success";
        $mensaje = "La contrase&ntilde;a ha sido actualizada correctamente";
    }
    if ($respuesta === "error_db") {
        $class = "danger";
        $mensaje = "La contrase&ntilde;a no pudo ser actualizada. Int&eacute;ntelo nuevamente.";
    }
    if ($respuesta === "contrasenia_invalida") {
        $class = "danger";
        $mensaje = "Las contrase&ntilde;as no coinciden.";
    }
    ?>
    <div class="js-alert alert alert-<?php echo $class; ?> alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <?php echo $mensaje; ?>
    </div>
<?php endif; ?>
<form class="form-vertical col-md-6 col-md-offset-3"  action="/acl/acl_usuarios/cambiar_contrasenia" method="POST">
    <fieldset>
        <input type='hidden' name='id_usuario' id='id_usuario' value="<?php echo $id_usuario; ?>" />
        <div class='form-group'>
            <label>Contrase&ntilde;a:</label>
            <input class='form-control' type='password' name='contrasenia' id='contrasenia' />
        </div>
        <div class='form-group'>
            <label>Repetir contrase&ntilde;a:</label>
            <input  class='form-control' type='password' name='repite_contrasenia' id='repite_contrasenia' />
        </div>
    </fieldset>
    <button type="submit" name="guardar" value="1" class="btn btn-primary">Guardar</button>
</form>