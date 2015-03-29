<?php

/**
 * 09/11/2014
 * File: acl_usuarios.php
 * Encoding: ISO-8859-1
 * Project: acl
 * Description of acl_usuarios
 * @moduloPermiso ACL - Usuarios
 * @author Diego Olmedo
 */
class Acl_usuarios extends CI_Controller
{

    const RPP = 10;
    const PAG_SEGMENT = 6;
    const TABLA_USUARIO = "acl_usuario";
    const TABLA_USUARIO_PERMISO = "acl_usuario_permiso";
    const TABLA_USUARIO_GRUPO = "acl_usuario_grupo";
    const RUTA_CONTROLADOR = "/acl/acl_usuarios";
    const RUTA_LISTADO = "/acl/acl_usuarios/listar";

    public function __construct()
    {
        parent::__construct();
        $this->load->library(array("acl/acl", "pagination"));
        $this->load->helper(array("url", "acl"));
        $this->load->model("acl/acl_usuarios_model", "acl_usuarios_model");
    }

    public function index()
    {
        redirect(self::RUTA_LISTADO);
    }

    public function listar($sOrderBy = "numero", $sSentido = "desc")
    {
        $order_by = strtolower($sOrderBy);
        $sentido = strtolower($sSentido) === "desc" ? "asc" : "desc";
        $total_registros = $this->acl_usuarios_model->count_all();
        $pag = $this->uri->segment(self::PAG_SEGMENT);
        $offset = (($pag > 0 ? $pag : 1 ) * self::RPP) - self::RPP;

        $dataPagina = array();
        $dataPagina["usuarios"] = $this->acl_usuarios_model->get_all(self::RPP, $offset, $sOrderBy, $sSentido);
        $dataPagina["order_by"] = $order_by;
        $dataPagina["sentido"] = $sentido;
        $dataPagina["total_registros"] = $total_registros;
        $dataPagina["respuesta"] = $this->_mostrar_mensaje_respuesta($this->input->get("respuesta"));
        init_pagination_acl(self::RUTA_LISTADO . "/{$sOrderBy}/$sSentido/", $total_registros, self::RPP, self::PAG_SEGMENT);
        $dataPagina["paginador"] = $this->pagination->create_links();


        $dataLayout = array();
        $dataLayout["contenido"] = $this->load->view("acl/usuarios/usuarios_acl", $dataPagina, TRUE);
        $this->load->view("acl/layout_acl", $dataLayout);
    }

    public function nuevo()
    {
        $dataPagina = array();
        $dataPagina["id_usuario"] = 0;
        $dataPagina["data"] = $this->session->flashdata("postdata");
        $dataLayout = array();
        $dataLayout["contenido"] = $this->load->view("acl/usuarios/form_usuario_acl", $dataPagina, TRUE);
        $this->load->view("acl/layout_acl", $dataLayout);
    }

    public function editar($iIdUsuario)
    {
        $id_usuario = (int) $iIdUsuario;
        if ($id_usuario === 0) {
            redirect("/acl/acl_usuarios/nuevo");
        }
        $dataPagina = array();
        $dataPagina["id_usuario"] = $id_usuario;
        $data_usuario = $this->acl_usuarios_model->buscar_por_id($id_usuario, FALSE);
        if ($this->session->flashdata("postdata")) {
            $data_usuario = $this->session->flashdata("postdata");
        }
        $dataPagina["data"] = $data_usuario;
        $dataLayout = array();
        $dataLayout["contenido"] = $this->load->view("acl/usuarios/form_usuario_acl", $dataPagina, TRUE);
        $this->load->view("acl/layout_acl", $dataLayout);
    }

