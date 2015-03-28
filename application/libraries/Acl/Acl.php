<?php

/**
 * 08/11/2014
 * File: acl.php
 * Encoding: ISO-8859-1
 * Project: acl
 * Description of acl
 * Idea basada en https://github.com/scorpionslh/ACL
 * @author Diego Olmedo
 */
class Acl
{

    protected $_CI;
    protected $_permisos;
    protected $_controladores_whitelist = array("CI_Controller", "Acl");

    const CONTROLADOR_CUSTOM = "acl_custom";
    const ACCION_CUSTOM = "acl_custom";

    public function __construct()
    {
        $this->_CI = & get_instance();
        $this->_CI->load->database();
        $this->_CI->load->helper(array("acl"));
    }

    public function get_permisos($bSoloRequerido = FALSE)
    {
        $controladores = $this->_get_controladores();
        $permisos_controladores = $this->_procesar_permisos($controladores, $bSoloRequerido);
        $permisos_custom = $this->_get_permisos_custom();
        $permisos = array_merge($permisos_controladores, $permisos_custom);
        return $permisos;
    }

    private function _get_permisos_custom()
    {
        $this->_CI->db->order_by("identificador");
        $query = $this->_CI->db->get_where("acl_permiso", array("controlador" => self::CONTROLADOR_CUSTOM, "accion" => self::ACCION_CUSTOM));
        $rows = $query->result_array();
        $permisos = array();
        $permisos[self::CONTROLADOR_CUSTOM]["descripcion_modulo"] = "Permisos personalizados";
        $permisos[self::CONTROLADOR_CUSTOM]["permisos"] = array();
        foreach ($rows as $row) {
            array_push($permisos[self::CONTROLADOR_CUSTOM]["permisos"], $row);
        }
        return $permisos;
    }

    private function _get_controladores()
    {
        $directoryList = FCPATH . 'application/controllers/';
        $directory = new \RecursiveDirectoryIterator($directoryList);
        $iterator = new \RecursiveIteratorIterator($directory);
        $classes = array();
        foreach ($iterator as $fileinfo) {
//            $folder = '';
            if ($fileinfo->isFile()) {
                if (stripos($fileinfo, '.php') !== FALSE) {
                    include_once($fileinfo);
                    /* Por ahora no se usa, pero estaba en la librería original. Es para guardar la carpeta del controlador.
                      $dir = str_replace(array($directoryList, '.php'), '', $fileinfo->getRealPath());
                      if (stripos($dir, "/") !== false) {
                      list($folder, $filenameFile) = explode('/', $dir);
                      }
                     */
                    $class = str_replace('.php', '', $fileinfo->getFilename());
                    $classes[$class]['class'] = new ReflectionClass($class);
//                    $classes[$class]['folder'] = $folder;
                }
            }
        }
        return $classes;
    }

    private function _get_descripcion_permiso($annotations)
    {
        $permiso = array();
        $descripcion_permiso = "";
        preg_match_all('#@permiso(.*?)\n#si', $annotations, $permiso);
        if (isset($permiso[1][0])) {
            $descripcion_permiso = trim($permiso[1][0]);
        }
        return $descripcion_permiso;
    }

    private function _get_descripcion_modulo_permiso($annotations)
    {
        $permiso = array();
        $descripcion_permiso = "";
        preg_match_all('#@moduloPermiso(.*?)\n#si', $annotations, $permiso);
        if (isset($permiso[1][0])) {
            $descripcion_permiso = trim($permiso[1][0]);
        }
        return $descripcion_permiso;
    }

    private function _es_whitelist_por_codigo($annotations)
    {
        $whitelist = array();
        preg_match_all('#@whitelist(.*?)\n#si', $annotations, $whitelist);
        return isset($whitelist[1][0]);
    }

    private function _get_annotations($object)
    {
        return $object->getDocComment();
    }

    private function _procesar_permisos($aControladores)
    {
        $controladores = (array) $aControladores;
        $this->_CI->load->model("acl/acl_permisos_model", "model_permisos");
        $permisos_seteados = $this->_CI->model_permisos->get_permisos_seteados();
        $permisos = array();
        $controladores_whitelist = array("CI_Controller");
        foreach ($controladores as $controlador) {
            $annotations_clase = $this->_get_annotations($controlador['class']);
            if ($this->_es_whitelist_por_codigo($annotations_clase) === FALSE) {
                $descripcion_modulo_permiso = $this->_get_descripcion_modulo_permiso($annotations_clase);
                $metodos = $controlador['class']->getMethods(ReflectionMethod::IS_PUBLIC);
                $index = 0;
                foreach ($metodos as $metodo) {
                    $annotations = $this->_get_annotations($metodo);
                    $metodo_valido = FALSE === stripos($metodo->name, "__");
                    $clase_valida = FALSE === in_array($metodo->class, $controladores_whitelist);
                    $whitelist_codigo = $this->_es_whitelist_por_codigo($annotations);
                    if ($metodo_valido === TRUE AND $clase_valida === TRUE AND $whitelist_codigo === FALSE) {
                        $nombre_clase = mb_strtolower($metodo->class);
                        if ( ! isset($permisos[$nombre_clase])) {
                            $permisos[$nombre_clase] = array();
                            $permisos[$nombre_clase]["descripcion_modulo"] = $descripcion_modulo_permiso;
                            $permisos[$nombre_clase]["permisos"] = array();
                        }
                        $permiso = $this->_procesar_metodo($metodo, $permisos_seteados, $annotations);

                        $permisos[$nombre_clase]["permisos"][$index] = $permiso;
                        $index ++;
                    }
                }
            }
        }
        return $permisos;
    }

    private function _procesar_metodo($metodo, $permisos_seteados, $annotations)
    {
        $whitelist = NULL;
        $blacklist = NULL;
        $id_permiso = 0;
        $identificador = armar_identificador_permiso($metodo->class, $metodo->name);

        if (isset($permisos_seteados[$identificador])) {
            $whitelist = $permisos_seteados[$identificador]["whitelist"];
            $blacklist = $permisos_seteados[$identificador]["blacklist"];
            $id_permiso = $permisos_seteados[$identificador]["blacklist"];
        }
        $descripcion = $this->_get_descripcion_permiso($annotations);
        $permiso['id_acl_permiso'] = $id_permiso;
        $permiso['descripcion'] = $descripcion;
        $permiso['identificador'] = $identificador;
        $permiso['controlador'] = $metodo->class;
        $permiso['accion'] = $metodo->name;
        $permiso['whitelist'] = $whitelist;
        $permiso['blacklist'] = $blacklist;
        return $permiso;
    }

}
