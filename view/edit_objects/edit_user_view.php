<div class="form-edit-container">
    <div class="left-container">
        <img src="src/checking.png" alt="No hay imagen">
        <div>
            <span><?php echo $_SESSION[$action][$position]->getCn(); ?></span>
   
        </div>
        <a href="index.php?controller=users&action=delete_ldap_object_to_domain&dn-object=<?php echo $_SESSION[$action][$position]->getDn(); ?>&control-action=users">Eliminar usuario</a>
    </div>
    <div class="right-container">
        <form action="index.php?controller=users&action=edit_object_to_domain&control-action=users" method="POST" enctype="multipart/form-data">
            <h1>Editar el usuario</h1>
            <div class="form_data">
                <p>DN del usuario</p>
                <input type="text" name="user-dn" value="<?php echo $_SESSION[$action][$position]->getDn(); ?>" require readonly>
            </div>
            <div class="form_data">
                <p>Nombre del usuario</p>
                
                <input type="text" name="firstname" value="<?php echo $_SESSION[$action][$position]->getGivenName(); ?>" require>
            </div>
            <div class="form_data">
                <p>Apellidos del usuario</p>
                <input type="text" name="lastname" value="<?php echo $_SESSION[$action][$position]->getSn(); ?>" require>
            </div>
            <div class="form_data">
                <p>Nombre completo del usuario</p>
                <input type="text" value="<?php echo $_SESSION[$action][$position]->getCn(); ?>" readonly>
            </div>
            <div class="form_data">
                <p>Numero de telefono</p>
                <input type="number" name="tel" value="<?php echo intval($_SESSION[$action][$position]->getTel()); ?>" require>
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
                <p>Cambiar de unidad organizativa</p>
                <select name="changeOU">
                    <option value="">Selecciona una nueva unidad organizativa</option>
                    
                    <?php
                        echo "<option value='dc=$dn[0],dc=$dn[1]'>Dominio</option>";
                        foreach($_SESSION['organizationalUnit'] as $value){
                            echo "<option value=".$value->getDn().">".$value->getCn()."</option>";
                        }

                    ?>
                </select>
            </div>
            <div class="form_data">
                <p>Selecciona una foto (250x250)</p>
                <input type="file" accept=".jpg" pattern=".+\.(jpg)$" name="photo">
                <p class="correct-message"></p>
            </div>
            <div class="form_data">
                <p>Introduce una dirección</p>
                <input type="text" name="address" value="<?php echo $_SESSION[$action][$position]->getHomePostalAddress(); ?>">
            </div>
            <div class="form_data">
                <input type="submit" name="submit">
                <input type="reset" name="reset">
            </div>
        </form>
    </div>
</div>