<?php

/**
 * 09/11/2014
 * File: acl_permisos.php
 * Encoding: ISO-8859-1
 * Project: acl
 * Description of acl_permisos
 * @moduloPermiso ACL - Permisos
 * @author Diego Olmedo
 */
class Acl_permisos extends CI_Controller
{

    const TABLA_PERMISO = "acl_permiso";
    const PK_PERMISO = "id_acl_permiso";
    const RUTA_LISTADO = "/acl/acl_permisos/listar";
    const RUTA_CONTROLADOR = "/acl/acl_permisos";

    public function __construct()
    {
        parent::__construct();
        $this->load->library("acl/acl");
        $this->load->helper(array("url", "acl"));
        $this->load->model("acl/acl_permisos_model", "model");
    }

    public function index()
    {
        redirect(self::RUTA_LISTADO);
    }

    public function listar($respuesta = "")
    {
        $dataIndex = array();
        $dataIndex["permisos"] = $this->acl->get_permisos();

        $dataLayout = array();
        $dataLayout["contenido"] = $this->load->view("acl/permisos/permisos_acl", $dataIndex, TRUE);
        $dataLayout["js_agregado"] = "/assets/acl/js/permisos.js";
        $this->load->view("acl/layout_acl", $dataLayout);
    }

    public function custom($iIdPermiso = 0)
    {
        $id_permiso = (int) $iIdPermiso;
        $dataIndex = array();
        $dataIndex["id_permiso"] = $id_permiso;
        $dataIndex["data"] = array();
        if ($id_permiso > 0) {
            if ($this->model->es_permiso_custom($id_permiso) === FALSE) {
                redirect(self::RUTA_LISTADO);
            }
            $dataIndex["data"] = $this->model->get_data_custom($id_permiso);
        }

        $dataLayout = array();
        $dataLayout["contenido"] = $this->load->view("acl/permisos/form_pemiso_custom", $dataIndex, TRUE);
        $this->load->view("acl/layout_acl", $dataLayout);
    }

    public function guardar_custom()
    {
        if ($this->input->post("guardar")) {
            $id_permiso = (int) $this->input->post("id_permiso");
            $values = array();
            $values["identificador"] = $this->input->post("identificador");
            $values["descripcion"] = $this->input->post("descripcion");
            $values["whitelist"] = (int) $this->input->post("blacklist") === 0 ? 1 : 0;
            $values["blacklist"] = (int) $this->input->post("blacklist");
            if ($id_permiso > 0) {
                $this->model->actualizar($id_permiso, $values);
            } else {
                $values["controlador"] = "acl_custom";
                $values["accion"] = "acl_custom";
                $id_permiso = $this->model->insertar($values);
            }
            if ($id_permiso > 0) {
                redirect(self::RUTA_CONTROLADOR . "/custom/{$id_permiso}/?respuesta=ok");
            }
            redirect(self::RUTA_CONTROLADOR . "/custom/{$id_permiso}/?respuesta=error");
        }
        redirect(self::RUTA_CONTROLADOR . "/custom/{$id_permiso}");
    }

    public function guardar()
    {
        $respuesta = "error";
        //echo "<hr/><pre>";print_r($this->input->post());echo "</pre><hr/>";die();
        if ($this->input->post("guardar")) {
            $permisos = $this->input->post("blacklist");
            $controladores = $this->input->post("controladores");
            $this->_guardar_descripcion_controladores($controladores);
            if ( ! empty($permisos)) {
                $this->_procesar_permisos($permisos);
                $respuesta = "ok";
            }
        }
        redirect(self::RUTA_LISTADO . "/$respuesta");
    }

    private function _guardar_descripcion_controladores($aDescripciones)
    {
        $descripciones = (array) $aDescripciones;
        $this->db->truncate("acl_modulo_permiso");
        foreach ($descripciones as $controlador => $descripcion) {
            $values = array("controlador" => $controlador, "descripcion" => $descripcion);
            $this->db->insert("acl_modulo_permiso", $values);
        }
    }

    /**
     *
     * @param type $aPermisos
     */
    private function _procesar_permisos($aPermisos)
    {
        $permisos = (array) $aPermisos;
        //@TODO: evaluar si conviene insertar tambi�n los controladores/acciones que no fueron seteados ni por req ni por whitelist
        $descripciones = $this->input->post("descripcion");
        //Pongo en activo = 0, todos los permisos
        $this->model->desactivar_permisos();
        foreach ($permisos as $identificador => $blacklist) {
            //Luego, los permisos que est�n vigentes updatean el campo activo = 1
            $descripcion = isset($descripciones[$identificador]) ? $descripciones[$identificador] : "";
            if ($this->model->permiso_existente($identificador) === TRUE) {
                $this->_actualizar_permiso($descripcion, $identificador, $blacklist);
            } else {
                $this->_insertar_permiso($descripcion, $identificador, $blacklist);
            }
        }
        //Finalmente, los que quedaron con activo = 0. Los borro porque significa que no se usan m�s.
        $this->model->borrar_permisos_obsoletos();
    }

    private function _actualizar_permiso($sDescripcion, $sIdentificador, $sBlacklist)
    {
        $blacklist = (string) $sBlacklist;
        $value = array();
        $value["activo"] = 1;
        $value["descripcion"] = $sDescripcion;
        $value["blacklist"] = $blacklist === "0" ? 0 : 1;
        $value["whitelist"] = $blacklist === "0" ? 1 : 0;
        $this->model->actualizar($sIdentificador, $value);
    }

    private function _insertar_permiso($sDescripcion, $sIdentificador, $sBlacklist)
    {
        $identificador = (string) $sIdentificador;
        $blacklist = (string) $sBlacklist;
        $datos_permiso = desarmar_identificador_permiso($identificador);
        if ($datos_permiso !== NULL) {
            $value = array();
            $value["activo"] = 1;
            $value["descripcion"] = $sDescripcion;
            $value["identificador"] = $identificador;
            $value["controlador"] = $datos_permiso["controlador"];
            $value["accion"] = $datos_permiso["accion"];
            $value["blacklist"] = $blacklist === "0" ? 0 : 1;
            $value["whitelist"] = $blacklist === "0" ? 1 : 0;
            $this->model->insertar($value);
        }
    }

}
