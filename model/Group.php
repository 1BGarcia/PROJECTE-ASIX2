<?php
    namespace model;

    class Group{

        private $cn;
        private $gidNumber;
        private $memberUid;
        private $dn;

        public function __construct(string $cn, string $gidNumber, array $memberUid, string $dn){
            $this->cn=$cn;
            $this->gidNumber=$gidNumber;
            $this->memberUid=$memberUid;
            $this->dn=$dn;
        }

        public function getCn(){
            return $this->cn;
        }

        public function getGidNumber(){
            return $this->gidNumber;
        }

        public function getMemberUid(){
            return $this->memberUid;
        }

        public function getDn(){
            return $this->dn;
        }

    }
?>