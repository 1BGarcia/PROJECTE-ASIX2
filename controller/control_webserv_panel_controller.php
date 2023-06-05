<?php

    use model\Apache;
    use model\Authenticate;

    class control_webserv_panel_controller{

        public function view_webserv_panel(){

            if(Authenticate::protected()){
                $apache = new Apache($_SESSION['domain']);

                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    
                    if(isset($_POST['isDeleteFile']) && is_file($_POST['routesFile'])){
                        shell_exec("rm ".$_POST['routesFile']);
                    }
                    // Obtener datos del formulario
                    if(is_dir($_POST['routesDir'])){
                        if(isset($_POST['isDeleteDir'])){
                            shell_exec("rm -r ".$_POST['routesDir']);
                        }

                        if($_POST['newName']!=""){
                            if(isset($_POST['isCreate'])){
                                shell_exec("mkdir ".$_POST['routesDir']."/".$_POST['newName']);
                            }else{
                                $newRoute = rtrim(dirname($_POST['routesDir']), "/");
                                shell_exec("mv ".$_POST['routesDir']." $newRoute/".$_POST['newName']."");
                            }
                        }else{
                            $apache->updateFile($_POST['routesDir']."/", $_FILES['filesGet']);
                        }   
                    }
                }

        
                require_once "view/control_webserv/control_webserv_view.php";
            }else{
                require_once "view/requests-messages/error_denied_access.php";
                header("Refresh: 5; url=index.php?controller=login&action=view_login");
            }
        }
    }

?>