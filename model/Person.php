<?php

    namespace model;

    class Person{

        private $cn;
        private $uid;
        private $uidNumber;
        private $jpegPhoto;
        private $tel;
        private $dn;
        private $status;
        private $sn;
        private $givenName;
        private $homePostalAddress;

        public function __construct(string $cn, string $uid, string $uidNumber, string $jpegPhoto, string $tel, string $dn, bool $status, string $sn, string $givenName, string $homePostalAddress){
            $this->cn=$cn;
            $this->uid=$uid;
            $this->uidNumber=$uidNumber;
            $this->jpegPhoto=$jpegPhoto;
            $this->tel=$tel;
            $this->dn=$dn;
            $this->status=$status;
            $this->sn=$sn;
            $this->givenName=$givenName;
            $this->homePostalAddress=$homePostalAddress;
        }

        public function getCn(){
            return $this->cn;
        }

        public function getUid(){
            return $this->uid;
        }

        public function getUidNumber(){
            return $this->uidNumber;
        }

        public function getJpegPhoto(){
            return $this->jpegPhoto;
        }

        public function getTel(){
            return $this->tel;
        }

        public function getDn(){
            return $this->dn;
        }

        public function getStatus(){
            return $this->status;
        }

        public function getSn(){
            return $this->sn;
        }

        public function getGivenName(){
            return $this->givenName;
        }

        public function getHomePostalAddress(){
            return $this->homePostalAddress;
        }

    }

?>