<?php

    use model\Authenticate;
    use model\Ldap;

    class users_controller{

        public function view_edit_page(){
            if(Authenticate::protected()){
                $dn = explode(".", $_SESSION['domain']);
                if(isset($_GET['current-object']) && isset($_GET['control-action'])){

                    $position = intval($_GET['current-object']);
                    $action = $_GET['control-action'];
                    $this->edit_ldap_object($dn, $position, $action);
                }
            }else{
                require_once "view/requests-messages/error_denied_access.php";

                header("Refresh: 5; url=index.php?controller=login&action=view_login");
            }
        }

        public function edit_ldap_object($dn, $position, $action){
            if($action == 'users'){
                foreach($_SESSION['organizationalUnit'] as $value){
                    $options .= "<option value='".$value->getCn()."'>".$value->getCn()."</option>";
                }

                // print_r($_SESSION[$_GET['control-action']][$position]->getGivenName());
                require_once "view/edit_objects/edit_user_view.php";     
            }else if($action == 'groups'){
                
                require_once "view/edit_objects/edit_groups_view.php";
                
            }else if($action == "organizationalUnit"){

                require_once "view/edit_objects/edit_ou_view.php";
            }else if($action == "policies"){

                require_once "view/edit_objects/edit_policy_view.php";
            }else if($action == 'devices'){

                require_once "view/edit_objects/edit_device_view.php";
            }
            
        }

        public function add_ldap_object(){
            if(Authenticate::protected()){
                if(isset($_GET['control-action'])){
                    $elementModify = array('users' => 'view/add_objects/add_user_view.php', 'groups' => 'view/add_objects/add_group_view.php', 'organizationalUnit' => 'view/add_objects/add_ou_view.php', 'policies' => 'view/add_objects/add_policy_view.php', 'devices'=>'view/add_objects/add_devices_view.php');

                    require_once $elementModify[$_GET['control-action']];
                }
            }else{
                require_once "view/requests-messages/error_denied_access.php";

                header("Refresh: 5; url=index.php?controller=login&action=view_login");
            }
        }

        public function add_object_to_domain(){
            if(Authenticate::protected()){
                if(isset($_GET['control-action']) && isset($_POST['submit'])){
                    if(isset($_POST['firstname']) && isset($_POST['lastname'])){$ldap = new Ldap($_POST['firstname'], $_POST['lastname']);}else{$ldap = new Ldap();}
                    
                    $ldap->addObjectLdap($_SESSION['ldapHostServer'], $_SESSION['usernameDNLdap'], base64_decode($_SESSION['password']), $_POST, $_GET['control-action'], $_SESSION['domain'], $_FILES['photo']['tmp_name']);
                }
            }else{
                require_once "view/requests-messages/error_denied_access.php";

                header("Refresh: 5; url=index.php?controller=login&action=view_login");
            }

        }

        public function edit_object_to_domain(){
            if(Authenticate::protected()){
                if(isset($_GET['control-action']) && isset($_POST['submit'])){
                    if(isset($_POST['firstname']) && isset($_POST['lastname'])){$ldap = new Ldap($_POST['firstname'], $_POST['lastname']);}else{$ldap = new Ldap();}

                    $ldap->editObjectLdap($_SESSION['ldapHostServer'], $_SESSION['usernameDNLdap'], base64_decode($_SESSION['password']), $_POST, $_GET['control-action'], $_SESSION['domain'], $_FILES['photo']['tmp_name']);
                }
            }else{
                require_once "view/requests-messages/error_denied_access.php";

                header("Refresh: 5; url=index.php?controller=login&action=view_login");
            }
        }

        public function delete_ldap_object_to_domain(){
            if(Authenticate::protected()){
                if(isset($_GET['control-action']) && isset($_GET['dn-object'])){
                    $ldap=new Ldap();
                    $ldap->deleteObjectLdap($_SESSION['ldapHostServer'], $_SESSION['usernameDNLdap'], base64_decode($_SESSION['password']), $_GET['dn-object'], $_GET['control-action'], $_SESSION['domain']);
                }
            }else{
                require_once "view/requests-messages/error_denied_access.php";

                header("Refresh: 5; url=index.php?controller=login&action=view_login");
            }
        }


    }

?>