    public function guardar()
    {
        if ($this->input->post("guardar")) {
            $id_usuario = (int) $this->input->post("id_usuario");
            $usuario = $this->input->post("usuario");
            $values = array(
                "nombre" => $this->input->post("nombre"),
                "apellido" => $this->input->post("apellido"),
                "email" => $this->input->post("email"),
                "usuario" => $usuario,
                "activo" => $this->input->post("activo"),
            );
            $usuario_existente = $this->acl_usuarios_model->usuario_existente($usuario, $id_usuario);
            if ($usuario_existente === TRUE) {
                $this->session->set_flashdata("postdata", $this->input->post());
                if ($id_usuario > 0) {
                    redirect("/acl/acl_usuarios/editar/{$id_usuario}/?error=usuario_existente");
                } else {
                    redirect("/acl/acl_usuarios/nuevo/?error=usuario_existente");
                }
            }
            if ($id_usuario > 0) {
                $ok = $this->acl_usuarios_model->actualizar($id_usuario, $values);
            } else {

                if (TRUE === $this->_contrasenia_valida($this->input->post("contrasenia"), $this->input->post("repite_contrasenia"))) {
                    $values["contrasenia"] = $this->_encriptar_contrasenia($this->input->post("contrasenia"));
                    $id_usuario = $this->acl_usuarios_model->insertar($values);
                    $ok = $id_usuario > 0;
                } else {
                    redirect("/acl/acl_usuarios/editar/{$id_usuario}/contrasenia_invalida");
                }
            }
            if ($ok === TRUE) {
                $this->_redireccionar_post_guardado($id_usuario);
            }
        }
        $this->_redireccionar_post_guardado($id_usuario);
    }

    private function _redireccionar_post_guardado($id_usuario)
    {
        if (tiene_permiso("acl_usuarios/editar")) {
            redirect("/acl/acl_usuarios/editar/{$id_usuario}/ok");
        }
        if (tiene_permiso("acl_usuarios/listar")) {
            redirect(self::RUTA_LISTADO);
        }
        if (tiene_permiso("acl_usuarios/nuevo")) {
            redirect("/acl/acl_usuarios/nuevo");
        }
    }

    public function permisos_usuario($iIdUsuario = 0)
    {
        $this->load->library("Acl/Acl_procesador_permisos", NULL, "procesador_permisos");
        $this->load->model("Acl/Acl_permisos_model", "acl_permisos_model");
        $id_usuario = (int) $iIdUsuario;
        $dataPagina = array();
        $permisos_de_usuario = $this->procesador_permisos->get_permisos_de_usuario($id_usuario);

        $dataPagina["permisos"] = $this->procesador_permisos->get_permisos_requeridos(TRUE);
        $dataPagina["permisos_de_usuario"] = $permisos_de_usuario;
        $dataPagina["id_usuario"] = $id_usuario;
        $dataPagina["nombre_usuario"] = get_nombre_usuario($id_usuario, "COMPLETO");

        $dataLayout = array();
        $dataLayout["contenido"] = $this->load->view("acl/usuarios/asignar_permisos_usuario", $dataPagina, TRUE);
        $dataLayout["js_agregado"] = "/assets/acl/js/permisos_usuario.js";
        $this->load->view("acl/layout_acl", $dataLayout);
    }

    public function grupos_usuario($iIdUsuario = 0, $sOrderBy = "asignado", $sSentido = "desc")
    {
        $order_by = strtolower($sOrderBy);
        $sentido = strtolower($sSentido) === "desc" ? "asc" : "desc";
//        $total_registros = $this->acl_usuarios_model->count_all();
//        $pag = $this->uri->segment(self::PAG_SEGMENT);
//        $offset = (($pag > 0 ? $pag : 1 ) * self::RPP) - self::RPP;

        $id_usuario = (int) $iIdUsuario;
        $dataPagina = array();
        $dataPagina["grupos"] = $this->acl_usuarios_model->get_grupos_por_usuario($id_usuario, $order_by, $sSentido);
        $dataPagina["id_usuario"] = $id_usuario;
        $dataPagina["order_by"] = $order_by;
        $dataPagina["sentido"] = $sentido;
        $dataPagina["nombre_usuario"] = get_nombre_usuario($id_usuario, "COMPLETO");

        $dataLayout = array();
        $dataLayout["contenido"] = $this->load->view("acl/usuarios/asignar_grupos_usuario", $dataPagina, TRUE);
        $this->load->view("acl/layout_acl", $dataLayout);
    }

    public function guardar_grupos_usuario()
    {
        $respuesta = "error";
        //echo "<hr/><pre>";print_r($this->input->post());echo "</pre><hr/>";die();
        $id_usuario = (int) $this->input->post("id_usuario");
        if ($this->input->post("guardar")) {
            if ($id_usuario === 0) {
                return FALSE;
            }
            $grupos = $this->input->post("grupos");
            $values = $this->_procesar_grupos_usuario($id_usuario, $grupos);
            if ( ! empty($values)) {
                $this->db->delete(self::TABLA_USUARIO_GRUPO, array("fk_acl_usuario" => $id_usuario));
                $this->db->insert_batch(self::TABLA_USUARIO_GRUPO, $values);
                $respuesta = "ok";
            }
        }
        redirect("/acl/acl_usuarios/grupos_usuario/$id_usuario/{$respuesta}");
    }

