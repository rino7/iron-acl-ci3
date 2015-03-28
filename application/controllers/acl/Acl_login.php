<?php

/**
 * 14/11/2014
 * File: acl_login.php
 * Encoding: ISO-8859-1
 * Project: ci_auth
 * Description of acl_login
 *
 * @author Diego Olmedo
 */
class Acl_login extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library("session");
        $this->load->helper(array("url", "acl"));
        $this->load->model("acl/acl_login_model", "model");

    }

    /**
     * @whitelist true
     */
    public function index()
    {
        if ( ! is_null($this->session->get_data('usuario'))) {
            redirect("/", "refresh");
        }
        $referer = $this->input->server("HTTP_REFERER");
        if (stripos($referer, "login") === FALSE) {
            $this->session->set_data("referer", $this->input->server("HTTP_REFERER"));
        }

        $dataPagina = array();
        $dataPagina["error"] = "";
        if ($this->input->get("respuesta")) {
            $respuesta = $this->input->get("respuesta");
            if ($respuesta === "credenciales") {
                $dataPagina["error"] = "Usuario o contrase&ntilde;a inv&aacute;lido";
            }
            if ($respuesta === "bloqueado") {
                $dataPagina["error"] = "Su usuario se encuentra bloqueado.<br/> Por favor, cont&aacute;ctese con el administrador para solucionar este problema.";
            }
        }
        $dataLayout = array();
        $dataLayout["id_pagina"] = "login";
        $dataLayout["contenido"] = $this->load->view("acl/login_acl", $dataPagina, TRUE);
        $this->load->view("acl/layout_login_acl", $dataLayout);
    }

    /**
     * @whitelist true
     */
    public function login()
    {

        if ($this->input->post("login")) {
            $this->load->helper('url');
            $usuario = $this->input->post("usuario");
            $password = $this->input->post("contrasenia");
            $nroError = $this->model->login($usuario, $password);
            if ($nroError === Acl_login_model::LOGIN_CORRECTO) {
                $idUsuarioLogueado = (int) $this->model->get_id_usuario();
                $this->_llenar_datos_usuario($idUsuarioLogueado);
                $referer = $this->session->get_data("referer");
                if (empty($referer)) {
                    $referer = "/";
                }
                redirect($referer);
            } else {
                if ($nroError === Acl_login_model::ERROR_BLOQUEADO) {
                    redirect('/acl/acl_login/?respuesta=bloqueado');
                }
                if ($nroError === Acl_login_model::ERROR_NICKNAME || $nroError === Acl_login_model::ERROR_PASSWORD) {
                    redirect('/acl/acl_login/?respuesta=credenciales');
                }
            }
        }
    }

    /**
     * @whitelist true
     */
    public function logout()
    {
        $this->session->finalizar();
        redirect("/");
    }

    private function _llenar_datos_usuario($iIdUsuario)
    {
        $this->load->model("acl/acl_usuarios_model", "usuario_model");
        $datosUsuario = (array) $this->usuario_model->buscar_por_id((int) $iIdUsuario);
        if ( ! empty($datosUsuario)) {
            $this->session->set_data('usuario', $datosUsuario);
        }
    }

}
