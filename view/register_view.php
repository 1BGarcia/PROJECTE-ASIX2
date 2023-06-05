<div class="register-form">
<!-- ?controller=register_domain&action=register_to_server -->
    <form action="index.php?controller=register_domain&action=register_to_server" method="POST" enctype="multipart/form-data" id="signup_form">
        <div class="form_data">
            <p>Nombre del dominio</p>
            <input type="text" name="domain" require>
        </div>
        <div class="form_data">
            <p>Nombre del propietario</p>
            <input type="text" name="firstname" require>
        </div>
        <div class="form_data">
            <p>Apellidos del propietario</p>
            <input type="text" name="lastname" require>
        </div>
        <div class="form_data">
            <p>Numero de telefono</p>
            <input type="number" name="tel" require>
        </div>
        <div class="form_data">
            <p>Introduce una contraseña</p>
            <input type="password" name="password" require>
        </div>
        <div class="form_data">
            <p>Repite la contraseña</p>
            <input type="password" name="rep_password" require>
        </div>
        <div class="form_data">
            <p>Selecciona una foto (250x250)</p>
            <input type="file" accept=".jpg" pattern=".+\.(jpg)$" name="photo">
            <p class="correct-message"></p>
        </div>
        <div class="form_data">
            <p>Intrudice una dirección</p>
            <input type="text" name="address">
        </div>
        <div class="form_data">
            <input type="submit" name="submit">
            <input type="reset" name="reset">
        </div>
    </form>
</div>