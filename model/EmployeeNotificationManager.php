<?php
    class EmployeeNotificationManager{
        private $notificationList;

        public function __construct(){
            $this->notificationList = array();
        }

        public function getEmployeeNotificationList(){
            global $db_connection;
            $query = "SELECT * FROM employee_notification;";
            try {
                $data = $db_connection->query($query);
                $result = $data->fetch_all(MYSQLI_ASSOC);
                return $result;
            } catch (Exception $ex) {
                echo $ex->getMessage();
            }catch(Error $er){
                echo $er->getMessage();
            }
        }
        public function addEmployeeNotification($employeeNotification){
            global $db_connection;
            $query = "INSERT INTO employee_notification (name, type, stt) VALUES('$employeeNotification->notificationName',
            '$employeeNotification->notificationType', '$employeeNotification->notificationStatus');";
            try {
                $result = $db_connection->query($query);
                return $result;
            } catch (Exception $ex) {
                echo $ex->getMessage();
            }catch(Error $er){
                echo $er->getMessage();
            }

            
        }
        
        public function updateEmployeeNotification($id, $name, $type, $status = 'disabled'){
            global $db_connection;
            $query = "UPDATE employee_notification SET name = '$name', type = '$type', stt = '$status' WHERE id = $id;";
            try {
                
                $result = $db_connection->query($query);
                return $result;
            } catch (Exception $ex) {
                echo $ex->getMessage();
            }catch(Error $er){
                echo $er->getMessage();
            }
        }

        public function deleteEmployeeNotification($id){
            global $db_connection;
            $query = "DELETE FROM employee_notification WHERE id = $id;";
            try {
                $result = $db_connection->query($query);
                return $result;
            } catch (Exception $ex) {
                echo $ex->getMessage();
            }catch(Error $er){
                echo $er->getMessage();
            }
        }

        public function searchEmployeeNotificationByName($name){
            global $db_connection;
            $query = "SELECT * FROM employee_notification WHERE name = '$name';";
            try {
                $result = $db_connection->query($query);
                return $result;
            } catch (Exception $ex) {
                echo $ex->getMessage();
            }catch(Error $er){
                echo $er->getMessage();
            }
        }
        public function __get($name){
            return $this->notificationList;
        }

        public function __destruct(){}
    }
    
?>