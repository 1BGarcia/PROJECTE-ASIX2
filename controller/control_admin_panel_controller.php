<?php

    use model\Authenticate;
    use model\Ldap;
    
    class control_admin_panel_controller{
        

        public function view_control_panel(){
            if(Authenticate::protected()){
                $this->get_users();
                $this->get_groups();
                $this->get_organizational_unit();
                $this->get_devices();

                switch($_GET['control-action']){
                    case 'users':
                        $resultInfoLdap = $_SESSION['users'];
                        
                        require_once "view/control_panel/control_admin_panel_view.php";
                        break;
                    case 'groups':
                        $resultInfoLdap = $_SESSION['groups'];
                        
                        require_once "view/control_panel/control_admin_panel_view.php";
                        break;
                    case 'policies':
                        $resultInfoLdap = $_SESSION['policies'];

                        require_once "view/control_panel/control_admin_panel_view.php";
                        break;
                    case 'devices':
                        $resultInfoLdap = $_SESSION['devices'];

                        require_once "view/control_panel/control_admin_panel_view.php";
                        break;
                    case 'organizationalUnit':
                        $resultInfoLdap = $_SESSION['organizationalUnit'];

                        require_once "view/control_panel/control_admin_panel_view.php";
                        break;
                    default:
                        require_once "view/control_panel/control_admin_panel_view.php";
                        break;
                }

            }else{
                require_once "view/requests-messages/error_denied_access.php";

                header("Refresh: 5; url=index.php?controller=login&action=view_login");
            }
        }

        // public function delete_domain(){

        // }

        public function get_users(){
            $dn = explode(".", $_SESSION['domain']);
            Ldap::getUsersLdap($_SESSION['ldapHostServer'], $_SESSION['usernameDNLdap'], base64_decode($_SESSION['password']), "dc=$dn[0],dc=$dn[1]");
        }

        // Obtener grupos del dominio del usuario actual.
        public function get_groups(){
            $dn = explode(".", $_SESSION['domain']);
            Ldap::getGroupsLdap($_SESSION['ldapHostServer'], $_SESSION['usernameDNLdap'], base64_decode($_SESSION['password']), "dc=$dn[0],dc=$dn[1]");
        }

        public function get_organizational_unit(){
            $dn = explode(".", $_SESSION['domain']);
            Ldap::getOrgUnitLdap($_SESSION['ldapHostServer'], $_SESSION['usernameDNLdap'], base64_decode($_SESSION['password']), "dc=$dn[0],dc=$dn[1]");
        }

        public function get_devices(){
            $dn = explode(".", $_SESSION['domain']);
            Ldap::getDevicesLdap($_SESSION['ldapHostServer'], $_SESSION['usernameDNLdap'], base64_decode($_SESSION['password']), "dc=$dn[0],dc=$dn[1]");
        }
    }

?>