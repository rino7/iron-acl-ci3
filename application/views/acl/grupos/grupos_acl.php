<?php if ( ! empty($respuesta)) : ?>
    <div class="js-alert alert alert-<?php echo $respuesta["class"]; ?> alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <?php echo $respuesta["mensaje"]; ?>
    </div>
<?php endif; ?>
<?php
//Permisos
$puede_crear = tiene_permiso("grupos/crear");
$puede_editar = tiene_permiso("grupos/editar");
$puede_eliminar = tiene_permiso("acl_grupos/eliminar_grupos");
$puede_asignar_usuarios = tiene_permiso("acl_grupos/asignar_usuarios_grupo");
$puede_asignar_permisos = tiene_permiso("acl_grupos/permisos_grupo");
?>
<?php if ($puede_editar OR $puede_crear): ?>
    <div class="panel panel-info">
        <div class="panel-heading">Crear/Editar grupo</div>
        <div class="panel-body">
            <form method="POST" action="/acl/acl_grupos/guardar" class="form-horizontal" role="form">
                <fieldset>
                    <input type="hidden" name="id_grupo" value="0" id="id_grupo" />
                    <div class="form-group col-xs-6">
                        <label for="nombre">Nombre</label>
                        <input type="text" class="form-control" name="nombre" id="nombre" required="">
                    </div>
                </fieldset>
                <button type="submit" class="btn btn-info" name="guardar" value="1">Guardar</button>
            </form>
        </div>
    </div>
<?php endif; ?>
<div class="row botonera">
    <div class="col-md-12">
        <div class="buscador-paginador">
            <div class="pull-left">
                <form action="/acl/acl_grupos/listar" method="GET" class="form-inline" role="form">
                    <div class="form-group">
                        <input type="text" class="form-control input-med" value="<?php echo $this->input->get("texto_buscar"); ?>" name="texto_buscar" id="texto_buscar" placeholder="Buscar grupo" required>
                    </div>
                    <button type="submit" class="btn btn-primary" name="buscar" value="1"><span class="glyphicon glyphicon-search"></span></button>
                    <a href="/acl/acl_grupos/listar" class="btn btn-default"><span class="glyphicon glyphicon-trash"></span></a>
                </form>
            </div>
            <div class="pull-right">
                <?php $this->load->view("acl/paginador"); ?>
            </div>
        </div>
    </div>
</div>
<form method="POST" action="/acl/acl_grupos/eliminar_grupos" class="clearfix">
    <table class="table table-stripped table-bordered table-hover">
        <thead>
            <tr>
                <th><a href="<?php echo Acl_grupos::RUTA_LISTADO ?>/numero/<?php echo ($order_by === "numero") ? $sentido : "asc" ?>" <?php ($order_by === "numero") ? "class='active'" : "" ?>>#</a></th>
                <th><a href="<?php echo Acl_grupos::RUTA_LISTADO ?>/nombre/<?php echo ($order_by === "nombre") ? $sentido : "asc" ?>" <?php ($order_by === "nombre") ? "class='active'" : "" ?>>Nombre</a></th>
                <th><a href="<?php echo Acl_grupos::RUTA_LISTADO ?>/activo/<?php echo ($order_by === "activo") ? $sentido : "asc" ?>" <?php ($order_by === "activo") ? "class='active'" : "" ?>>Activo</a></th>
                <th>Acciones</th>
                <?php if ($puede_eliminar):
                    ?>
                    <th>Eliminar</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($grupos as $grupo): ?>
                <tr>
                    <td><?php echo $grupo["id_acl_grupo"]; ?></td>
                    <td class="js-td-nombre-<?php echo $grupo["id_acl_grupo"]; ?>"><?php echo $grupo["nombre"]; ?></td>
                    <td>
                        <a data-id="<?php echo $grupo["id_acl_grupo"]; ?>" data-accion="<?php echo $grupo["activo"] === "S" ? "desactivar" : "activar"; ?>" class="btn btn-<?php echo $grupo["activo"] === "S" ? "success" : "danger"; ?> js-toggle-activar" data-entidad="grupos"><?php echo $grupo["activo"] === "S" ? "SI" : "NO"; ?></a>
                    </td>
                    <td>
                        <?php if ($puede_editar): ?>
                            <a class="btn btn-primary js-editar-grupo" data-id="<?php echo $grupo["id_acl_grupo"]; ?>">
                                <span class="glyphicon glyphicon-edit"></span>
                            </a>
                        <?php endif; ?>
                        <?php if ($puede_asignar_permisos): ?>
                            &nbsp;
                            <a class="btn btn-warning" href="/acl/acl_grupos/permisos_grupo/<?php echo $grupo["id_acl_grupo"]; ?>">
                                <span class="glyphicon glyphicon-lock"></span>
                            </a>
                        <?php endif; ?>
                        <?php if ($puede_asignar_usuarios): ?>
                            &nbsp;
                            <a class="btn btn-success" href="/acl/acl_grupos/asignar_usuarios_grupo/<?php echo $grupo["id_acl_grupo"]; ?>">
                                <span class="glyphicon glyphicon-user"></span>
                            </a>
                        <?php endif; ?>
                    </td>
                    <?php if ($puede_eliminar): ?>
                        <td>
                            <input type="checkbox" name="grupos[<?php echo $grupo["id_acl_grupo"]; ?>]" value="<?php echo $grupo["id_acl_grupo"]; ?>" />
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php if ($puede_eliminar): ?>
        <button type="submit" class="btn btn-danger pull-right" name="eliminar" value="1"><span class="glyphicon glyphicon-trash"></span>&nbsp;Eliminar seleccionados</button>
    <?php endif; ?>
</form>
<?php $this->load->view("acl/paginador"); ?>

