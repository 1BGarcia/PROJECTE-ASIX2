<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proyecto ASIX 2022-2023</title>
    <link rel="stylesheet" href="css/app_styles.css">
    <link rel="stylesheet" href="css/messages.css">
</head>
<?php

    require_once 'autoloader.php';
    // dl('ssh2.so');
    session_start();
    use model\Authenticate;
?>
<body>
    <header>
        <ul>
            <li>
                <a href="index.php">Inicio</a>
            </li>
            <?php 
                if (Authenticate::protected()){
                    if($_SESSION['isLdapAdmin']){
                        $url = $_SERVER['REQUEST_URI'];
                        $url_parts = parse_url($url);
                        $query_params = array();
                        parse_str($url_parts['query'], $query_params);
                        if(isset($query_params['control-action'])){
                            echo "<li><a href='index.php?controller=control_admin_panel&action=view_control_panel&control-action=".$query_params['control-action']."'>Panel de administrador</a></li>";
                        }else{
                            echo "<li><a href='index.php?controller=control_admin_panel&action=view_control_panel'>Panel de administrador</a></li>";
                        }
                    }else{
                        echo "<li><a href='index.php?controller=control_webserv_panel&action=view_webserv_panel'>Panel de administrador</a></li>";
                    }
                    echo "<li><a href='index.php?controller=login&action=logout'>Cerrar sesión</a></li>";
                }else{
                    echo "<li><a href='index.php?controller=login&action=view_web_login'>Inicio de sesión servidor web</li>";
                    echo "<li><a href='index.php?controller=login&action=view_login'>Inicio de sesión LDAP</a></li>";
                }
        
            ?>
        </ul>
    </header>
    <main>
    <?php
        if(isset($_GET['controller'])){
            $name_controller = $_GET['controller'].'_controller';
        }
        else{
            $name_controller = "main_controller";
        }
            
        if(class_exists($name_controller)){
            $controller = new $name_controller();
                
            if(isset($_GET['action']) && method_exists($controller, $_GET['action'])){
                $action = $_GET['action'];
                switch($action){
                    case 'view_register':
                        $controller->$action();
                        break;
                    case 'register_to_server':
                        $controller->$action();
                        break;
                    case 'view_login':
                        $controller->$action();
                        break;
                    case 'view_web_login':
                        $controller->$action();
                        break;
                    case 'login_to_ldap':
                        $controller->$action();
                        break;
                    case 'login_web_panel':
                        $controller->$action();
                        break;
                    case 'view_control_panel':
                        $controller->$action();
                        break;
                    case 'view_webserv_panel':
                        $controller->$action();
                        break;
                    case 'view_edit_page':
                        $controller->$action();
                        break;
                    case 'add_ldap_object':
                        $controller->$action();
                        break;
                    case 'add_object_to_domain':
                        $controller->$action();
                        break;
                    case 'edit_object_to_domain':
                        $controller->$action();
                        break;
                    case 'delete_ldap_object_to_domain':
                        $controller->$action();
                        break;
                    case 'logout':
                        $controller->$action();
                        break;
                }
            }else{
                $action = "view_main";
                $controller->$action();
            }
        }
        else{
            require_once "view/requests-messages/error_denied_access.php";
            header("Refresh: 5; url=index.php?controller=login&action=view_login");
        }
    ?>
    </main>
    <footer>
        <p>Proyecto ASIX Marc & Bryan</p>
    </footer>
    <script src="script/webserv_panel.js"></script>
    <script src="script/app_script.js"></script>
</body>
</html>
