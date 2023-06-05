<?php
    namespace model;
    class Connection{

        private $ldap_addr;
        private $ds;

        public function __construct(){
            $this->ldap_addr = "ldap://192.168.21.107";
            $this->ds = $this->setConnex();
        }

        public function setConnex(){
            $ds = ldap_connect($this->ldap_addr);

            if(!$ds) die("Error al conectar con el servidor LDAP");

            ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION,3);
            ldap_set_option($ds, LDAP_OPT_REFERRALS,0);

            return $ds;
        }

        public function binding($username, $password){
            
            // ldap_set_option($ldapConn, LDAP_OPT_X_TLS_REQUIRE_CERT, 0);
            // ldap_set_option($ldapConn, LDAP_OPT_REFERRALS, 0);
            // ldap_start_tls($ldapConn);
            if($bind = ldap_bind($this->ds, $username, $password)){
                return $bind;
            }
        }
        
        public function search($dn, $filter=null, $attr=[]){
            return ldap_get_entries($this->ds, ldap_search($this->ds, $dn, $filter, $attr));
        }

        public function unbind(){
            ldap_unbind($this->ds);
        }

    }

?>