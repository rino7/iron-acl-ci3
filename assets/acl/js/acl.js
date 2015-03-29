$(document).ready(function () {
    $('.js-tooltip').tooltip();
    $('.js-alert').alert();

    //@TODO: unificar las clases para que el mismo método sirva tanto para checbox como para radios.


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
   // actualizarStatusControladores();
});



/**
 * Activa o desactiva un registro de la db
 * @param {type} entidad
 * @param {type} accion
 * @param {type} el
 * @returns {undefined}
 */
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

//function toggleSeleccionarTodo(selector, seleccionado) {
//    var inputs = $(selector);
//    inputs.each(function () {
//        $(this).prop("checked", seleccionado);
//    });
//}

