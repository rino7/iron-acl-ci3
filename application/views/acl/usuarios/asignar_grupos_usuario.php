<h3>Asignando grupos a <mark><?php echo $nombre_usuario; ?></mark></h3>
<form action="/acl/acl_usuarios/guardar_grupos_usuario" method="POST">
    <input type="hidden" name="id_usuario" value="<?php echo $id_usuario; ?>" />
    <table class="table table-stripped table-bordered table-hover">
        <thead>
            <tr>
                <th><a href="<?php echo Acl_usuarios::RUTA_CONTROLADOR ?>/grupos_usuario/<?php echo $id_usuario; ?>/numero/<?php echo ($order_by === "numero") ? $sentido : "asc" ?>" <?php ($order_by === "numero") ? "class='active'" : "" ?>>#</a></th>
                <th><a href="<?php echo Acl_usuarios::RUTA_CONTROLADOR ?>/grupos_usuario/<?php echo $id_usuario; ?>/nombre/<?php echo ($order_by === "nombre") ? $sentido : "asc" ?>" <?php ($order_by === "nombre") ? "class='active'" : "" ?>>Nombre</a></th>
                <th><a href="<?php echo Acl_usuarios::RUTA_CONTROLADOR ?>/grupos_usuario/<?php echo $id_usuario; ?>/asignado/<?php echo ($order_by === "asignado") ? $sentido : "asc" ?>" <?php ($order_by === "asignado") ? "class='active'" : "" ?>>Asignado</a></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($grupos as $grupo): ?>
                <tr>
                    <td><?php echo $grupo["id_acl_grupo"]; ?></td>
                    <td><?php echo $grupo["nombre"]; ?></td>
                    <td>
                        <input <?php echo ((int) $grupo["asignado"] === 1) ? "checked" : ""; ?> type="checkbox" name="grupos[<?php echo $grupo["id_acl_grupo"]; ?>]" value="1" />
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <button type='submit' class='btn btn-success' name='guardar' value="1">Guardar</button>
</form>