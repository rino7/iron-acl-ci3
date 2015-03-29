<?php
$error = $this->input->get("error");
if ($error AND $this->session->flashdata("postdata")):
    ?>
    <?php if ($error === "usuario_existente"): ?>
        <div class='alert alert-danger'>
            <p>Atenci&oacute;n: El usuario <strong><?php echo $data["usuario"]; ?></strong> ya se encuentra registrado. Utilice otro por favor</p>
        </div>
    <?php endif; ?>
<?php endif; ?>
<form method="POST" class="form-vertical col-md-6 col-md-offset-3" action="/acl/acl_usuarios/guardar">
    <fieldset>
        <input type="hidden" name="id_usuario" value="<?php echo (int) $id_usuario; ?>" />
        <div class="form-group">
            <label>Nombre</label>
            <input type="text" name="nombre" id="nombre" class="form-control" value="<?php echo isset($data["nombre"]) ? $data["nombre"] : ""; ?>" required />
        </div>
        <div class="form-group">
            <label>Apellido</label>
            <input type="text" name="apellido" id="apellido" class="form-control" value="<?php echo isset($data["apellido"]) ? $data["apellido"] : ""; ?>" required />
        </div>
        <div class="form-group">
            <label>Usuario</label>
            <input autocomplete="off" type="text" name="usuario" id="usuario" class="form-control" value="<?php echo isset($data["usuario"]) ? $data["usuario"] : ""; ?>" required />
        </div>
        <?php if ($id_usuario === 0) : ?>
            <div class="form-group">
                <label>Contrase&ntilde;a</label>
                <input autocomplete="off" type="password" name="contrasenia" id="contrasenia" class="form-control" value="" required />
            </div>
            <div class="form-group">
                <label>Repetir Contrase&ntilde;a</label>
                <input autocomplete="off" type="password" name="repite_contrasenia" id="repite_contrasenia" class="form-control" value="" required />
            </div>
        <?php else: ?>
            <a href="/acl/acl_usuarios/cambiar_contrasenia/<?php echo $id_usuario; ?>" class="btn btn-warning">Cambiar contrase&ntilde;a</a>
        <?php endif; ?>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" id="email" class="form-control" value="<?php echo isset($data["email"]) ? $data["email"] : ""; ?>" />
        </div>
        <div class="form-group">
            <label>Activo</label>
            <select name="activo" class="form-control" required>
                <option <?php echo ( ! isset($data["activo"])) ? "selected" : ""; ?> value="">Seleccione</option>
                <option <?php echo (isset($data["activo"]) AND $data["activo"] === "S") ? "selected" : ""; ?> value="S">SI</option>
                <option <?php echo (isset($data["activo"]) AND $data["activo"] === "N") ? "selected" : ""; ?> value="N">NO</option>
            </select>
        </div>
        <button type="submit" name="guardar" value="1" class="btn btn-primary">Guardar</button>
    </fieldset>
</form>