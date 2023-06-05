<div class="form-edit-container">
    <div class="left-container">
        <div>
            <span><?php echo $_SESSION[$action][$position]->getCn(); ?></span>
            <div class="status-user"></div>
        </div>
        <a href="index.php?controller=users&action=delete_ldap_object_to_domain&dn-object=<?php echo $_SESSION[$action][$position]->getDn(); ?>&control-action=organizationalUnit">Eliminar unidad organizativa</a>
    </div> 
    <div class="right-container">
        <form action="index.php?controller=users&action=edit_object_to_domain&control-action=organizationalUnit" method="POST" enctype="multipart/form-data">
            <h1>Editar la unidad organizativa</h1>
            <div class="form_data">
                <p>DN de la unidad organizativa</p>
                <input type="text" name="ou-dn" value="<?php echo $_SESSION[$action][$position]->getDn(); ?>" require readonly >
            </div>
            <div class="form_data">
                <p>Nombre de la unidad organizativa</p>
                <?php
                    if($_SESSION[$action][$position]->getCn() == "users" || $_SESSION[$action][$position]->getCn() == "groups" || $_SESSION[$action][$position]->getCn() == "computers" || $_SESSION[$action][$position]->getCn() == "policy"){
                        echo "<p>No se puede cambiar el nombre a esta unidad organizativa</p>";
                        echo '<input type="text" name="name-ou" value="'.$_SESSION[$action][$position]->getCn().'" require readonly>';
                    }else{
                        echo '<input type="text" name="name-ou" value="'.$_SESSION[$action][$position]->getCn().'" require>';
                    }
                    // if($_SESSION[$action][$position]->getCn() != "users" || $_SESSION[$action][$position]->getCn() != "groups" || $_SESSION[$action][$position]->getCn() != "computers" || $_SESSION[$action][$position]->getCn() != "policy"){
                    //     echo '<input type="text" name="name-ou" value="'.$_SESSION[$action][$position]->getCn().'" require>';
                    // }else{
                    //     echo $_SESSION[$action][$position]->getCn();
                    //     echo '<input type="text" name="name-ou" value="'.$_SESSION[$action][$position]->getCn().'" require readonly>';
                    // }
                ?>
            </div>
            <div class="form_data">
                <p>Descripción</p>
                <input type="text" name="description-ou" value="<?php echo $_SESSION[$action][$position]->getDescription(); ?>" require>
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
                <p>DN de los objectos de la unidad organizativa</p>            
                <select multiple disabled>
                    <?php
                        // echo "<pre>";
                        // print_r($_SESSION[$action][$position]->getObjectsOu());
                        // echo "</pre>";
                        for($i=1; $i<$_SESSION[$action][$position]->getObjectsOu()['count']; $i++){
                            echo "<option value='".$_SESSION[$action][$position]->getObjectsOu()[$i]['dn']."'>".$_SESSION[$action][$position]->getObjectsOu()[$i]['dn']."</option>";
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