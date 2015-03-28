<?php if ( ! empty($respuesta)) : ?>
    <div class="js-alert alert alert-<?php echo $respuesta["class"]; ?> alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <?php echo $respuesta["mensaje"]; ?>
    </div>
<?php endif; ?>
<?php
//Permisos
$puede_eliminar = tiene_permiso("/acl_usuarios/eliminar_usuarios");
$puede_crear = tiene_permiso("/acl_usuarios/nuevo");
$puede_modificar = tiene_permiso("/acl_usuarios/editar");
$puede_asignar_permisos = tiene_permiso("/acl_usuarios/permisos_usuario");
$puede_asignar_grupos = tiene_permiso("/acl_usuarios/grupos_usuario");
?>
<div class="row botonera">
    <div class="col-md-12">
        <div class="buscador-paginador">
            <div class="pull-left">
                <form action="/acl/acl_usuarios/listar" method="GET" class="form-inline" role="form">
                    <div class="form-group">
                        <input type="text" class="form-control input-med" value="<?php echo $this->input->get("texto_buscar"); ?>" name="texto_buscar" id="texto_buscar" placeholder="Buscar usuario" required>
                    </div>
                    <button type="submit" class="btn btn-primary" name="buscar" value="1"><span class="glyphicon glyphicon-search"></span></button>
                    <a href="/acl/acl_usuarios/listar" class="btn btn-default"><span class="glyphicon glyphicon-trash"></span></a>
                </form>
            </div>
            <?php if ($puede_crear === TRUE) : ?>
                <div class="pull-right">
                    <a href="/acl/acl_usuarios/nuevo/" class="btn btn-primary">Nuevo</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<form method="POST" action="/acl/acl_usuarios/eliminar_usuarios">
    <table class="table table-stripped table-bordered table-hover">
        <thead>
            <tr>
                <th><a href="<?php echo Acl_usuarios::RUTA_LISTADO ?>/numero/<?php echo ($order_by === "numero") ? $sentido : "asc" ?>" <?php ($order_by === "numero") ? "class='active'" : "" ?>>#</a></th>
                <th><a href="<?php echo Acl_usuarios::RUTA_LISTADO ?>/nombre/<?php echo ($order_by === "nombre") ? $sentido : "asc" ?>" <?php ($order_by === "nombre") ? "class='active'" : "" ?>>Nombre</a></th>
                <th><a href="<?php echo Acl_usuarios::RUTA_LISTADO ?>/usuario/<?php echo ($order_by === "usuario") ? $sentido : "asc" ?>" <?php ($order_by === "usuario") ? "class='active'" : "" ?>>Usuario</a></th>
                <th><a href="<?php echo Acl_usuarios::RUTA_LISTADO ?>/activo/<?php echo ($order_by === "activo") ? $sentido : "asc" ?>" <?php ($order_by === "activo") ? "class='active'" : "" ?>>Activo</a></th>
                <th>Acciones</th>
                <?php if ($puede_eliminar === TRUE) : ?>
                    <th>Eliminar</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $usuario): ?>
                <tr>
                    <td><?php echo $usuario["id_acl_usuario"]; ?></td>
                    <td><?php echo $usuario["nombre"] . ", " . $usuario["nombre"]; ?></td>
                    <td><?php echo $usuario["usuario"]; ?></td>
                    <td>
                        <a data-id="<?php echo $usuario["id_acl_usuario"]; ?>" data-accion="<?php echo $usuario["activo"] === "S" ? "desactivar" : "activar"; ?>" class="btn btn-<?php echo $usuario["activo"] === "S" ? "success" : "danger"; ?> js-toggle-activar" data-entidad="usuarios"><?php echo $usuario["activo"] === "S" ? "SI" : "NO"; ?></a>
                    </td>
                    <td>
                        <?php if ($puede_eliminar === TRUE) : ?>
                            <a class="btn btn-primary" href="/acl/acl_usuarios/editar/<?php echo $usuario["id_acl_usuario"]; ?>">
                                <span class="glyphicon glyphicon-edit"></span>
                            </a>
                        <?php endif; ?>
                        <?php if ($puede_asignar_permisos === TRUE) : ?>
                            &nbsp;
                            <a class="btn btn-warning" href="/acl/acl_usuarios/permisos_usuario/<?php echo $usuario["id_acl_usuario"]; ?>">
                                <span class="glyphicon glyphicon-lock"></span>
                            </a>
                        <?php endif; ?>
                        <?php if ($puede_asignar_grupos === TRUE) : ?>
                            &nbsp;
                            <a class="btn btn-success" href="/acl/acl_usuarios/grupos_usuario/<?php echo $usuario["id_acl_usuario"]; ?>">
                                <span class="glyphicon glyphicon-user"></span>
                            </a>
                        <?php endif; ?>
                    </td>
                    <?php if ($puede_eliminar === TRUE) : ?>
                        <td>
                            <input type="checkbox" name="usuarios[<?php echo $usuario["id_acl_usuario"]; ?>]" value="<?php echo $usuario["id_acl_usuario"]; ?>" />
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php if ($puede_eliminar === TRUE) : ?>
        <button type="submit" class="btn btn-danger pull-right" name="eliminar" value="1"><span class="glyphicon glyphicon-trash"></span>&nbsp;Eliminar seleccionados</button>
    <?php endif; ?>
</form>
<?php $this->load->view("acl/paginador"); ?>
