<?php 
    class Log{
        private $employeeID;
        private $moduleName;
        private $action;
        private $dateTime;
        private $ip;

        function __construct($employeeID, $moduleName, $action, $dateTime, $ip){
            $this->employeeID = $employeeID;
            $this->moduleName = $moduleName;
            $this->action = $action;
            $this->dateTime = $dateTime;
            $this->ip = $ip;
        }

        public function __get($name){
            return $this->$name;
        }

        function __destruct() {}
    }
    
?>