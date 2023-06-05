<?php

    namespace model;

    use model\Connection;
    class Authenticate{

        private $username;
        private $password;
        private $ldapHost;
        private $ldapOwnersGroup;
        
        public function __construct(string $username, string $password, string $ldapHost, string $ldapOwnersGroup){
            $this->username=$username;
            $this->password=$password;
            $this->ldapHost=$ldapHost;
            $this->ldapOwnersGroup=$ldapOwnersGroup;
        }

        // Methods

        public function authPerson(){
            if(empty($this->username) || empty($this->password)) return false;

            $dn = explode('.', explode('@', $this->username)[1]);
            $username = "cn=$this->username,dc=$dn[0],dc=$dn[1]";
            $dnUidAdmin = "uid=$this->username,ou=users,dc=$dn[0],dc=$dn[1]";

            $ldapBaseDn = "ou=users,dc=$dn[0],dc=$dn[1]";

            // Conexion Ldap
            // $ldapConn = ldap_connect("192.168.21.107");

            // if(!$ldapConn) die("Error al conectar con el servidor LDAP");

            // ldap_set_option($ldapConn, LDAP_OPT_PROTOCOL_VERSION,3);
            // ldap_set_option($ldapConn, LDAP_OPT_REFERRALS,0);
            // ldap_set_option($ldapConn, LDAP_OPT_X_TLS_REQUIRE_CERT, 0);
            // ldap_set_option($ldapConn, LDAP_OPT_REFERRALS, 0);
            // ldap_start_tls($ldapConn);


            // Generar inicio de session
            // $bind = ldap_bind($ldapConn, $username, $this->password);
            $conn = new Connection();

            if($conn->binding($username, $this->password)) {

                // ldap_unbind($ldapConn);
                $conn->unbind();
                $_SESSION['username'] = $this->username;
                $_SESSION['usernameDNLdap'] = $username;
                $_SESSION['dnUidUserAdmin'] = $dnUidAdmin;
                $_SESSION['access'] = 1;
                $_SESSION['password'] = base64_encode($this->password);
                $_SESSION['domain']="$dn[0].$dn[1]";
                $_SESSION['ldapHostServer']=$this->ldapHost;
                $_SESSION['users']=[];
                $_SESSION['groups']=[];
                $_SESSION['policies']=[];
                $_SESSION['organizationalUnit']=[];
                $_SESSION['devices']=[];
                $_SESSION['isLdapAdmin']=true;

                return true;
            }
            else{
                return false;
            }
        }

        public function authWebPane(){
            if(empty($this->username) || empty($this->password)) return false;
            
            $dn = explode('.', explode('@', $this->username)[1]);
            $username = "uid=$this->username,ou=users,dc=$dn[0],dc=$dn[1]";
            
            $conn = new Connection();

            if($conn->binding($username, $this->password)){

                $filter = "(memberUid=$this->username)";

                
                $group = $conn->search($this->ldapOwnersGroup, $filter, ['memberUid']);
                $conn->unbind();
                if($group[0]['memberuid']['count'] != 0){
                    
                    $_SESSION['username'] = $this->username;
                    $_SESSION['domain']="$dn[0].$dn[1]";

                    $_SESSION['access'] = 1;

                    $_SESSION['isOwner']=false;

                    return true;
                }
            }else{
                return false;
            }


        }

        public static function protected(){

            if(!$_SESSION['isLdapAdmin']){
                if($_SESSION['access'] == 1) return true;
            }
            else if(isset($_SESSION['username']) && isset($_SESSION['usernameDNLdap']) && isset($_SESSION['access']) && isset($_SESSION['password']) && isset($_SESSION['domain']) && isset($_SESSION['ldapHostServer']) && isset($_SESSION['users']) && isset($_SESSION['groups']) && isset($_SESSION['policies']) && isset($_SESSION['organizationalUnit']) && isset($_SESSION['devices'])){
                if($_SESSION['access'] == 1) return true;
            }else{
                return false;
            }

        }

    }

?>