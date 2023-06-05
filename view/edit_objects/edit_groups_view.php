<div class="form-edit-container">
    <div class="left-container">
        <div>
            <span><?php echo $_SESSION[$action][$position]->getCn(); ?></span>
            <div class="status-user"></div>
        </div>
        <a href="index.php?controller=users&action=delete_ldap_object_to_domain&dn-object=<?php echo $_SESSION[$action][$position]->getDn(); ?>&control-action=organizationalUnit">Eliminar grupo</a>
    </div> 
    <div class="right-container">
        <form action="index.php?controller=users&action=edit_object_to_domain&control-action=groups" method="POST" enctype="multipart/form-data">
            <h1>Editar el grupo</h1>
            <div class="form_data">
                <p>DN del grupo</p>
                <input type="text" name="group-dn" value="<?php echo $_SESSION[$action][$position]->getDn(); ?>" require readonly >
            </div>
            <div class="form_data">
                <p>Nombre de grupo</p>
                <?php

                    if($_SESSION[$action][$position]->getCn() == "owners"){
                        echo '<input type="text" name="name-group" value="'.$_SESSION[$action][$position]->getCn().'" require readonly>';
                    }else{
                        echo '<input type="text" name="name-group" value="'.$_SESSION[$action][$position]->getCn().'" require>';
                    }

                ?>
                
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
                <p>Eliminar usuarios del grupo</p>
                
                <select name="delUsers[]" multiple>
                    <?php

                        for($i=0; $i<$_SESSION[$action][$position]->getMemberUid()['count']; $i++){
                            echo "<option value='".$_SESSION[$action][$position]->getMemberUid()[$i]."'>".$_SESSION[$action][$position]->getMemberUid()[$i]."</option>";
                        }
                    ?>
                </select>
            </div>
            <div class="form_data">
                <p>Añadir usuarios al grupo.</p>

                <select name="users[]" id="" multiple>
                    <?php
                    
                            // echo "<option value='".$value->getUid()."'>".$value->getCn()."</option>";

                        foreach($_SESSION['users'] as $value){
                            if($_SESSION['dnUidUserAdmin'] != $value->getDn()){
                                if (!in_array($value->getUid(), $_SESSION[$action][$position]->getMemberUid())) {
                                    echo "<option value='".$value->getUid()."'>".$value->getUid()."</option>";
                                }
                            // if($_SESSION[$action][$position]->getMemberUid()[$i] != $value->getUid()){
                            //     echo "<option value='".$value->getUid()."'>".$value->getCn()."</option>";
                            // }
                            }
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