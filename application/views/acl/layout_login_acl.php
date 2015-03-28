<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="ISO-8859-1">
        <title>Autenticaci&oacute;n y Control de Acceso</title>
        <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet">
        <link href="/assets/acl/css/acl.css" rel="stylesheet" type="text/css"/>
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>



    </head>
    <body class="body-login">
        <div class="container">
            <?php
            if (isset($contenido)) {
                echo $contenido;
            }
            $this->load->view("acl/footer_acl");
