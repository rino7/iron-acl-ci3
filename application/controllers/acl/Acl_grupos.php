<?php

/**
 * 09/11/2014
 * File: acl_grupos.php
 * Encoding: ISO-8859-1
 * Project: acl
 * Description of acl_grupos
 *
 * @moduloPermiso ACL - Grupos
 * @author Diego Olmedo
 */
class Acl_grupos extends CI_Controller
{

    const RPP = 15;
    const PAG_SEGMENT = 6;
    const RUTA_CONTROLADOR = "/acl/acl_grupos";
    const RUTA_LISTADO = "/acl/acl_grupos/listar";

    public function __construct()
    {
        parent::__construct();
        $this->load->library(array("acl/acl", "pagination"));
        $this->load->helper(array("url", "acl"));
        $this->load->model("acl/acl_grupos_model", "model");
    }

    /**
     * @whitelist
     * @permiso Index
     */
    public function index()
    {
        redirect("acl/acl_grupos/listar");
    }

    /**
     * @permiso Cambiar el estado del grupo
     */
    public function cambiar_activo()
    {
        //@TODO: validar permiso de cambiar activo
        if ($this->input->post("ajax")) {
            $accion = $this->input->post("accion");
            $id_grupo = (int) $this->input->post("id");
            $value = strtolower($accion) === "desactivar" ? "N" : "S";
            $ok = $this->model->cambiar_activo($value, $id_grupo);
            if ($ok === TRUE) {
                echo "ok";
                die;
            }
            echo "ERROR: No se pudo actualizar el estado";
            die;
        }
    }

    /**
     * @permiso Mostrar todos los grupos
     */
    public function listar($sOrderBy = "numero", $sSentido = "desc")
    {
        $order_by = strtolower($sOrderBy);
        $sentido = strtolower($sSentido) === "desc" ? "asc" : "desc";
        $total_registros = $this->model->count_all();
        $pag = $this->uri->segment(self::PAG_SEGMENT);
        $offset = (($pag > 0 ? $pag : 1 ) * self::RPP) - self::RPP;
        $dataPagina = array();
        $dataPagina["grupos"] = $this->model->get_all(self::RPP, $offset, $sOrderBy, $sSentido);
        $dataPagina["order_by"] = strtolower($order_by);
        $dataPagina["sentido"] = strtolower($sentido);
        $dataPagina["total_registros"] = $total_registros;
        $dataPagina["respuesta"] = $this->_mostrar_mensaje_respuesta($this->input->get("respuesta"));
        init_pagination_acl(self::RUTA_CONTROLADOR . "/listar/{$order_by}/$sSentido/", $total_registros, self::RPP, self::PAG_SEGMENT);
        $dataPagina["paginador"] = $this->pagination->create_links();
        $dataLayout = array();
        $dataLayout["contenido"] = $this->load->view("acl/grupos/grupos_acl", $dataPagina, TRUE);
        $this->load->view("acl/layout_acl", $dataLayout);
    }

    public function guardar()
    {
        $post = $this->input->post();
        $puede_crear = tiene_permiso("grupos/crear");
        $puede_editar = tiene_permiso("grupos/editar");
        if ($post["guardar"]) {
            $id_grupo = (int) $post["id_grupo"];
            if (empty($post["nombre"])) {
                redirect(self::RUTA_CONTROLADOR . "/listar/?respuesta=nombre_vacio");
            }
            if (TRUE === $this->model->grupo_existente($post["nombre"], $id_grupo)) {
                redirect(self::RUTA_CONTROLADOR . "/listar/?respuesta=duplicado");
            }

            $values = array("nombre" => $post["nombre"]);
            $ok = FALSE;
            //@TODO: validar permiso de guardar nuevo/editar
            if ($id_grupo > 0) {
                if ($puede_editar === FALSE) {
                    show_error_permiso();
                }
                $ok = $this->model->actualizar($values, $id_grupo);
            } else {
                if ($puede_crear === FALSE) {
                    show_error_permiso();
                }
                $ok = $this->model->insertar($values) > 0;
            }
            if ($ok === TRUE) {
                redirect(self::RUTA_CONTROLADOR . "/listar/?respuesta=ok");
            }
            redirect(self::RUTA_CONTROLADOR . "/listar/?respuesta=error_db");
        }
    }

    public function permisos_grupo($iIdGrupo = 0)
    {
        $id_grupo = (int) $iIdGrupo;

        $dataPagina = array();
        $dataPagina["permisos"] = $this->model->get_permisos_por_grupo($id_grupo);
        $dataPagina["id_grupo"] = $id_grupo;
        $dataPagina["nombre_grupo"] = get_nombre_grupo($id_grupo);

        $dataLayout = array();
        $dataLayout["contenido"] = $this->load->view("acl/grupos/asignar_permisos_grupo", $dataPagina, TRUE);
        $dataLayout["js_agregado"] = "/assets/acl/js/permisos_grupo.js";
        $this->load->view("acl/layout_acl", $dataLayout);
    }

    public function guardar_permisos_grupo()
    {
        $respuesta = "error";
        //echo "<hr/><pre>";print_r($this->input->post());echo "</pre><hr/>";die();
        $id_grupo = (int) $this->input->post("id_grupo");
        if ($this->input->post("guardar")) {
            if ($id_grupo === 0) {
                return FALSE;
            }
            $permitidos = $this->input->post("permitido");
            $values = $this->_procesar_permisos_grupo($id_grupo, $permitidos);
            if ( ! empty($values)) {
                $this->model->guardar_permisos($values, $id_grupo);
                $respuesta = "ok";
            }
        }
        redirect(self::RUTA_CONTROLADOR . "/permisos_grupo/$id_grupo/{$respuesta}");
    }