    public function guardar_permisos_usuario()
    {
        $respuesta = "error";
        $id_usuario = (int) $this->input->post("id_usuario");
        if ($this->input->post("guardar")) {
            if ($id_usuario === 0) {
                return FALSE;
            }
            $permitidos = $this->input->post("permitido");
            $values = $this->_procesar_permisos_usuario($id_usuario, $permitidos);
            if ( ! empty($values)) {
                $this->db->delete(self::TABLA_USUARIO_PERMISO, array("fk_acl_usuario" => $id_usuario));
                $this->db->insert_batch(self::TABLA_USUARIO_PERMISO, $values);
                $respuesta = "ok";
            }
        }
        redirect("/acl/acl_usuarios/permisos_usuario/$id_usuario/{$respuesta}");
    }

    public function cambiar_activo()
    {
        //@TODO: validar permiso de cambiar activo
        if ($this->input->post("ajax")) {
            $accion = $this->input->post("accion");
            $id_grupo = (int) $this->input->post("id");
            $value = strtolower($accion) === "desactivar" ? "N" : "S";
            $ok = $this->acl_usuarios_model->cambiar_activo($value, $id_grupo);
            if ($ok === TRUE) {
                echo "ok";
                die;
            }
            echo "ERROR: No se pudo actualizar el estado";
            die;
        }
    }

    public function eliminar_usuarios()
    {
        if ($this->input->post("eliminar")) {
            $ids_usuarios = $this->input->post("usuarios");
            $ok = $this->acl_usuarios_model->eliminar_usuarios($ids_usuarios);
            if ($ok === TRUE) {
                redirect(self::RUTA_LISTADO . "/?respuesta=eliminados_ok");
            }
            redirect(self::RUTA_LISTADO . "/?respuesta=error_db");
        }
        redirect(self::RUTA_LISTADO);
    }

    public function cambiar_contrasenia($iIdUsuario = 0)
    {
        if ($this->input->post("guardar")) {
            if (TRUE === $this->_contrasenia_valida($this->input->post("contrasenia"), $this->input->post("repite_contrasenia"))) {
                $id_usuario = (int) $this->input->post("id_usuario");
                $nueva_contrasenia = $this->_encriptar_contrasenia($this->input->post("contrasenia"));
                $ok = $this->acl_usuarios_model->cambiar_contrasenia($nueva_contrasenia, $id_usuario);
                if ($ok === TRUE) {
                    redirect("/acl/acl_usuarios/cambiar_contrasenia/{$id_usuario}/?respuesta=ok");
                } else {
                    redirect("/acl/acl_usuarios/cambiar_contrasenia/{$id_usuario}/?respuesta=error_db");
                }
            } else {
                redirect("/acl/acl_usuarios/cambiar_contrasenia/{$id_usuario}/?respuesta=contrasenia_invalida");
            }
        }

        $id_usuario = (int) $iIdUsuario;
        $dataPagina = array();
        $dataPagina["permisos"] = $this->acl_usuarios_model->get_permisos_personales_por_usuario($id_usuario);
        $dataPagina["id_usuario"] = $id_usuario;
        $dataPagina["nombre_usuario"] = get_nombre_usuario($id_usuario, "COMPLETO");

        $dataLayout = array();
        $dataLayout["contenido"] = $this->load->view("acl/usuarios/cambiar_contrasenia", $dataPagina, TRUE);
        $this->load->view("acl/layout_acl", $dataLayout);
    }

    /**
     * Devuevle los permisos personales asignados a un usuario
     * @param int $iIdUsuario
     * @return array
     */
    private function _get_permisos_seteados_por_usuario($iIdUsuario)
    {
        $id_usuario = (int) $iIdUsuario;
        $this->db->where("fk_acl_usuario", $id_usuario);
        $rows = $this->db->get('acl_usuario_permiso')->result_array();

        $permisos_de_grupo = $this->_get_permisos_de_grupo_heredados_por_usuario($id_usuario);
    }

