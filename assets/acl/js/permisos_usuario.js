$(document).ready(function () {

    $('.js-contenedor-controlador').on('click', 'input[type=radio]', function () {
        var $contenedor = $(this).parents('.js-contenedor-controlador');
        actualizarStatusControlador($contenedor);
    });

    $('.js-permitir-todo').on('click.permitirTodo', function () {
        var $this = $(this);
        var $contenedor = $this.parents('.js-contenedor-controlador');
        seleccionarTodoPermitido($this);
        actualizarStatusControlador($contenedor);
    });

    $('.js-requerir-todo').on('click.requerirTodo', function () {
        var $this = $(this);
        var $contenedor = $this.parents('.js-contenedor-controlador');
        seleccionarTodoRequerido($this);

        actualizarStatusControlador($contenedor);
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
    var cant_radios = $contenedor.find("input[type=radio]").length / 2; //Divido x 2 porque hay 2 opciones por acción
    var cant_checked_requerido = $contenedor.find(".js-icon-blacklist.show").length;
    var cant_checked_permitido = $contenedor.find(".js-icon-whitelist.show").length;
    var color = 'panel-info';
    if (cant_radios > 0) {
        if (cant_checked_requerido === cant_radios) {
            color = 'panel-danger';
        } else if (cant_checked_permitido === cant_radios) {
            color = 'panel-success';
        } else if (cant_checked_permitido > 0 || cant_checked_requerido > 0) {
            color = 'panel-warning';
        }
    }
    $contenedor.removeClass('panel-warning panel-success panel-danger panel-info');
    $contenedor.addClass(color);

}

function seleccionarTodoPermitido($el) {
    var controlador = $el.data('controlador');
    var selector_input = '.js-radio-whitelist-' + controlador;
    var selector_input_opuesto = '.js-radio-blacklist-' + controlador;
    var cant_checkboxs = $(selector_input).length;
    var cant_checked = $('.js-radio-whitelist-' + controlador + ':checked').length;
    $(selector_input_opuesto).each(function () {
        $(this).prop("checked", false);
    });
    if (cant_checked === cant_checkboxs) {
        toggleSeleccionarTodo(selector_input, false);
    } else {
        toggleSeleccionarTodo(selector_input, true);

    }
}

function seleccionarTodoRequerido($el) {
    var controlador = $el.data('controlador');
    var selector_input = '.js-radio-blacklist-' + controlador;
    var selector_input_opuesto = '.js-radio-whitelist-' + controlador;
    var cant_checkboxs = $(selector_input).length;
    var cant_checked = $('.js-radio-blacklist-' + controlador + ':checked').length;
    $(selector_input_opuesto).each(function () {
        $(this).prop("checked", false);
    });
    if (cant_checked === cant_checkboxs) {
        toggleSeleccionarTodo(selector_input, false);
    } else {
        toggleSeleccionarTodo(selector_input, true);
    }
}

function toggleSeleccionarTodo(selector, seleccionado) {
    var inputs = $(selector);
    inputs.each(function () {
        $(this).prop("checked", seleccionado);
    });
}

