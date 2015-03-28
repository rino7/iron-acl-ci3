<?php

/**
 * 15/11/2014
 * File: acl_permisos_model.php
 * Encoding: ISO-8859-1
 * Project: ci_auth
 * Description of acl_permisos_model
 *
 * @author Diego Olmedo
 */
class Acl_permisos_model extends CI_Model
{

    const TABLA_PERMISO = "acl_permiso";

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param boolean $bSoloRequerido
     * @return array
     */
    public function get_permisos_seteados($bSoloRequerido = FALSE)
    {
        $this->db->order_by('controlador', 'asc');
        if ($bSoloRequerido === TRUE) {
            $this->db->where("blacklist", 1);
        }
        $rows = $this->db->get('acl_permiso')->result_array();
        $permisos = array();
        foreach ($rows as $row) {
            $permisos[$row['identificador']] = $row;
        }
        return $permisos;
    }

    public function insertar($aValues)
    {
        $this->db->insert(self::TABLA_PERMISO, $aValues);
        return $this->db->insert_id();
    }

    public function actualizar($mIdentificador, $aValues)
    {
        if (is_int($mIdentificador)) {
            $pk = "id_acl_permiso";
        } else {
            $pk = "identificador";
        }
        $this->db->update(self::TABLA_PERMISO, $aValues, array($pk => $mIdentificador));
        return $this->db->affected_rows() >= 0;
    }

    public function permiso_existente($sIdentificador)
    {
        $this->db->where("identificador", $sIdentificador);
        return $this->db->count_all_results(self::TABLA_PERMISO) > 0;
    }

    public function es_permiso_custom($iIdPermiso)
    {
        $id_permiso = (int) $iIdPermiso;
        $this->db->where(array("id_acl_permiso" => $id_permiso, "controlador" => "acl_custom", "accion" => "acl_custom"));
        $this->db->from(self::TABLA_PERMISO);
        return $this->db->count_all_results() > 0;
    }

    public function get_data_custom($iIdPermiso)
    {
        $id_permiso = (int) $iIdPermiso;
        $query = $this->db->get_where(self::TABLA_PERMISO, array("id_acl_permiso" => $id_permiso, "controlador" => "acl_custom", "accion" => "acl_custom"));
        return $query->row_array();
    }

    /**
     * Desactiva todos los permisos.
     */
    public function desactivar_permisos()
    {
        $this->db->update(self::TABLA_PERMISO, array("activo" => 0));
    }

    /**
     * Borra los permisos que no se usan. Activo = 0
     */
    public function borrar_permisos_obsoletos()
    {
        $this->db->delete(self::TABLA_PERMISO, array("activo" => 0));
    }

}
