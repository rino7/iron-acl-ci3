<?php if (!empty($error)) : ?>
    <div class='row'>
        <div class="js-alert alert alert-danger alert-dismissible col-md-4 col-md-offset-4" role="alert">
            <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <strong>¡Error!</strong> <?php echo $error; ?>.
        </div>
    </div>
<?php endif; ?>
<form class="form-signin" role="form" action="/acl/acl_login/login" method="POST">
    <h2 class="form-signin-heading">Ingrese los datos</h2>
    <label for="usuario" class="sr-only">Usuario</label>
    <input type="text" id="usuario" name="usuario" class="form-control" placeholder="Usuario" required autofocus>
    <label for="contrasenia" class="sr-only">Contrase&ntilde;a</label>
    <input type="password" id="contrasenia" name="contrasenia" class="form-control" placeholder="Password" required>
    <!--    <div class="checkbox">
            <label>
                <input type="checkbox" value="remember-me"> Remember me
            </label>
        </div>-->
    <button class="btn btn-lg btn-primary btn-block" name="login" value="1" type="submit">Entrar</button>
</form>
