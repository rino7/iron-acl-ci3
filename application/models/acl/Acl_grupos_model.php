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
class Acl_grupos_model extends CI_Model
{

    const TABLA_GRUPO = "acl_grupo";
    const PK_TABLA_GRUPO = "id_acl_grupo";
    const TABLA_GRUPO_PERMISO = "acl_grupo_permiso";
    const TABLA_USUARIO_GRUPO = "acl_usuario_grupo";

    public function __construct()
    {
        parent::__construct();
    }

    public function get_all($iLimit = 0, $iOffset = 0, $sOrden = "nombre", $sSentido = "asc", $bSoloActivos = FALSE)
    {
        $limit = (int) $iLimit;
        $campos_orden = array("numero" => "id_acl_grupo", "nombre" => "nombre", "activo" => "activo");
        $orden = isset($campos_orden[$sOrden]) ? $campos_orden[$sOrden] : self::PK_TABLA_GRUPO;
        //valido que no manden cualquier cosa en el sentido
        $sentido = strtolower($sSentido) === "desc" ? "desc" : "asc";

        $where = array("eliminado" => 0);
        if ($bSoloActivos === TRUE) {
            $where["activo"] = "S";
        }
        if ($limit > 0) {
            $offset = (int) $iOffset;
            $this->db->limit($limit, $offset);
        }
        $this->_set_filtro_busqueda();
        $this->db->where($where);
        $this->db->order_by($orden, $sentido);
        return $this->db->get_where(self::TABLA_GRUPO, $where)->result_array();
    }

    public function get_activos($iLimit = 0, $iOffset = 0, $sOrden = "nombre", $sSentido = "asc")
    {
        return $this->get_all($iLimit, $iOffset, $sOrden, $sSentido, TRUE);
    }

    public function count_all($bSoloActivos = FALSE)
    {
        $where = array("eliminado" => 0);
        if ($bSoloActivos === TRUE) {
            $where["activo"] = "S";
        }
        $this->db->where($where);
        $this->_set_filtro_busqueda();
        return $this->db->count_all_results(self::TABLA_GRUPO);
    }

    public function count_activos()
    {
        return $this->count_all(TRUE);
    }

    public function insertar($aValues)
    {
        $aValues["activo"] = "S";
        $aValues["fecha_creacion"] = date('Y-m-d H:m:s');
        $this->db->insert(self::TABLA_GRUPO, $aValues);
        return $this->db->insert_id();
    }

    public function actualizar($aValues, $iId)
    {
        $this->db->update(self::TABLA_GRUPO, $aValues, array(self::PK_TABLA_GRUPO => (int) $iId));
        return $this->db->affected_rows() >= 0;
    }

    public function get_permisos_por_grupo($iIdGrupo)
    {
        $id_grupo = (int) $iIdGrupo;
        $permisos_por_grupo = $this->_get_permisos_seteados_por_grupo($id_grupo);
        return $this->_procesar_permisos_por_grupo($permisos_por_grupo);
    }

    public function guardar_permisos($aValues, $iIdGrupo)
    {
        $values = (array) $aValues;
        $id_grupo = (int) $iIdGrupo;
        $this->db->delete(self::TABLA_GRUPO_PERMISO, array("fk_acl_grupo" => $id_grupo));
        $this->db->insert_batch(self::TABLA_GRUPO_PERMISO, $values);
    }

    public function grupo_existente($sNombre, $iIdGrupo)
    {
        $id_grupo = (int) $iIdGrupo;
        $this->db->where(array("nombre" => $sNombre, "eliminado" => 0, self::PK_TABLA_GRUPO => "<> {$id_grupo}"));
        return $this->db->count_all_results(self::TABLA_GRUPO) > 0;
    }

    public function cambiar_activo($sValue, $iIdGrupo)
    {
        $id_grupo = (int) $iIdGrupo;
        $value = $sValue;
        $this->db->update(self::TABLA_GRUPO, array("activo" => $value), array(self::PK_TABLA_GRUPO => $id_grupo));
        return $this->db->affected_rows() >= 0;
    }

