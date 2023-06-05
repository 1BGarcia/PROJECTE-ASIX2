<div class="control_panel">
    <div class="control-panel-menu">
        <a href="index.php?controller=control_admin_panel&action=view_control_panel&control-action=users">Usuarios</a>
        <a href="index.php?controller=control_admin_panel&action=view_control_panel&control-action=groups">Grupos</a>
        <a href="index.php?controller=control_admin_panel&action=view_control_panel&control-action=devices">Dispositivos</a>
        <a href="index.php?controller=control_admin_panel&action=view_control_panel&control-action=organizationalUnit">Unidades Organizativas</a>
        <!-- <a href="">Borrar dominio</a> -->
    </div>
    <div class="control-panel-data">
        <div>
            <?php
                if(isset($_GET['control-action'])){
                    if($_GET['control-action']=='users'){
                        echo '<a href="index.php?controller=users&action=add_ldap_object&control-action='.$_GET['control-action'].'">Añadir usuario</a>';
                    }else if($_GET['control-action']=='groups'){
                        echo '<a href="index.php?controller=users&action=add_ldap_object&control-action='.$_GET['control-action'].'">Añadir grupo</a>';
                    }else if($_GET['control-action']=='organizationalUnit'){
                        echo '<a href="index.php?controller=users&action=add_ldap_object&control-action='.$_GET['control-action'].'">Añadir unidad organizativa</a>';
                    }else if($_GET['control-action']=='policies'){
                        echo '<a href="index.php?controller=users&action=add_ldap_object&control-action='.$_GET['control-action'].'">Añadir politicas</a>';
                    }else if($_GET['control-action']=='devices'){
                        echo '<a href="index.php?controller=users&action=add_ldap_object&control-action='.$_GET['control-action'].'">Añadir dispositivos</a>';
                    }
                }
            ?>
        </div>
        <ul>
            <?php

                // print_r($_SESSION['groups']);

                if(!isset($resultInfoLdap)){
                    echo '
                        <h1>¿Que datos quieres ver?</h1>                         
                    ';
                }else{
                    // print_r($resultInfoLdap);
                    if($_GET['control-action'] == 'users'){
                        foreach($resultInfoLdap as $key => $value){
                            if($_SESSION['dnUidUserAdmin'] != $value->getDn()){
                                echo '
                                <a href="index.php?controller=users&action=view_edit_page&current-object='.$key.'&control-action='.$_GET['control-action'].'">
                                    <li class="element-res">
                                        <img src="'.$value->getJpegPhoto().'" alt="">
                                        <span>'.$value->getCn().'</span>
                                    </li>
                                </a>
                                ';
                            }
                        }
                    }else{

                        foreach($resultInfoLdap as $key => $value){
                            echo '
                            <a href="index.php?controller=users&action=view_edit_page&current-object='.$key.'&control-action='.$_GET['control-action'].'">
                                <li class="element-res">
                                    <span>'.$value->getCn().'</span>
                                </li>
                            </a>
                            ';
                        }
                    }
                }
            ?>
        </ul>
    </div>
</div>