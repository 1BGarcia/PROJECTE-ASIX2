<div class="form-edit-container">
    <div class="left-container">
        <div>
            <span><?php echo $_SESSION[$action][$position]->getCn(); ?></span>
            <div class="status-user"></div>
        </div>
        <a href="index.php?controller=users&action=delete_ldap_object_to_domain&dn-object=<?php echo $_SESSION[$action][$position]->getDn(); ?>&control-action=devices">Eliminar dispositivo</a>
    </div> 
    <div class="right-container">
        <form action="index.php?controller=users&action=edit_object_to_domain&control-action=devices" method="POST" enctype="multipart/form-data">
            <h1>Editar dispositivos</h1>
            <div class="form_data">
                <p>DN del dispositivo</p>
                <input type="text" name="devices-dn" value="<?php echo $_SESSION[$action][$position]->getDn(); ?>" require readonly >
            </div>
            <div class="form_data">
                <p>Nombre del dispositivo</p>
                <input type="text" name="devices-name" value="<?php echo $_SESSION[$action][$position]->getCn(); ?>" require>
            </div>
            <div class="form_data">
                <p>Descripción</p>
                <input type="text" name="description-devices" value="<?php echo $_SESSION[$action][$position]->getDescription(); ?>" require>
            </div>
            <div class="form_data">
                <p>Dirección IP</p>
                <input type="text" name="iphost-devices" value="<?php echo $_SESSION[$action][$position]->getIpHost(); ?>" require>
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
                <input type="submit" name="submit">
                <input type="reset" name="reset">
            </div>
        </form>
    </div>
</div>