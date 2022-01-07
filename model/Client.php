<?php
    class Client{
        private $clientId;
        private $companyName;
        private $businessNumber;
        private $firstName;
        private $lastName;
        private $phoneNumber;
        private $cellphone;
        private $carrier;
        private $hst;
        private $website;
        private $status;

        public function __construct($clientId, $companyName, $businessNumber, $firstName, $lastName,$phoneNumber,$cellphone,$carrier,
        $hst = "",$website="",$status="active"){
            $this -> clientId = $clientId;
            $this -> companyName = $companyName;
            $this -> businessNumber = $businessNumber;
            $this -> firstName = $firstName;
            $this -> lastName = $lastName;
            $this -> phoneNumber = $phoneNumber;
            $this -> cellphone = $cellphone;
            $this -> carrier = $carrier;
            $this -> hst = $hst;
            $this->website = $website;
            $this -> status = $status;
        }

        public function __get($name){
            return $this->$name;
        }

        public function __destruct(){

        }
    }
?>