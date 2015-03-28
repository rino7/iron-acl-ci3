<h3>Permiso personalizado</h3>
<?php
if ($this->input->get("respuesta")) :
    $respuesta = $this->input->get("respuesta");
    if ($respuesta === "ok") {
        $class = "success";
        $mensaje = "El permiso ha sido actualizada correctamente";
    }
    if ($respuesta === "error_db") {
        $class = "danger";
        $mensaje = "El permiso no pudo ser guardado. Int&eacute;ntelo nuevamente.";
    }
    ?>
    <div class="js-alert alert alert-<?php echo $class; ?> alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <?php echo $mensaje; ?>
    </div>
<?php endif; ?>
<form class="form-vertical col-md-6 col-md-offset-3"  action="/acl/acl_permisos/guardar_custom" method="POST">
    <fieldset>
        <input type='hidden' name='id_permiso' id='id_permiso' value="<?php echo $id_permiso; ?>" />
        <div class='form-group'>
            <label>Identificador:</label>
            <input class='form-control' type='text' name='identificador' id='identificador' value='<?php echo (isset($data["identificador"])) ? $data["identificador"] : "" ?>' />
        </div>
        <div class='form-group'>
            <label>Descripci&oacute;n:</label>
            <input  class='form-control' type='text' name='descripcion' id='descripcion'  value='<?php echo (isset($data["descripcion"]) ) ? $data["descripcion"] : "" ?>'  />
        </div>
        <div class="checkbox">
            <label>
                <input type="checkbox" name="blacklist" value='1' <?php echo (isset($data["blacklist"]) AND (int) $data["blacklist"] === 1 ) ? "checked" : "" ?>> Requerido
            </label>
        </div>

    </fieldset>
    <button type="submit" name="guardar" value="1" class="btn btn-primary">Guardar</button>
</form>