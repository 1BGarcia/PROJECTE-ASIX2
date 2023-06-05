<div class="login-form">
    <form action="index.php?controller=login&action=login_to_ldap" method="POST" enctype="multipart/form-data">
        <h1>Iniciar sesión en panel LDAP</h1>
        <div class="form_data">
            <p>Nombre de usuario</p>
            <p>Inicia sesión introduciendo tu "nombre.apellido@dominio"</p>
            <input type="text" name="username-login" require>
        </div>
        <div class="form_data">
            <p>Contraseña</p>
            <input type="password" name="password-login" require>
        </div>
        <div class="form_data">
            <input type="submit" name="submit">
            <input type="reset" name="reset">
        </div>
    </form>
</div>