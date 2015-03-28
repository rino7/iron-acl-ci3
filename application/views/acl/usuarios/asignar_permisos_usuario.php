<?php $descripciones_modulos = get_descripcion_modulos(); ?>
<h3>Asignando permisos a <mark><?php echo $nombre_usuario; ?></mark></h3>
<form action="/acl/acl_usuarios/guardar_permisos_usuario" method="POST">
    <input type="hidden" name="id_usuario" value="<?php echo $id_usuario; ?>" />
    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
        <?php foreach ($permisos as $controlador => $acciones) : ?>
            <div class="panel panel-default">
                <div class="panel-heading" role="tab" id="heading_<?php echo $controlador; ?>">
                    <h4 class="panel-title">
                        <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#acciones_<?php echo $controlador; ?>" aria-expanded="false" aria-controls="acciones_<?php echo $controlador; ?>">
                            <?php echo (isset($descripciones_modulos[$controlador])) ? $descripciones_modulos[$controlador] : $controlador; ?>
                        </a>
                    </h4>
                    <div class="pull-right js-botones-todo">
                        <button type="button" data-controlador="<?php echo $controlador; ?>" class="btn btn-success js-permitir-todo">Todo permitido</button>
                        <button type="button" data-controlador="<?php echo $controlador; ?>" class="btn btn-danger js-requerir-todo">Todo requerido</button>
                    </div>
                </div>
                <div id="acciones_<?php echo $controlador; ?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading_<?php echo $controlador; ?>">
                    <div class="panel-body">
                        <table class="table table-stripped table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th>Descripci&oacute;n</th>
                                    <th>Acci&oacute;n</th>
                                    <th class="center">Permtir</th>
                                    <th class="center">Denegar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($acciones as $index => $accion) : ?>
                                    <tr>
                                        <td><?php echo ! empty($accion["descripcion"]) ? $accion["descripcion"] : $accion["accion"]; ?></td>
                                        <td><?php echo $accion["accion"]; ?></td>
                                        <td class="center">
                                            <input <?php echo (int) $accion["permitido"] === 1 ? "checked" : ""; ?>  class="js-radio-whitelist-<?php echo $controlador; ?>" type="radio" value="1" name="permitido[<?php echo $accion["id_acl_permiso"]; ?>]" />
                                        </td>
                                        <td class="center">
                                            <input <?php echo (int) $accion["denegado"] === 1 ? "checked" : ""; ?>  class="js-radio-blacklist-<?php echo $controlador; ?>" type="radio" value="0" name="permitido[<?php echo $accion["id_acl_permiso"]; ?>]" />
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="">
        <button type="submit" class="btn btn-success" name="guardar" value="1">Guardar</button>
    </div>
</form>