<?php

/**
 * 28/03/2015
 * File: Acl_permisos.php
 * Encoding: UTF-8
 * Project: iron_acl_ci3
 * Description of Acl_permisos
 *
 * @author Diego Olmedo
 */
class Acl_procesador_permisos
{

    private $_permisos_de_grupo;
    private $_permisos_personales;
    private $_permisos_calculados;
    private $_usuarios_model;
    private $_CI;

    const NOMBRE_GRUPO_PERMISO_PERSONAL = "Permiso Personal";
    const ID_GRUPO_PERMISO_PERSONAL = -1;
    const TIPO_PERMISO_OTORGADO = 1;

    public function __construct()
    {
        $this->_CI = & get_instance();
        $this->_CI->load->model("Acl/Acl_usuarios_model", "acl_usuarios_model");
        $this->_usuarios_model = $this->_CI->acl_usuarios_model;
        $this->_permisos_de_grupo = array();
        $this->_permisos_personales = array();
    }

    public function set_permisos_de_grupo($aPermisosGrupo)
    {
        $this->_permisos_de_grupo = (array) $aPermisosGrupo;
    }

    public function set_permisos_personales($aPermisosPersonales)
    {
        $this->_permisos_personales = (array) $aPermisosPersonales;
    }

    public function get_permisos_de_usuario($iIdUsuario)
    {
        $id_usuario = (int) $iIdUsuario;
        $permisos_personales = $this->_usuarios_model->get_permisos_personales_por_usuario($id_usuario);
        $permisos_de_grupo = $this->_get_permisos_de_grupo_agrupados($id_usuario);

        $this->set_permisos_personales($permisos_personales);
        $this->set_permisos_de_grupo($permisos_de_grupo);

        $this->_permisos_calculados = $this->_permisos_de_grupo;

        if ( ! empty($this->_permisos_personales)) {
            foreach ($this->_permisos_personales as &$permiso_personal) {
                $id_acl_permiso = (int) $permiso_personal["id_acl_permiso"];
                $es_heredado = $this->_es_heredado_de_grupo($id_acl_permiso);
                if ($es_heredado === TRUE) {
                    $this->_sobrescribir_permiso_grupo($permiso_personal);
                } else {
                    $this->_permisos_calculados[$id_acl_permiso] = array(
                        "tipo_permiso" => (int) $permiso_personal["tipo_permiso"],
                        "heredado_de" => array(
                            "tipos_permiso" => array((int) $permiso_personal["tipo_permiso"]),
                            "ids" => array(self::ID_GRUPO_PERMISO_PERSONAL),
                            "nombres" => array(self::NOMBRE_GRUPO_PERMISO_PERSONAL),
                        ),
                    );
                }
            }
        }
        ksort($this->_permisos_calculados);
        return $this->_permisos_calculados;
    }

    public function get_permisos_requeridos()
    {
        $this->_CI->load->model("acl/acl_permisos_model", "model_permisos");
        $solo_requeridos = TRUE;
        $permisos_requeridos = $this->_CI->model_permisos->get_permisos_seteados($solo_requeridos);
        $permisos_controller_index = array();
        foreach ($permisos_requeridos as &$permiso) {
            $nombre_clase = mb_strtolower($permiso["controlador"]);
            if ( ! isset($permisos_controller_index[$nombre_clase])) {
                $permisos_controller_index[$nombre_clase] = array();
            }
            array_push($permisos_controller_index[$nombre_clase], $permiso);
        }
        return $permisos_controller_index;
    }

    private function _get_permisos_de_grupo_agrupados($id_usuario)
    {
        $permisos_grupo = $this->_usuarios_model->get_permisos_de_grupo_por_usuario($id_usuario);
        $permisos_key_index = array();
        foreach ($permisos_grupo as $permiso) {
            if (isset($permisos_key_index[$permiso['id_acl_permiso']])) {
                $permisos_key_index[$permiso['id_acl_permiso']]["heredado_de"]["tipos_permiso"][] = self::TIPO_PERMISO_OTORGADO;
                $permisos_key_index[$permiso['id_acl_permiso']]["heredado_de"]["ids"][] = (int) $permiso["id_acl_grupo"];
                $permisos_key_index[$permiso['id_acl_permiso']]["heredado_de"]["nombres"][] = $permiso["grupo"];
            } else {
                $permisos_key_index[$permiso['id_acl_permiso']] = array("tipo_permiso" => self::TIPO_PERMISO_OTORGADO);
                $permisos_key_index[$permiso['id_acl_permiso']]["heredado_de"] = array(
                    "tipos_permiso" => array(self::TIPO_PERMISO_OTORGADO),
                    "ids" => array((int) $permiso["id_acl_grupo"]),
                    "nombres" => array($permiso["grupo"]),
                );
            }
        }
        return $permisos_key_index;
    }

    private function _es_heredado_de_grupo($id_acl_permiso)
    {
        $permisos_de_grupo = $this->_permisos_de_grupo;
        foreach ($permisos_de_grupo as $id_acl_permiso_grupo => $permiso_de_grupo) {
            if ((int) $id_acl_permiso_grupo === (int) $id_acl_permiso) {
                return TRUE;
            }
        }
        return FALSE;
    }

    private function _sobrescribir_permiso_grupo($aPermisoPersonal)
    {
        $permiso_personal = (array) $aPermisoPersonal;
        $id_acl_permiso = (int) $permiso_personal["id_acl_permiso"];

        $this->_permisos_calculados[$id_acl_permiso]["tipo_permiso"] = (int) $permiso_personal["tipo_permiso"];
        $this->_permisos_calculados[$id_acl_permiso]["heredado_de"]["tipos_permiso"][] = (int) $permiso_personal["tipo_permiso"];
        $this->_permisos_calculados[$id_acl_permiso]["heredado_de"]["ids"][] = self::ID_GRUPO_PERMISO_PERSONAL;
        $this->_permisos_calculados[$id_acl_permiso]["heredado_de"]["nombres"][] = self::NOMBRE_GRUPO_PERMISO_PERSONAL;
    }

}
