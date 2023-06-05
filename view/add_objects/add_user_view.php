<div class="add-form">
    <form action="index.php?controller=users&action=add_object_to_domain&control-action=users" method="POST" enctype="multipart/form-data">
        <h1>Añadir un usuario</h1>    
        <div class="form_data">
            <p>Nombre del usuario</p>
            <input type="text" name="firstname" require>
        </div>
        <div class="form_data">
            <p>Apellidos del usuario</p>
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
            <p>Introduce una dirección</p>
            <input type="text" name="address">
        </div>
        <div class="form_data">
            <input type="submit" name="submit">
            <input type="reset" name="reset">
        </div>
    </form>
</div>