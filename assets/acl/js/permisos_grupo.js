$(document).ready(function () {

    $('.js-contenedor-controlador').on('click', 'input[type=checkbox]', function () {
        var $contenedor = $(this).parents('.js-contenedor-controlador');
        actualizarStatusControlador($contenedor);
    });
    $('.seleccionar-todos-permitido').on('click.permitirTodo', function () {
        var $this = $(this);
        var controlador = $this.data("controlador");
        var checkBoxes = $(".js-checkbox-permitido-" + controlador);
        var cant_checked = $(".js-checkbox-permitido-" + controlador + ":checked").length;
        var check_value;

        if (cant_checked === checkBoxes.length) {
            check_value = false;
        } else {
            check_value = true;
        }
        checkBoxes.each(function () {
            $(this).prop("checked", check_value);
        });
        var $contenedor = $this.parents('.js-contenedor-controlador');
        actualizarStatusControlador($contenedor);
    });
    actualizarStatusControladores();
})

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
    var color = 'panel-primary';
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