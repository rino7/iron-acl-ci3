<div class="row botonera">
    <div class="col-md-12">
        <div class="buscador-paginador">
            <div class="pull-right">
                <a href="/acl/acl_permisos/custom/" class="btn btn-primary">Nuevo</a>
            </div>
        </div>
    </div>
</div>
<form action="/acl/acl_permisos/guardar" method="POST" id="form_permisos">
    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
        <?php foreach ($permisos as $controlador => $acciones) : ?>
            <div class="panel panel-default js-contenedor-controlador">
                <div class="panel-heading" role="tab" id="heading_<?php echo $controlador; ?>">
                    <h4 class="panel-title">
                        <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#acciones_<?php echo $controlador; ?>" aria-expanded="false" aria-controls="acciones_<?php echo $controlador; ?>">
                            <?php echo ( ! empty($acciones["descripcion_modulo"])) ? $acciones["descripcion_modulo"] . " (" . $controlador . ")" : $controlador; ?>
                        </a>
                    </h4>
                    <input type="hidden" name="controladores[<?php echo $controlador; ?>]" value="<?php echo ( ! empty($acciones["descripcion_modulo"])) ? $acciones["descripcion_modulo"] : $controlador; ?>" />
                    <div class="pull-right js-botones-todo">
                        <button type="button" data-controlador="<?php echo $controlador; ?>" class="btn btn-success js-permitir-todo">Permitir todo</button>
                        <button type="button" data-controlador="<?php echo $controlador; ?>" class="btn btn-danger js-requerir-todo">Requerir todo</button>
                    </div>
                </div>
                <div id="acciones_<?php echo $controlador; ?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading_<?php echo $controlador; ?>">
                    <div class="panel-body">
                        <table class="table table-stripped table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th>Descripci&oacute;n</th>
                                    <th>Acci&oacute;n</th>
                                    <th class="center">Permitido</th>
                                    <th class="center">Requerido</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $permisos = $acciones["permisos"];
                                foreach ($permisos as $index => $accion) :
                                    ?>
                                    <tr>
                                        <td>
                                            <input type='hidden' value='<?php echo $accion["descripcion"]; ?>' name='descripcion[<?php echo $accion["identificador"]; ?>]' />
                                            <?php if ($controlador === Acl::CONTROLADOR_CUSTOM) : ?>
                                                <a href="/acl/acl_permisos/custom/<?php echo $accion["id_acl_permiso"]; ?>" class="glyphicon glyphicon-edit"></a> |
                                            <?php endif; ?>
                                            <?php echo ! empty($accion["descripcion"]) ? $accion["descripcion"] : $accion["accion"]; ?>
                                        </td>
                                        <td><?php echo $accion["accion"]; ?></td>
                                        <td class="center">
                                            <input <?php echo (int) $accion["whitelist"] === 1 ? "checked" : ""; ?> class="js-radio-whitelist js-radio-whitelist-<?php echo $controlador; ?>" type="radio" value="0" name="blacklist[<?php echo $accion["identificador"]; ?>]" />
                                        </td>
                                        <td class="center">
                                            <input <?php echo (int) $accion["blacklist"] === 1 ? "checked" : ""; ?>  class="js-radio-blacklist js-radio-blacklist-<?php echo $controlador; ?>" type="radio" value="1" name="blacklist[<?php echo $accion["identificador"]; ?>]" />
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