<?php

    use model\Authenticate;

    class login_controller{

        public function view_login(){
            require_once "view/login_view.php";
        }

        public function view_web_login(){
            require_once "view/login_web_view.php";
        }

        public function login_to_ldap(){
            if(isset($_GET['out'])) {
                // destroy session
                session_unset();
                $_SESSION = array();
                unset($_SESSION['user'],$_SESSION['access']);
                session_destroy();
            }

            if(isset($_POST['username-login'])){
                $dotenv=parse_ini_file(".env");
                $ldapHost = $dotenv["LDAP_SERVER_ROUTE"];
                $dn = explode('.', explode('@', $_POST['username-login'])[1]);
                $ldapOwnerGroup = "cn=owners,ou=groups,dc=$dn[0],dc=$dn[1]";

                $authenticate = new Authenticate($_POST['username-login'], $_POST['password-login'], $ldapHost, $ldapOwnerGroup);

                if($authenticate->authPerson())
                {
                   
                    // authentication passed
                    header("Location: index.php?controller=control_admin_panel&action=view_control_panel");

                    // header("Location: protected.php");
                    // die();
                } else {
                    // authentication failed
                    $error = 1;
                }
            }

            if (isset($error)) echo "Login Failed";
        }

        public function login_web_panel(){
            if(isset($_POST['username-login'])){
                $dotenv=parse_ini_file(".env");
                $ldapHost = "ldap://".$dotenv["LDAP_SERVER_ROUTE"];
                $dn = explode('.', explode('@', $_POST['username-login'])[1]);
                $ldapOwnerGroup = "cn=owners,ou=groups,dc=$dn[0],dc=$dn[1]";

                $authenticate = new Authenticate($_POST['username-login'], $_POST['password-login'], $ldapHost, $ldapOwnerGroup);

                if($authenticate->authWebPane()){
                    header("Location: index.php?controller=control_webserv_panel&action=view_webserv_panel");
                }else{
                    $error = 1;
                }
            }
            if (isset($error)) echo "Login Failed";
        }

        // Logout Session
        public function logout(){
            if (Authenticate::protected()){
                session_destroy();
                header("Location: index.php");
            }
        }


    }

?>