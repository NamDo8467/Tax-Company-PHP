<?php 
    class LogManager{
       private $logList;

        function __construct(){
        }

        // Get all the log 
        function getLogList(){
            global $db_connection;
            $query = 'SELECT * FROM log_activity;';
            $data = $db_connection->query($query);
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


        function addLog($log){
            global $db_connection;
            $query = "INSERT INTO log_activity (employee_id, module_name, action, date_time, ip) VALUES ('$log->employeeID', '$log->moduleName', '$log->action', '$log->dateTime', '$log->ip');";
            $result = $db_connection->query($query);   
        }

        function __destruct() {}
    }    
?>