    public function asignar_usuarios_grupo($iIdGrupo, $sOrderBy = "numero", $sSentido = "desc")
    {
        $id_grupo = (int) $iIdGrupo;
        if ($id_grupo === 0) {
            return FALSE;
        }
        $order_by = strtolower($sOrderBy);
        $sentido = strtolower($sSentido) === "desc" ? "asc" : "desc";

        $dataPagina = array();
        $dataPagina["id_grupo"] = $id_grupo;
        $dataPagina["order_by"] = $order_by;
        $dataPagina["sentido"] = $sentido;
        $dataPagina["nombre_grupo"] = get_nombre_grupo($id_grupo);
        $dataPagina["usuarios_asignados"] = $this->_get_usuarios_grupo($id_grupo, $order_by, $sSentido);

        $dataLayout = array();
        $dataLayout["contenido"] = $this->load->view("acl/grupos/asignar_usuarios_grupo", $dataPagina, TRUE);
        $this->load->view("acl/layout_acl", $dataLayout);
    }

    public function grupos_usuario($iIdUsuario = 0)
    {
        $id_usuario = (int) $iIdUsuario;
        $dataIndex = array();
        $dataIndex["grupos"] = $this->acl->get_grupos_por_usuario($id_usuario);
        $dataIndex["id_usuario"] = $id_usuario;
        $dataIndex["nombre_usuario"] = get_nombre_usuario($id_usuario, "COMPLETO");
    }

    private function _get_usuarios_grupo($iIdGrupo, $sOrderBy = "numero", $sSentido = "desc")
    {
        $id_grupo = (int) $iIdGrupo;
        return $this->model->get_usuarios_grupo($id_grupo, $sOrderBy, $sSentido);
    }

    public function ajax_buscar_usuario()
    {
        $term = $this->input->get("term");
        $id_grupo = (int) $this->input->get("id_grupo");
        $usuarios = $this->model->buscar_usuario($id_grupo, $term);
        foreach ($usuarios as &$usuario) {
            $usuario["value"] = $usuario["apellido"] . ", " . $usuario["nombre"];
        }
        echo json_encode($usuarios);
        die;
    }

    public function guardar_usuario_grupo()
    {
        $id_grupo = (int) $this->input->post("id_grupo");
        $id_usuario = (int) $this->input->post("id_usuario");
        if ($id_grupo === 0 OR $id_usuario === 0) {
            redirect(self::RUTA_CONTROLADOR . "/asignar_usuarios_grupo/$id_grupo/?respuesta=datos_vacios");
        }
        if ($this->model->guardar_usuario_grupo($id_grupo, $id_usuario) > 0) {
            redirect(self::RUTA_CONTROLADOR . "/asignar_usuarios_grupo/$id_grupo/?respuesta=ok");
        }
    }

    public function desasignar_usuarios_grupo()
    {
        $id_grupo = (int) $this->input->post("id_grupo");
        $ids_usuario = $this->input->post("usuarios");
        if ($id_grupo === 0 OR empty($ids_usuario)) {
            redirect(self::RUTA_CONTROLADOR . "/asignar_usuarios_grupo/$id_grupo/?respuesta=cero_usuarios_seleccionados");
        }
        if ($this->model->desasignar_usuarios_grupo($id_grupo, $ids_usuario) === TRUE) {
            redirect(self::RUTA_CONTROLADOR . "/asignar_usuarios_grupo/$id_grupo/?respuesta=eliminados_ok");
        }
    }

    public function eliminar_grupos()
    {
        if ($this->input->post("eliminar")) {
            $ids_grupos = $this->input->post("grupos");
            $ok = $this->model->eliminar_grupos($ids_grupos);
            if ($ok === TRUE) {
                redirect(self::RUTA_CONTROLADOR . "/listar/?respuesta=eliminados_ok");
            }
            redirect(self::RUTA_CONTROLADOR . "/listar/?respuesta=error_db");
        }
        redirect(self::RUTA_CONTROLADOR . "/listar");
    }

// PRIVATE
    private function _mostrar_mensaje_respuesta($sRespuesta)
    {
        if ( ! empty($sRespuesta)) {
            $respuesta = strtolower($sRespuesta);
            $mensaje = "Error desconocido";
            $class = "danger";
            if ($respuesta === "nombre_vacio") {
                $mensaje = "El nombre del grupo no puede estar vac�o";
                $class = "danger";
            }
            if ($respuesta === "error_db") {
                $mensaje = "Los cambios no pudieron ser guardados. Intentelo nuevamente";
                $class = "danger";
            }
            if ($respuesta === "duplicado") {
                $mensaje = "Ya existe un grupo con el mismo nombre";
                $class = "danger";
            }
            if ($respuesta === "ok") {
                $mensaje = "El grupo fue guardado correctamente";
                $class = "success";
            }
            if ($respuesta === "eliminados_ok") {
                $mensaje = "Los grupos fueron eliminados correctamente";
                $class = "success";
            }
            return array(
                "mensaje" => $mensaje,
                "class" => $class,
            );
        }
        return array();
    }

    private function _procesar_permisos_grupo($iIdGrupo, $aPermisos)
    {
        $id_grupo = (int) $iIdGrupo;
        $permisos = (array) $aPermisos;
        $values = array();
        //@TODO: evaluar si conviene insertar tambi�n los controladores/acciones que no fueron seteados ni por req ni por whitelist
        foreach ($permisos as $id_permiso => $permitido) {
            $value = array();
            $value["fk_acl_grupo"] = $id_grupo;
            $value["fk_acl_permiso"] = $id_permiso;
            array_push($values, $value);
        }
        return $values;
    }

}
