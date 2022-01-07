<?php
    class Employee{
        private $employeeId;
        private $firstName;
        private $lastName;
        private $email;
        private $cellPhone;
        private $position;
        private $password;
        private $picture;
        private $status;

        public function __construct(){
            $arguments = func_get_args();
            $numberOfArguments = func_num_args();

            if (method_exists($this, $function = '__construct'.$numberOfArguments)) {
                call_user_func_array(array($this, $function), $arguments);
            }
        }

        //This constructor is for adding employee
        public function __construct9($employeeId, $firstName, $lastName, $email, 
        $cellPhone, $position, $password, $picture = '', $status = "active"){
            $this->employeeId = $employeeId;
            $this->firstName = $firstName;
            $this->lastName = $lastName;
            $this->email = $email;
            $this->cellPhone = $cellPhone;
            $this->position = $position;
            $this->password = $password;
            $this->picture = $picture;
            $this->status = $status;
        }

        //This constructor is for updating employee
        public function __construct8($employeeId, $firstName, $lastName, $email, 
        $cellPhone, $position, $picture = '', $status = "active"){
            $this->employeeId = $employeeId;
            $this->firstName = $firstName;
            $this->lastName = $lastName;
            $this->email = $email;
            $this->cellPhone = $cellPhone;
            $this->position = $position;
            $this->picture = $picture;
            $this->status = $status;
        }

        public function __get($name){
            return $this->$name;
        }

        public function __destruct(){}
    }
    
?>