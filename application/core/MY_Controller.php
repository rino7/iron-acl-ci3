<?php

/**
 * 28/03/2015
 * File: MY_Controllers.php
 * Encoding: UTF-8
 * Project: iron_acl_ci3
 * Description of MY_Controllers
 *
 * @author Diego Olmedo
 */
class MY_Controller extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library("session");
    }
}
