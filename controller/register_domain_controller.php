<?php

    use model\Zone;
    use model\Ldap;
    use model\Apache;
    // session_start();
    class register_domain_controller{

        public function view_register(){
            require_once "view/register_view.php";
        }

        public function register_to_server(){
            $domainName = $_POST['domain'];
            $firstname = $_POST['firstname'];
            $lastname = $_POST['lastname'];
            $password = $_POST['password'];
            $tel = $_POST['tel'];
            $address = $_POST['address'];

            $routePhoto = $this->save_file_to_server($firstname);

            $zone = new Zone($domainName, 3600, "/etc/bind/zones");
            $apache=new Apache($domainName);
            $registerLdapDomain = new Ldap($firstname, $lastname, $password, $domainName, $routePhoto, $tel, $address);

            //Comprobar que los nombres de dominio no coincidan
            if ($zone->getZones($domainName)){
                if($response != ""){
                    require_once "view/requests-messages/error_domain_register.php";
                    header("Refresh: 5; url=index.php?controller=register_domain&action=view_register");
                }else{
                    if($apache->createStructure() && $apache->configureVirtualHost()){
                        // Crear dominio la zona DNS de bind9
                        $zone->addZone();
                        //Crear la olcDataBase de dominio ldap
                        $response = $registerLdapDomain->addNewLdapDomain();

                        require_once "view/requests-messages/check_message.php";

                        header("Refresh: 5; url=index.php");   
                    }else{
                        echo "Ha ocurrido un error al crear la zona";
                        // header("Refresh: 5; url=index.php");   
                    }
                }
            }else{
                $response = "El dominio que intenta registrar ya existe en el servidor";
                require_once "view/requests-messages/error_domain_register.php";

                header("Refresh: 5; url=index.php?controller=register_domain&action=view_register");
            }

            

            // Crear un perfil de inicio de sesion al usuario owner
        
        
        }

        public function get_extension($country){
            $extension = array(
                "Argentina" => "+54",
                "Bolivia" => "+591",
                "Chile" => "+56",
                "Colombia" => "+57",
                "Costa Rica" => "+506",
                "Cuba" => "+53",
                "Ecuador" => "+593",
                "El Salvador" => "+503",
                "España" => "+34",
                "Guatemala" => "+502",
                "Honduras" => "+504",
                "México" => "+52",
                "Nicaragua" => "+505",
                "Panamá" => "+507",
                "Paraguay" => "+595",
                "Perú" => "+51",
                "Puerto Rico" => "+1",
                "República Dominicana" => "+1",
                "Uruguay" => "+598",
                "Venezuela" => "+58"
            );

            return $extension[$country];
        }

        public function save_file_to_server(string $userFirstname){

            $route = "user_photos";
            $image = $_FILES['photo']['tmp_name'];
            if (!file_exists($route)){
                mkdir($route, 0777, true);
            }
            
            $routePhoto = "$route/".strtolower(str_replace(' ', '', $userFirstname)).uniqid().".jpg";
            move_uploaded_file($image, $routePhoto);

            return $routePhoto;
        }

    }

?>
