<?php

    namespace model;

    class OrganizationalUnit{
        private $cn;
        private $dn;
        private $description;
        private $objects;

        public function __construct(string $cn, string $dn, string $description, array $objects){
            $this->cn=$cn;
            $this->dn=$dn;
            $this->description=$description;
            $this->objects=$objects;
        }

        public function getCn(){
            return $this->cn;
        }

        public function getDn(){
            return $this->dn;
        }

        public function getDescription(){
            return $this->description;
        }
        
        public function getObjectsOu(){
            return $this->objects;
        }
    }

?>