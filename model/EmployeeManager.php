<?php
    class EmployeeManager{
        private $employeeList;
        
        public function __construct(){
            $this->employeeList = array();
        }

        public function getEmployeeList(){
            global $db_connection;
            $query = "SELECT * FROM employee;";
            $data = $db_connection->query($query);
            $employeeList = $data->fetch_all(MYSQLI_ASSOC);
            return $employeeList;
        }

        
        public function addEmployee($employee){
            global $db_connection;
            $query = "INSERT INTO employee VALUES('$employee->employeeId','$employee->firstName', '$employee->lastName','$employee->password','$employee->email',
            '$employee->cellPhone', '$employee->position', '$employee->picture', '$employee->status');";

            try {
                $result = $db_connection->query($query);
                return $result;
            } catch (Exception $e) {
                return false;
            }
        }
        
        public function updateEmployeeInformation($employee){
            $employeeList = $this->getEmployeeList();
            global $db_connection;
            $query = "UPDATE employee SET first_name = '$employee->firstName', 
                        last_name = '$employee->lastName', email = '$employee->email', cell_phone = '$employee->cellPhone',
                        position = '$employee->position', status = '$employee->status'";
            foreach ($employeeList as $e) {
                if($e['employee_id'] == $employee->employeeId){
                    if($employee->picture != ''){
                        
                        $query = $query . ", picture = '$employee->picture'";
                    }
                    $query = $query . " WHERE employee_id = '$employee->employeeId';";
                    
                    try {   
                        // echo $query;
                        $result = $db_connection->query($query);
                        return $result;
                    } catch (Exception $e) {
                        return $e.getMessage();
                    }
                    
                }
            }
            
        }

        public function getEmployeeDetail($employeeId){
            $employeeList = $this->getEmployeeList();
            global $db_connection;
            foreach ($employeeList as $employee) {
                if($employee['employee_id'] == $employeeId){
                    $query = "SELECT * FROM employee WHERE employee_id = '$employeeId';";
                    try {
                        $data = $db_connection->query($query); 
                        $result = $data ->fetch_all(MYSQLI_ASSOC);
                        return $result;
                    } catch (Exception $e) {
                        return "Error";
                    }
                }
            }
        }
        
        public function deleteEmployee($employeeId){
            global $db_connection;
            $query = "DELETE FROM employee WHERE employee_id = '$employeeId';";
            try {
                $result = $db_connection->query($query);
                return true;
            } catch (Exception $e) {
                return false;
            }
        }

        public function searchByLastName($lastName){
            global $db_connection;
            $query = "SELECT * FROM employee WHERE last_name = '$lastName';";
            $data = $db_connection->query($query);
            $employeeList = $data->fetch_all(MYSQLI_ASSOC);
            if($employeeList){
                return $employeeList;
            }else{
                return false;
            }
            
        }
        public function updateStatus($employeeId, $status){
            foreach($this->employeeList as $employee){
                if($employee->employeeId == $employeeId){
                    $employee->status = $status;
                    break;
                }
                
            }
        }
        public function __destruct(){}
    }
    
?>