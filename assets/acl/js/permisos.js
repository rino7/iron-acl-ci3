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
    $('.js-publicar-todo').on('click', function () {
        var $this = $(this);
        var $contenedor = $this.parents('.js-contenedor-controlador');
        seleccionarTodoPublico($this);
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
    var cant_radios = $contenedor.find("input[type=radio]").length / 3; //Divido x 3 porque hay 2 opciones por acción
    var cant_checked_requerido = $contenedor.find("input[type=radio].js-radio-blacklist:checked").length;
    var cant_checked_permitido = $contenedor.find("input[type=radio].js-radio-whitelist:checked").length;
    var cant_checked_publico = $contenedor.find("input[type=radio].js-radio-publico:checked").length;
    var color = 'panel-info';
    if (cant_radios > 0) {
        if (cant_checked_requerido === cant_radios) {
            color = 'panel-danger';
        } else if (cant_checked_permitido === cant_radios) {
            color = 'panel-success';
        } else if (cant_checked_publico > 0 === cant_radios) {
            color = 'panel-primary';
        } else if (cant_checked_permitido > 0 || cant_checked_requerido > 0) {
            color = 'panel-warning';
        }
    }
    $contenedor.removeClass('panel-warning panel-success panel-danger panel-info panel-primary');
    $contenedor.addClass(color);

}

function seleccionarTodoPermitido($el) {
    var controlador = $el.data('controlador');
    var selector_checkbox = '.js-radio-whitelist-' + controlador;
    var selector_checkbox_opuesto = '.js-radio-blacklist-' + controlador;
    var cant_checkboxs = $(selector_checkbox).length;
    var cant_checked = $('.js-radio-whitelist-' + controlador + ':checked').length;
    $(selector_checkbox_opuesto).each(function () {
        $(this).prop("checked", false);
    });
    if (cant_checked === cant_checkboxs) {
        toggleSeleccionarTodo(selector_checkbox, false);
    } else {
        toggleSeleccionarTodo(selector_checkbox, true);
    }
}

function seleccionarTodoRequerido($el) {
    var controlador = $el.data('controlador');
    var selector_checkbox = '.js-radio-blacklist-' + controlador;
    var selector_checkbox_opuesto = '.js-radio-whitelist-' + controlador;
    var cant_checkboxs = $(selector_checkbox).length;
    var cant_checked = $('.js-radio-blacklist-' + controlador + ':checked').length;
    $(selector_checkbox_opuesto).each(function () {
        $(this).prop("checked", false);
    });
    if (cant_checked === cant_checkboxs) {
        toggleSeleccionarTodo(selector_checkbox, false);
    } else {
        toggleSeleccionarTodo(selector_checkbox, true);
    }
}

function seleccionarTodoPublico($el) {
    var controlador = $el.data('controlador');
    var selector_checkbox = '.js-radio-publico-' + controlador;
    var selector_checkbox_opuesto = '.js-radio-whitelist-' + controlador;
    var cant_checkboxs = $(selector_checkbox).length;
    var cant_checked = $('.js-radio-publico-' + controlador + ':checked').length;
    $(selector_checkbox_opuesto).each(function () {
        $(this).prop("checked", false);
    });
    if (cant_checked === cant_checkboxs) {
        toggleSeleccionarTodo(selector_checkbox, false);
    } else {
        toggleSeleccionarTodo(selector_checkbox, true);
    }
}


function toggleSeleccionarTodo(selector, seleccionado) {
    var inputs = $(selector);
    inputs.each(function () {
        $(this).prop("checked", seleccionado);
    });
}