    /**
     * Estable la prioridad denegado > permitido en todos los permisos seteados a un usuario.
     * Es decir que si un usuario tiene un permiso xxx otorgado por un grupo pero denegado por otro.
     * El permiso xxx estará denegado para ese usuario.
     * @param array $aPermisosSeteadosPorUsuario Los permisos que tiene un usuario
     * @return array Los permisos de un usuario luego de establecida la priorida denegado > otorgado
     */
    private function _procesar_permisos_por_usuario($aPermisosSeteadosPorUsuario)
    {
        $permisos_requeridos = $this->_get_permisos_seteados(TRUE);
        $permisos_por_usuario = (array) $aPermisosSeteadosPorUsuario;
        $mis_permisos = array();
        $index = 0;

        foreach ($permisos_requeridos as &$permiso) {
            $permiso["permitido"] = NULL;
            $permiso["denegado"] = NULL;
            if (isset($permisos_por_usuario[$permiso["id_acl_permiso"]])) {
                $tipo_permiso = (int) $permisos_por_usuario[$permiso["id_acl_permiso"]]["tipo_permiso"];
                $permiso["permitido"] = $tipo_permiso === 1 ? 1 : 0;
                $permiso["denegado"] = $tipo_permiso === 0 ? 1 : 0;
            }
            $nombre_clase = mb_strtolower($permiso["controlador"]);
            if ( ! isset($mis_permisos[$nombre_clase])) {
                $mis_permisos[$nombre_clase] = array();
            }
            array_push($mis_permisos[$nombre_clase], $permiso);
            $index ++;
        }
        return $mis_permisos;
    }

    /**
     * Encripta la contrase�a con crypy y el salt Blowfish
     * Idea tomada del siguiente link
     * @link http://alias.io/2010/01/store-passwords-safely-with-php-and-mysql/
     * PDF en proyecto plataforma_multiservicios:
     * @link \plataforma_multiservicios\documentacion\EncriptacionPasswordsUsada.pdf
     * @param string $password La contrase�a a encriptar
     * @return string La contrase�a encriptada
     */
    private function _encriptar_contrasenia($password)
    {
        $cost = 10;
        //Random salt
        $salt = strtr(base64_encode(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM)), '+', '.');
        //"$2a$" salt especial, que crypt reconoce para codificar con el alogaritmo Blowfish
        $salt = sprintf("$2a$%02d$", $cost) . $salt;

        $hash = crypt($password, $salt);

        return $hash;
    }

    private function _procesar_permisos_usuario($iIdUsuario, $aPermisos)
    {
        $id_usuario = (int) $iIdUsuario;
        $permisos = (array) $aPermisos;
        $values = array();
        //@TODO: evaluar si conviene insertar tambi�n los controladores/acciones que no fueron seteados ni por req ni por whitelist
        foreach ($permisos as $id_permiso => $permitido) {
            $value = array();
            $value["fk_acl_usuario"] = $id_usuario;
            $value["fk_acl_permiso"] = $id_permiso;
            $value["tipo_permiso"] = (int) $permitido;
            array_push($values, $value);
        }
        return $values;
    }

    private function _procesar_grupos_usuario($iIdUsuario, $aGrupos)
    {
        $id_usuario = (int) $iIdUsuario;
        $grupos = (array) $aGrupos;
        $values = array();
        foreach ($grupos as $id_grupo => $asignado) {
            $value = array();
            $value["fk_acl_usuario"] = $id_usuario;
            $value["fk_acl_grupo"] = $id_grupo;
            array_push($values, $value);
        }
        return $values;
    }

    // PRIVATE
    private function _mostrar_mensaje_respuesta($sRespuesta)
    {
        if ( ! empty($sRespuesta)) {
            $respuesta = strtolower($sRespuesta);
            $mensaje = "Error desconocido";
            $class = "danger";
            if ($respuesta === "error_db") {
                $mensaje = "Los cambios no pudieron ser guardados. Intentelo nuevamente";
                $class = "danger";
            }
            if ($respuesta === "eliminados_ok") {
                $mensaje = "Los usuarios fueron eliminados correctamente";
                $class = "success";
            }
            return array(
                "mensaje" => $mensaje,
                "class" => $class,
            );
        }
        return array();
    }

    private function _contrasenia_valida($sContrasenia, $sRepiteContrasenia)
    {
        $contrasenia = (string) $sContrasenia;
        $repite_contrasenia = (string) $sRepiteContrasenia;
        if (empty($contrasenia)) {
            return FALSE;
        }
        return $contrasenia === $repite_contrasenia;
    }

}
