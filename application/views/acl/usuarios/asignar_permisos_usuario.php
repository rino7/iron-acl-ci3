<?php $descripciones_modulos = get_descripcion_modulos(); ?>
<h3>Asignando permisos a <mark><?php echo $nombre_usuario; ?></mark></h3>
<form action="/acl/acl_usuarios/guardar_permisos_usuario" method="POST">
    <input type="hidden" name="id_usuario" value="<?php echo $id_usuario; ?>" />
    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
        <?php foreach ($permisos as $controlador => $acciones) : ?>
            <div class="panel panel-default js-contenedor-controlador">
                <div class="panel-heading" role="tab" id="heading_<?php echo $controlador; ?>">
                    <h4 class="panel-title">
                        <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#acciones_<?php echo $controlador; ?>" aria-expanded="false" aria-controls="acciones_<?php echo $controlador; ?>">
                            <?php echo (isset($descripciones_modulos[$controlador])) ? $descripciones_modulos[$controlador] : $controlador; ?>
                        </a>
                    </h4>
                    <div class="pull-right js-botones-todo">
                        <button type="button" data-controlador="<?php echo $controlador; ?>" class="btn btn-success js-permitir-todo">Permitir todo</button>
                        <button type="button" data-controlador="<?php echo $controlador; ?>" class="btn btn-danger js-requerir-todo">Denegar todo</button>
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
                                    <th class="center">Estado Actual</th>
                                    <th class="center">Heredado De</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($acciones as $index => &$accion) :
                                    $id_acl_permiso = (int) $accion["id_acl_permiso"];
                                    $permitido = NULL;
                                    $heredado_de = array();

                                    if (isset($permisos_de_usuario[$id_acl_permiso])) {
                                        $permitido = (int) $permisos_de_usuario[$id_acl_permiso]["tipo_permiso"] === 1;
                                        $heredado_de = $permisos_de_usuario[$id_acl_permiso]["heredado_de"]["nombres"];
                                    }
                                    ?>
                                    <tr>
                                        <td><?php echo ! empty($accion["descripcion"]) ? $accion["descripcion"] : $accion["accion"]; ?></td>
                                        <td><?php echo $accion["accion"]; ?></td>
                                        <td class="center">
                                            <input class="js-radio-whitelist-<?php echo $controlador; ?>" type="radio" value="1" name="permitido[<?php echo $id_acl_permiso; ?>]" />

                                        </td>
                                        <td class="center">
                                            <input  class="js-radio-blacklist-<?php echo $controlador; ?>" type="radio" value="0" name="permitido[<?php echo $id_acl_permiso; ?>]" />
                                        </td>
                                        <td  class="center">
                                            <span  class="<?php echo ($permitido === TRUE) ? "show" : "hidden"; ?> js-icon-whitelist js-icon-whitelist-<?php echo $controlador; ?> glyphicon glyphicon-ok green"></span>
                                            <span  class="<?php echo ($permitido !== TRUE) ? "show" : "hidden"; ?> js-icon-blacklist js-icon-blacklist-<?php echo $controlador; ?> glyphicon glyphicon-remove red" ></span>
                                        </td>
                                        <td class="center">
                                            <?php if ( ! empty($heredado_de)) : ?>
                                                <ul class="list-unstyled">
                                                    <?php
                                                    foreach ($heredado_de as $id_herencia => $nombre_grupo) :
                                                        $tipo_permiso = (int) $permisos_de_usuario[$id_acl_permiso]["heredado_de"]["tipos_permiso"][$id_herencia];
                                                        ?>
                                                        <li style="text-align: left;">
                                                            <span class="caret"></span>
                                                            <?php echo ($tipo_permiso === 1) ? "Permitido por: " : "Denegado por:"; ?>
                                                            &nbsp;<?php echo $nombre_grupo; ?>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            <?php else:; ?>
                                                Sin asignar.
                                            <?php endif; ?>
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