<?php

/**
 * 29/03/2015
 * File: Ejemplo.php
 * Encoding: UTF-8
 * Project: iron_acl_ci3
 * Description of Ejemplo
 *
 * @moduloPermiso Métodos de ejemplo para probar el ACL
 * @author Diego Olmedo
 */
class Ejemplo extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @permiso Método de ejemplo para requerir su ingreso. Está en la blacklist.
     */
    public function metodo_requerido()
    {
        echo "INGRESÓ A MÉTODO REQUERIDO";
    }

    /**
     * @permiso Método de ejemplo para permitir su ingreso
     */
    public function metodo_permitido()
    {
        echo "INGRESÓ A MÉTODO PERMITIDO";
    }

    /**
     * @permiso Método de ejemplo para restringir ingreso
     */
    public function metodo_no_permitido()
    {
        echo "INGRESÓ A MÉTODO <strong>NO</strong> PERMITIDO";
    }

    /**
     * @permiso Método de ejemplo para no requerirlo. Está en la whitelist
     */
    public function metodo_no_requerido()
    {
        echo "INGRESÓ A MÉTODO <strong>NO</strong> REQUERIDO";
    }

    /**
     * @permiso Método que no se definió el requerimiento
     */
    public function metodo_sin_definir_requerimiento()
    {
        echo "INGRESÓ A MÉTODO SIN DEFINIR REQUERIMIENTO";
    }

    /**
     * @permiso Método que no se definió el permiso
     */
    public function metodo_sin_definir_permiso()
    {
        echo "INGRESÓ A MÉTODO SIN DEFINIR ASIGNACIÓN DE PERMISO";
    }
}
