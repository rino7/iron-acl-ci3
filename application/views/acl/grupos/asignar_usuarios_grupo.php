<h3>Asignando usuarios a <mark><?php echo $nombre_grupo; ?></mark></h3>
<br>
<form class="form-inline" method="POST" action="/acl/acl_grupos/guardar_usuario_grupo">
    <input type="hidden" name="id_grupo" id='id_grupo' value="<?php echo $id_grupo; ?>" />
    <input type="hidden" name="id_usuario" id='id_usuario' id="id_usuario" />
    <input class="form-control input-med js-asignar-usuario-grupo" data-id_grupo='<?php echo $id_grupo; ?>' type="text" name="usuario" id="usuario" placeholder="Ingrese el nombre o el apellido del usuario" required autocomplete="off"/>
    &nbsp;<button type="submit" class="btn btn-success"><span class="glyphicon glyphicon-plus"></span></button>
</form>
<br><br>
<?php if (empty($usuarios_asignados)): ?>
    <div>No hay usuarios asignados a este grupo</div>
<?php else: ?>
    <form method="POST" action="/acl/acl_grupos/desasignar_usuarios_grupo">
        <input type="hidden" name="id_grupo" value="<?php echo $id_grupo; ?>" />
        <table class="table table-stripped table-bordered table-hover">
            <thead>
                <tr>
                    <th><a href="<?php echo Acl_grupos::RUTA_CONTROLADOR ?>/asignar_usuarios_grupo/<?php echo $id_grupo; ?>/numero/<?php echo ($order_by === "numero") ? $sentido : "asc" ?>" <?php ($order_by === "numero") ? "class='active'" : "" ?>># Asignaci&oacute;n</a></th>
                    <th><a href="<?php echo Acl_grupos::RUTA_CONTROLADOR ?>/asignar_usuarios_grupo/<?php echo $id_grupo; ?>/nombre/<?php echo ($order_by === "nombre") ? $sentido : "asc" ?>" <?php ($order_by === "nombre") ? "class='active'" : "" ?>>Nombre</a></th>
                    <th>Quitar</th
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios_asignados as $usuario_asignado): ?>
                    <tr>
                        <td><?php echo $usuario_asignado["id_acl_usuario_grupo"]; ?></td>
                        <td><?php echo $usuario_asignado["apellido"] . ", " . $usuario_asignado["nombre"]; ?></td>
                        <td>
                            <input type="checkbox" name="usuarios[<?php echo $usuario_asignado["id_acl_usuario"]; ?>]" value="<?php echo $usuario_asignado["id_acl_usuario"]; ?>" />
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <button type="submit" class="btn btn-danger"><span class="glyphicon glyphicon-trash"></span>&nbsp;Eliminar seleccionados</button>
    </form>
<?php endif; ?>
