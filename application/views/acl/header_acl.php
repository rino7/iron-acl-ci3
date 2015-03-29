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
<!--        <script src="/assets/acl/js/pubsub.js" type="text/javascript"></script>-->
        <script src="/assets/acl/js/acl.js"></script>
        <?php if (isset($js_agregado)) : ?>
            <script src="<?php echo $js_agregado; ?>"></script>
        <?php endif; ?>
        <!-- Jquery UI para jquery 1.11.0 -->
        <link rel="stylesheet" href="/assets/acl/jquery-ui/jquery-ui.min.css">
        <script src="/assets/acl/jquery-ui/jquery-ui.min.js"></script>

    </head>
    <body  <?php echo isset($id_pagina) ? "id={$id_pagina}" : ""; ?>>
        <div class="container">
            <?php if (FALSE === isset($no_mostrar_menu)) : ?>
                <?php $this->load->view("acl/menu_acl"); ?>
            <?php endif; ?>
