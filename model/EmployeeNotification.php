<?php
    class EmployeeNotification{
        private $notificationName;
        private $notificationType;
        private $notificationStatus;
        

        public function __construct($notificationName, $notificationType, $notificationStatus = "disabled"){
            $this->notificationName = $notificationName;
            $this->notificationType = $notificationType;
            $this->notificationStatus = $notificationStatus;
        }

        public function __get($name){
            return $this -> $name;
        }
        public function __destruct(){

        }
    }
    
?>