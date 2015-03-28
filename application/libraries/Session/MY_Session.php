<?php

/**
 * 28/03/2015
 * File: MY_Session.php
 * Encoding: UTF-8
 * Project: iron_acl_ci3
 * Description of MY_Session
 *
 * @author Diego Olmedo
 */
class MY_Session extends CI_Session
{

    public function __construct(array $params = array())
    {
        parent::__construct($params);
    }

    /**
     * Devuelve el valor guardado en sesion para una key o en caso de ser un array
     * multidimensional se puede pasar una cadena separada por barras que representen
     * la jerarquía
     * @example
     * La cadena foo/bar/nombre buscará el valor establecido en
     * [foo]
     *      [bar]
     *          [nombre]
     * @param string $key La key o cadena de key buscada en el array $_SESSION
     * @return mixed El valor buscado o NULL si no existe la key buscada en el array $_SESSION
     */
    public function get_data($key)
    {
        $keys = explode('/', trim($key, "/"));
        if (count($keys) > 1) {
            return $this->_get_data_desde_ruta($keys);
        }
        return $this->userdata($key);
    }

    /**
     * Setea un valor en la clave indicada en el array $_SESSION.
     * Si la cadena es un string separado por barras (/), arma la estructura del array
     * siguiendo la jerarquía indicada y finalmente setea el valor dado en el último
     * elemento
     * @example
     * La cadena foo/bar/nombre con value = "un_nombre" formará
     * [foo]
     *      [bar]
     *          [nombre] = "un_nombre"
     * @param string $key Nombre de la key o cadena de key para armar la estructura del array
     * @param mixed $value el valor a setear
     */
    public function set_data($key, $value)
    {
        $keys = explode('/', trim($key, "/"));
        if (count($keys) > 1) {
            $this->_set_data_desde_ruta($keys, $value);
        } else {
            //$_SESSION[$key] = $value;
            $this->set_userdata($key, $value);
        }
    }


    public function finalizar()
    {
        $this->sess_destroy();
    }
    /**
     * Obtiene el valor de un elemento del array a partir de una cadena separada
     * por barras (/)
     * @example
     * La cadena foo/bar/nombre buscará el valor establecido en
     * [foo]
     *      [bar]
     *          [nombre]
     * @param array $keys Array de claves de la estructura
     * @return mixed el valor encontrado o NULL si alguno de los elementos no existe
     */
    private function _get_data_desde_ruta($keys)
    {
        $session = $_SESSION;
        $refSession = &$session;
        while (count($keys) > 0) {
            $key = array_shift($keys);
            if ( ! isset($refSession[$key])) {
                return NULL;
            }
            $refSession = $refSession[$key];
        }
        return $refSession;
    }

    /**
     * Arma la estructura del array y setea el valor del último elemento a
     * a partir de una cadena separada por barras (/).
     * @example
     * La cadena foo/bar/nombre con value = "un_nombre" quedará así
     * [foo]
     *      [bar]
     *          [nombre] = "un_nombre"
     * @param array $keys Array de claves para formar la estructura
     * @param mixed $valor El valor que se seteará al último elemento de la cadena
     */
    private function _set_data_desde_ruta($keys, $valor)
    {
        $refSession = &$_SESSION;
        while (count($keys) > 0) {
            $key = array_shift($keys);
            if ( ! isset($refSession[$key])) {
                $refSession[$key] = array();
            }

            $refSession = &$refSession[$key];
        }
        $refSession = $valor;
    }

}
