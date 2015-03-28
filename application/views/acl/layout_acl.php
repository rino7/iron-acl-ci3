<?php

$this->load->view("acl/header_acl");
if (isset($contenido)) {
    echo $contenido;
}
$this->load->view("acl/footer_acl");
