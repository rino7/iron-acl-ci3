<?php

/**
 * 13/11/2014
 * File: acl_grupos_model.php
 * Encoding: ISO-8859-1
 * Project: acl
 * Description of acl_grupos_model
 *
 * @author Diego Olmedo
 */
class Acl_login_model extends CI_Model
{

    const TABLA_USUARIO = "acl_usuario";
    const PK_TABLA_USUARIO = "id_acl_usuario";
    const CAMPO_USUARIO = "usuario";
    const CAMPO_PASSWORD = "contrasenia";
    const MAX_CII = 3;
    const LOGIN_CORRECTO = 0;
    const ERROR_NICKNAME = 1;
    const ERROR_PASSWORD = 2;
    const ERROR_BLOQUEADO = 3;

    protected $_idUsuarioLogueado = 0;

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function login($usuario, $password)
    {
//        $sql = "SELECT id_acl_usuario, contrasenia, cii FROM acl_usuario WHERE usuario = ? AND activo = 'S'";
//        $query = $this->db->query($sql, array($usuario));
        $this->db->select(self::PK_TABLA_USUARIO . ", cii, " . self::CAMPO_PASSWORD);
        $query = $this->db->get_where(self::TABLA_USUARIO, array(self::CAMPO_USUARIO => $usuario, "activo" => "S"));

        $row = $query->row_array();
        if (empty($row)) {
            return self::ERROR_NICKNAME;
        }
        if ( ! empty($row)) {
            $id_usuario = (int) $row[self::PK_TABLA_USUARIO];
            $contraseniaDB = $row[self::CAMPO_PASSWORD];
            $this->_idUsuarioLogueado = $id_usuario;
        }

        if ((int) $row["cii"] >= self::MAX_CII) { //Chequeamos si esta bloqueado el usuario
            return self::ERROR_BLOQUEADO;
        }
        //Verificamos la contrase�a
        if ($this->_validar_password($password, $contraseniaDB) === FALSE) {
            $this->_registrar_ingreso_incorrecto($id_usuario);
            return self::ERROR_PASSWORD;
        }
        return self::LOGIN_CORRECTO;
    }

    public function get_id_usuario()
    {
        return $this->_idUsuarioLogueado;
    }

    /**
     *
     * @param string $sPasswordIngresada
     * @param string $sPasswordDB
     * @return boolean
     */
    private function _validar_password($sPasswordIngresada, $sPasswordDB)
    {
        if (crypt($sPasswordIngresada, $sPasswordDB) === $sPasswordDB) {
            return true;
        }
        return false;
    }

    private function _registrar_ingreso_incorrecto($iIdUsuario)
    {
        $id_usuario = (int) $iIdUsuario;
        $sql = "UPDATE " . self::TABLA_USUARIO . " SET cii = cii + 1 WHERE id_acl_usuario = {$id_usuario}";
        $this->db->query($sql);
        return $this->db->affected_rows() >= 0;
//        $data = array(
//            'id_usuario' => $iIdUsuario,
//            'fecha' => date('Y-m-d H:i:s'),
//            'ip' => $this->input->server('REMOTE_ADDR'),
//        );
//        $this->db->insert('ingreso_invalido', $data);
    }

}
