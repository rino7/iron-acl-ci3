$(document).ready(function () {
    $('.js-tooltip').tooltip();
    $('.js-alert').alert();

    //@TODO: unificar las clases para que el mismo m�todo sirva tanto para checbox como para radios.
    $('.seleccionar-todos-permitido').on('click', function () {
        var $this = $(this);
        var class_activo = 'seleccionado'
        var controlador = $this.data("controlador");
        var checkBoxes = $(".js-checkbox-permitido-" + controlador);
        var check_value;

        if ($this.hasClass(class_activo)) {
            $this.removeClass(class_activo);
            check_value = false;
        } else {
            $this.addClass(class_activo);
            check_value = true;
        }
        checkBoxes.each(function () {
            $(this).prop("checked", check_value);
        });
        var $contenedor = $this.parents('.js-contenedor-controlador');
        actualizarStatusControlador($contenedor);
    });

    $('.js-permitir-todo').on('click.permitirtodo', function () {
        var $this = $(this);
        seleccionarTodoPermitido($this);

    });
    $('.js-contenedor-controlador').on('click', 'input[type=checkbox]', function () {
        var $contenedor = $(this).parents('.js-contenedor-controlador');
        actualizarStatusControlador($contenedor);
    });
    $('.js-requerir-todo').on('click.requerirtodo', function () {
        var $this = $(this);
        seleccionarTodoRequerido($this);
    });
    $('.js-toggle-activar').on('click.toggleActivar', function (e) {
        e.preventDefault();
        var data = $(this).data();
        toggleActivar(data.entidad, data.accion, $(this));
    });
    $('.js-editar-grupo').on('click.editarGrupo', function (e) {
        e.preventDefault();
        var data = $(this).data();
        var id_grupo = data.id;
        var nombre = $('.js-td-nombre-' + id_grupo).html();
        $('#nombre').val(nombre);
        $('#id_grupo').val(id_grupo);
        $('.panel-info').removeClass('panel-info').addClass('panel-warning');
    });
    $('.js-asignar-usuario-grupo').autocomplete({
        'source': '/acl/acl_grupos/ajax_buscar_usuario?id_grupo=' + $("#id_grupo").val(),
        'select': function (event, ui) {
            if (typeof ui.item.id_acl_usuario !== 'undefined') {
                $('#id_usuario').val(ui.item.id_acl_usuario);
            } else {
                $('#id_usuario').val('0')
            }

        }
    });
    actualizarStatusControladores();
});

function actualizarStatusControladores() {
    var $contenedores_controlador = $('.js-contenedor-controlador');
    $contenedores_controlador.each(function () {
        actualizarStatusControlador($(this));
    });
}

function actualizarStatusControlador($element) {
    var $contenedor = $element;
    var cant_checkboxs = $contenedor.find("input[type=checkbox]").length;
    var cant_checked = $contenedor.find("input[type=checkbox]:checked").length;
    var color = '';
    console.log($contenedor);
    console.info(cant_checkboxs)
    if (cant_checkboxs > 0) {
        if (cant_checked === 0) {
            color = 'panel-danger';
        }
        if (cant_checked === cant_checkboxs) {
            color = 'panel-success';
        }
        if (cant_checked > 0 && cant_checked < cant_checkboxs) {
            color = 'panel-warning';
        }
    }
    $contenedor.removeClass('panel-warning panel-success panel-danger');
    $contenedor.addClass(color);

}

function toggleActivar(entidad, accion, el) {
    var $_el = el;
    var _accion = accion;
    var _id = $_el.data("id");
    $.ajax({
        'url': "/acl/acl_" + entidad + "/cambiar_activo",
        'type': 'POST',
        'data': {
            'id': _id,
            'accion': _accion,
            'ajax': 1,
        },
        'success': function (respuesta) {
            if (respuesta === 'ok') {
                if (_accion === 'activar') {
                    $_el.removeClass('btn-danger');
                    $_el.addClass('btn-success');
                    $_el.html("SI");
                    $_el.data("accion", "desactivar");
                }
                if (_accion === 'desactivar') {
                    $_el.removeClass('btn-success');
                    $_el.addClass('btn-danger');
                    $_el.html("NO");
                    $_el.data("accion", "activar");
                }
            } else {
                alert(respuesta);
            }
        }
    })
}

function seleccionarTodoPermitido($el) {
    var class_activo = 'seleccionado';
    var controlador = $el.data('controlador');
    var selector_checkbox = '.js-radio-whitelist-' + controlador;
    if ($el.hasClass(class_activo)) {
        $el.removeClass(class_activo);
        toggleSeleccionarTodo(selector_checkbox, false);
    } else {
        $el.addClass(class_activo);
        toggleSeleccionarTodo(selector_checkbox, true);
        $('.js-requerir-todo[data-controlador=' + controlador + ']').removeClass('seleccionado');
    }
}

function seleccionarTodoRequerido($el) {
    var class_activo = 'seleccionado';
    var controlador = $el.data('controlador');
    var selector_checkbox = '.js-radio-blacklist-' + controlador;
    if ($el.hasClass(class_activo)) {
        $el.removeClass(class_activo);
        toggleSeleccionarTodo(selector_checkbox, false);
    } else {
        $el.addClass(class_activo);
        toggleSeleccionarTodo(selector_checkbox, true);
        $('.js-permitir-todo[data-controlador=' + controlador + ']').removeClass('seleccionado');
    }
}

function toggleSeleccionarTodo(selector, seleccionado) {
    var checkBoxes = $(selector);
    checkBoxes.each(function () {
        $(this).prop("checked", seleccionado);
    });
}

