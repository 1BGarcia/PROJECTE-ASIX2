<?php

    namespace model;

    class Devices{

        private $dn;
        private $cn;
        private $description;
        private $ipHost;

        public function __construct(string $dn, string $cn, string $description, string $ipHost){
            $this->dn=$dn;
            $this->cn=$cn;
            $this->description=$description;
            $this->ipHost=$ipHost;
        }

        public function getDn(){
            return $this->dn;
        }

        public function getCn(){
            return $this->cn;
        }

        public function getDescription(){
            return $this->description;
        }

        public function getIpHost(){
            return $this->ipHost;
        }

    }

?>
