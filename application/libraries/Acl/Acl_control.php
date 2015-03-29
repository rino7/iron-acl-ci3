<?php

/**
 * 09/11/2014
 * File: acl_validation.php
 * Encoding: ISO-8859-1
 * Project: acl
 * Description of acl_validation
 *
 * @author Diego Olmedo
 */
class Acl_control
{

    protected $_CI;
    protected $_id_usuario;
    protected $_url_redirect;
    protected $_controlador;
    protected $_accion;
    protected $_id_permiso;

    const CONTROLADOR_LOGIN = "acl_login";

    public function __construct()
    {
        $this->_CI = & get_instance();
        if (is_null($this->_CI)) {
            return FALSE;
        }
        //cargo las configuraciones de acl
        $this->_CI->config->load("acl");
        $this->_CI->load->database();
        $this->_CI->load->library("session");
        $this->_CI->load->helper(array("acl", "url"));

        $this->_id_usuario = (int) $this->_CI->session->get_data("usuario/id_acl_usuario");
        //echo "<hr/><pre>";print_r($this->_CI->session->get_data("usuario"));echo "</pre><hr/>";die();
//        var_dump($this->_id_usuario); die;
        $this->_url_redirect = '/acl/error';
        $this->_controladores_whitelist = array("acl");
    }

    private function _whitelist_logueado()
    {
        $controlador = $this->_CI->router->fetch_class();
        if ($controlador === self::CONTROLADOR_LOGIN) {
            return TRUE;
        }
        return FALSE;
//        $whitelist = array("acl/acl_login", "acl/acl_login/index", "acl/acl_login/login", "acl/acl_login/logout", "logout", "login");
//        $uri_string = trim(uri_string(), "/");
//        $ok = in_array($uri_string, $whitelist);
        //return $ok;
    }

    private function _usuario_logueado()
    {
        if ($this->_id_usuario === 0 OR empty($this->_id_usuario)) {
            return FALSE;
        }
        return TRUE;
    }

    public function validar_permiso()
    {
        //echo $this->_CI->router->uri; die;
        if (TRUE === $this->_whitelist_logueado() OR $this->_CI->config->item("autenticar") === FALSE) {
            return TRUE;
        }

        if (FALSE === $this->_usuario_logueado()) {
            redirect("/acl/acl_login");
            exit;
        } else {
            $identificador = $this->_get_identificador();
            //var_dump($identificador);die;
            $tiene_permiso = $this->tiene_permiso($identificador);
            if ($tiene_permiso === NULL) {
                //En teoría si no está es porque no hace falta permiso => whitelist por código
                return TRUE;
                //redirect($this->_url_redirect . "/permiso_desconocido/$identificador");
            }
            if ($tiene_permiso === FALSE) {
                show_error_permiso();
            }
            if ($tiene_permiso === TRUE) {
                return TRUE;
            }
        }
    }

    public function tiene_permiso($sIdentificador)
    {
        $this->_set_id_permiso($sIdentificador);
        if (empty($this->_id_permiso)) {
            return NULL;
            //redirect($this->_url_redirect . "/permiso_desconocido/$identificador");
        }
        if ($this->_es_whitelist()) {
            //print("whitelist");
            return TRUE;
        }

        if ($this->_tiene_permiso() === FALSE) {
            //die('error - User don\'t has permission. ' . $this->_id_permiso);
            return FALSE;
            //show_error_permiso();
            //redirect($this->_url_redirect . "/permiso_denegado");
            exit;
        }
        return TRUE;
    }

    private function _get_identificador()
    {
        $controlador = $this->_CI->router->fetch_class();
        $accion = $this->_CI->router->fetch_method();
        $identificador = armar_identificador_permiso($controlador, $accion);
        return $identificador;
    }

    private function _set_id_permiso($sIdentificador)
    {
        $identificador = trim($sIdentificador, "/");
        $this->_CI->db->select("id_acl_permiso");
        $query = $this->_CI->db->get_where("acl_permiso", array("identificador" => $identificador));
        //echo $this->_CI->db->last_query(); die;
        $row = $query->row_array();
        if (empty($row)) {
            $this->_id_permiso = NULL;
        } else {
            $this->_id_permiso = (int) $row["id_acl_permiso"];
        }
    }

    /**
     * @TODO: Implementar
     * @param type $sPermission
     * @return boolean
     */
    public function validate_custom_permission($sPermission)
    {

        $this->_CI->db->select('p.id_acl_permiso');
        $this->_CI->db->join('acl_usuario_permiso as up', 'p.id_acl_permiso = up.fk_acl_permiso');
        $this->_CI->db->where('p.custom', $sPermission);
        $this->_CI->db->where('up.fk_id_acl_usuario', $this->_id_usuario);
        $query = $this->_CI->db->get('acl_permiso as p');
        //echo $this->_CI->db->last_query();
        if ($query->num_rows == 0) {
            return false;
        }
        return true;
    }

    private function _es_whitelist()
    {
        $this->_CI->db->where('id_acl_permiso', $this->_id_permiso);
        $this->_CI->db->where('whitelist', 1);
        $count = $this->_CI->db->count_all_results('acl_permiso');
        return $count > 0;
    }

    private function _tiene_permiso()
    {
        $permiso_de_grupo = $this->_grupo_usuario_tiene_permiso();
        //var_dump($permiso_de_grupo); die;
        if ($permiso_de_grupo === TRUE) {
            $denegado = $this->_usuario_no_tiene_permiso();
            return $denegado === FALSE;
        }
        return $this->_usuario_tiene_permiso();
    }

    private function _usuario_tiene_permiso()
    {
        $this->_CI->db->select('up.id_acl_usuario_permiso');
        $this->_CI->db->join('acl_usuario_permiso as up', 'p.id_acl_permiso = up.fk_acl_permiso');
        $this->_CI->db->where('p.id_acl_permiso', $this->_id_permiso);
        $this->_CI->db->where('up.fk_acl_usuario', $this->_id_usuario);
        $this->_CI->db->where('up.tipo_permiso', 1);
        $query = $this->_CI->db->get('acl_permiso as p');
        return $query->num_rows > 0;
    }

    private function _usuario_no_tiene_permiso()
    {
        $this->_CI->db->select('up.id_acl_usuario_permiso');
        $this->_CI->db->join('acl_usuario_permiso as up', 'p.id_acl_permiso = up.fk_acl_permiso');
        $this->_CI->db->where('p.id_acl_permiso', $this->_id_permiso);
        $this->_CI->db->where('up.fk_acl_usuario', $this->_id_usuario);
        $this->_CI->db->where('up.tipo_permiso', 0);
        $query = $this->_CI->db->get('acl_permiso as p');
        return $query->num_rows > 0;
    }

    private function _grupo_usuario_tiene_permiso()
    {
        $where = array("u.id_acl_usuario" => (int) $this->_id_usuario, "p.id_acl_permiso" => (int) $this->_id_permiso);

        $this->_CI->db->join("acl_grupo_permiso gp", "gp.fk_acl_grupo = ug.fk_acl_grupo", "INNER");
        $this->_CI->db->join("acl_usuario u", "u.id_acl_usuario = ug.fk_acl_usuario", "INNER");
        $this->_CI->db->join("acl_permiso p", "p.id_acl_permiso = gp.fk_acl_permiso", "INNER");
        $this->_CI->db->where($where);
        $cant = $this->_CI->db->count_all_results("acl_usuario_grupo ug");
        //echo $this->_CI->db->last_query(); die;
        return (int) $cant > 0;
    }

}