    public function get_usuarios_grupo($iIdGrupo, $sOrden = "numero", $sSentido = "desc")
    {
        $campos_orden = array("numero" => "id_acl_usuario_grupo", "nombre" => "apellido");
        $orden = isset($campos_orden[$sOrden]) ? $campos_orden[$sOrden] : self::PK_TABLA_GRUPO;
        //valido que no manden cualquier cosa en el sentido
        $sentido = strtolower($sSentido) === "desc" ? "desc" : "asc";

        $id_grupo = (int) $iIdGrupo;
        $this->db->select("u.*, ug.id_acl_usuario_grupo", FALSE);
        $this->db->join("acl_usuario u", "u.id_acl_usuario = ug.fk_acl_usuario", "INNER");
        $this->db->order_by($orden, $sentido);
        $where = array("u.activo" => "S", "ug.fk_acl_grupo" => $id_grupo);
        $query = $this->db->get_where(self::TABLA_USUARIO_GRUPO . " ug", $where);
        return $query->result_array();
    }

    public function buscar_usuario($iIdGrupo, $sTextoBuscar = "")
    {
        //@TODO: Poner el 'SET NAMES UTF-8'
        $id_grupo = (int) $iIdGrupo;
        if ($id_grupo === 0) {
            return NULL;
        }
        $texto_buscar = $this->db->escape_like_str($sTextoBuscar);
        $and_where = " AND activo = 'S' AND (nombre LIKE '%{$texto_buscar}%' OR apellido LIKE '%{$texto_buscar}%' OR usuario LIKE '%{$texto_buscar}%')";
        $sql = "SELECT
                    id_acl_usuario,
                    nombre,
                    apellido,
                    usuario
                FROM
                    acl_usuario u
                LEFT JOIN acl_usuario_grupo ug
                    ON ug.fk_acl_usuario = u.`id_acl_usuario`
                    AND ug.`fk_acl_grupo` = {$id_grupo}
                WHERE ug.`fk_acl_usuario` IS NULL "
            . "{$and_where}";
        //echo $sql;die;
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function guardar_usuario_grupo($iIdGrupo, $iIdUsuario)
    {
        $id_grupo = (int) $iIdGrupo;
        $id_usuario = (int) $iIdUsuario;
        if ($id_grupo === 0 OR $id_usuario === 0) {
            return FALSE;
        }
        $values = array("fk_acl_usuario" => $id_usuario, "fk_acl_grupo" => $id_grupo);
        $this->db->insert(self::TABLA_USUARIO_GRUPO, $values);
        return $this->db->insert_id();
    }

    public function desasignar_usuarios_grupo($iIdGrupo, $aIdsUsuarios)
    {
        $id_grupo = (int) $iIdGrupo;
        $ids_usuario = (array) $aIdsUsuarios;
        if ($id_grupo === 0 OR empty($ids_usuario)) {
            return FALSE;
        }
        $where = array("fk_acl_grupo" => $id_grupo);
        foreach ($ids_usuario as $id_usuario) {
            $where["fk_acl_usuario"] = $id_usuario;
            $this->db->delete(self::TABLA_USUARIO_GRUPO, $where);
        }
        return TRUE;
    }

    public function eliminar_grupos($aIdsGrupos)
    {
        $ids_grupo = (array) $aIdsGrupos;
        $values = array("activo" => "N", "eliminado" => 0);
        foreach ($ids_grupo as $id_grupo) {
            $where[self::PK_TABLA_GRUPO] = $id_grupo;
            $this->db->update(self::TABLA_GRUPO, $values, $where);
        }
        return TRUE;
    }

//PRIVATE
    private function _set_filtro_busqueda()
    {
        if ($this->input->get("texto_buscar")) {
            $like = $this->db->escape_like_str($this->input->get("texto_buscar"));
            $this->db->like("nombre", $like);
        }
    }

    private function _get_permisos_seteados_por_grupo($iIdGrupo)
    {
        $id_grupo = (int) $iIdGrupo;
        $this->db->where("fk_acl_grupo", $id_grupo);
        $rows = $this->db->get(self::TABLA_GRUPO_PERMISO)->result_array();
        $permisos = array();

        foreach ($rows as $row) {
            $permisos[$row['fk_acl_permiso']] = $row;
        }
        return $permisos;
    }

    private function _procesar_permisos_por_grupo($aPermisosSeteadosPorGrupo)
    {
        $permisos_requeridos = $this->_get_permisos_seteados(TRUE);
        $permisos_por_grupo = (array) $aPermisosSeteadosPorGrupo;
        $mis_permisos = array();
        $index = 0;
        foreach ($permisos_requeridos as &$permiso) {
            $permiso["permitido"] = 0;
            if (isset($permisos_por_grupo[$permiso["id_acl_permiso"]])) {
                $permiso["permitido"] = 1;
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

    private function _get_permisos_seteados($bSoloRequerido = FALSE)
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

}
