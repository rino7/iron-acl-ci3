<?php $descripciones_modulos = get_descripcion_modulos(); ?>
<h3>Asignando permisos a <mark><?php echo $nombre_grupo; ?></mark></h3>
<form action="/acl/acl_grupos/guardar_permisos_grupo" method="POST" >
    <input type="hidden" value="<?php echo $id_grupo; ?>" name="id_grupo" />
    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
        <?php foreach ($permisos as $controlador => $acciones) : ?>
            <div class="panel js-contenedor-controlador">
                <div class="panel-heading" role="tab" id="heading_<?php echo $controlador; ?>">
                    <h4 class="panel-title">
                        <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#acciones_<?php echo $controlador; ?>" aria-expanded="false" aria-controls="acciones_<?php echo $controlador; ?>">
                            <?php echo (isset($descripciones_modulos[$controlador])) ? $descripciones_modulos[$controlador] : $controlador; ?>
                        </a>
                    </h4>
                    <div class="pull-right js-botones-todo">
                        <button type="button" data-controlador="<?php echo $controlador; ?>" class="btn btn-default seleccionar-todos-permitido">Seleccionar todos</button>
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
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($acciones as $index => $accion) : ?>
                                    <tr>
                                        <td><?php echo ! empty($accion["descripcion"]) ? $accion["descripcion"] : $accion["accion"]; ?></td>
                                        <td><?php echo $accion["accion"]; ?></td>
                                        <td class="center">
                                            <input <?php echo (int) $accion["permitido"] === 1 ? "checked" : ""; ?>  class="js-checkbox-permitido-<?php echo $controlador; ?>" type="checkbox" value="1" name="permitido[<?php echo $accion["id_acl_permiso"]; ?>]" />
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
        <button type="submit" class="btn btn-primary" name="guardar" value="1">Guardar</button>
    </div>
</form>