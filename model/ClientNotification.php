<?php
    class ClientNotification{
        private $clientId;
        private $notificationId;
        private $date;
        private $frequency;
        private $status;

        public function __construct($clientId, $notificationId, $date, $frequency, $status = "active"){
            $this->clientId = $clientId;
            $this->notificationId = $notificationId;
            $this->date = $date;
            $this->frequency = $frequency;
            $this->status = $status;
        }

        public function __get($name){
            return $this -> $name;
        }
        public function __destruct(){

        }

        
    }
   
?>