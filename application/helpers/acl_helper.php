<?php

define("SEPARADOR_IDENTIFICADOR", "/");
if ( ! function_exists('armar_identificador_permiso')) {

    /**
     *
     * @param string $sControlador
     * @param string $sAccion
     * @return string
     */
    function armar_identificador_permiso($sControlador, $sAccion)
    {
        return mb_strtolower($sControlador . SEPARADOR_IDENTIFICADOR . $sAccion);
    }

}

if ( ! function_exists('desarmar_identificador_permiso')) {


    /**
     *
     * @param string $sIdentificador
     * @return array
     */
    function desarmar_identificador_permiso($sIdentificador)
    {
        $identificador = trim($sIdentificador, "/");
        $partes = explode(SEPARADOR_IDENTIFICADOR, $identificador);
        if (is_array($partes) AND sizeof($partes) === 2) {
            return array("controlador" => $partes[0], "accion" => $partes[1]);
        }
        return NULL;
    }

}
if ( ! function_exists('get_grupos')) {

    /**
     * Devuelve un listado de grupos
     * @param string $sOrden el campo para ordenar
     * @param string $sSentido el sentido del orden [ASC | DESC]
     * @return array
     */
    function get_grupos($bSoloActivos = FALSE, $sOrden = "id_acl_grupo", $sSentido = "DESC")
    {
        $CI = & get_instance();
        $where = array("eliminado" => 0);
        if ($bSoloActivos === TRUE) {
            $where["activo"] = "S";
        }
        $CI->db->where($where);
        $CI->db->order_by($sOrden, $sSentido);
        return $CI->db->get_where("acl_grupo", $where)->result_array();
    }

}

if ( ! function_exists('get_nombre_grupo')) {

    /**
     * Devuelve el nombre de un grupo
     * @param int $iIdGrupo
     * @return string
     */
    function get_nombre_grupo($iIdGrupo)
    {
        $id_grupo = (int) $iIdGrupo;
        $CI = & get_instance();
        $where = array("id_acl_grupo" => $id_grupo);
        $CI->db->select("nombre");
        $CI->db->where($where);
        $row = $CI->db->get_where("acl_grupo")->row_array();
        return $row["nombre"];
    }

}

if ( ! function_exists('get_usuarios')) {

    /**
     * Devuelve un listado de usuarios
     * @return array
     */
    function get_usuarios()
    {
        $CI = & get_instance();
        $where = array("activo" => "S", "eliminado" => 0);
        $CI->db->where($where);
        return $CI->db->get_where("acl_usuario", $where)->result_array();
    }

}

if ( ! function_exists('get_nombre_usuario')) {

    /**
     * Devuelve el nombre un usuario en el formato solicitado
     * @param int $iIdUsuario
     * @param string $tipo
     * @return string
     */
    function get_nombre_usuario($iIdUsuario, $tipo = "COMPLETO")
    {
        $id_usuario = (int) $iIdUsuario;
        $CI = & get_instance();
        $where = array("id_acl_usuario" => $id_usuario);
        $CI->db->select("nombre, apellido, usuario");
        $CI->db->where($where);
        $row = $CI->db->get_where("acl_usuario")->row_array();
        if (mb_strtoupper($tipo) === "COMPLETO") {
            $nombre = $row["apellido"] . ", " . $row["nombre"] . " - (" . $row["usuario"] . ")";
        }
        if (mb_strtoupper($tipo) === "CORTO") {
            $nombre = $row["apellido"] . ", " . $row["nombre"];
        }
        if (mb_strtoupper($tipo) === "USUARIO") {
            $nombre = $row["usuario"];
        }
        if (mb_strtoupper($tipo) === "NOMBRE") {
            $nombre = $row["nombre"];
        }
        if (mb_strtoupper($tipo) === "APELLIDO") {
            $nombre = $row["apellido"];
        }
        return $nombre;
    }

}

if ( ! function_exists('init_pagination_acl')) {

    /**
     *
     * @param string $uri
     * @param int $total_rows
     * @param int $per_page
     * @param int $segment
     * @return array
     */
    function init_pagination_acl($uri, $total_rows, $per_page = 10, $segment = 4)
    {
        $ci = & get_instance();
        $config['per_page'] = $per_page;
        $config['uri_segment'] = $segment;
        $config['base_url'] = base_url() . $uri;
        $config['total_rows'] = $total_rows;
        $config['use_page_numbers'] = TRUE;
        $config['reuse_query_string'] = TRUE;
        $config['first_tag_open'] = $config['last_tag_open'] = $config['next_tag_open'] = $config['prev_tag_open'] = $config['num_tag_open'] = '<li>';
        $config['first_tag_close'] = $config['last_tag_close'] = $config['next_tag_close'] = $config['prev_tag_close'] = $config['num_tag_close'] = '</li>';

        $config['num_links'] = 3;
        $config['first_link'] = 'Primero';
        $config['last_link'] = '&Uacute;ltimo';

        $config['cur_tag_open'] = "<li class='active'><span><b>";
        $config['cur_tag_close'] = "</b></span></li>";
        $ci->pagination->initialize($config);
        return $config;
    }

}

if ( ! function_exists('show_error_permiso')) {

    /**
     * Error Handler
     *
     * This function lets us invoke the exception class and
     * display errors using the standard error template located
     * in application/errors/errors.php
     * This function will send the error page directly to the
     * browser and exit.
     *
     * @access	public
     * @return	void
     */
    function show_error_permiso()
    {
        $_error = & load_class('Exceptions', 'core');
        echo $_error->show_error("", "", 'error_permiso', 500);
        exit;
    }

}

if ( ! function_exists('get_descripcion_modulos')) {

    /**
     * Devuelve un array de las descripciones de los módulos de permisos (controladores)
     * donde la key es el controlador y el value la descripción.
     *
     * @return array
     */
    function get_descripcion_modulos()
    {
        $CI = & get_instance();
        $rows = $CI->db->get("acl_modulo_permiso")->result_array();
        $descripciones = array();
        foreach ($rows as $row) {
            $descripciones[$row["controlador"]] = $row["descripcion"];
        }
        return $descripciones;
    }

}

if ( ! function_exists('tiene_permiso')) {

    function tiene_permiso($sIdentificador)
    {
        $identificador = trim($sIdentificador, "/");
        $CI = & get_instance();
        $CI->load->library("Acl/Acl_control", NULL, "acl_control");
        $tiene_permiso = $CI->acl_control->tiene_permiso($identificador);
        if ($tiene_permiso === TRUE OR $tiene_permiso === NULL) {
            //En teoría: si no está el permiso en la db es porque no hace falta permiso => whitelist por código
            return TRUE;
        }
        return FALSE;
    }

}
