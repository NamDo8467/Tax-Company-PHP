<?php
    class ClientNotificationManager{
        private $notificationList;

        public function __construct(){
            $this->notificationList = array();
        }

        public function getClientNotificationList(){
            global $db_connection;
            $query = "SELECT * FROM client_notification;";

            $date = $db_connection->query($query);
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
        public function addClientNotification($clientNotification){
            global $db_connection;
            $deleteQuery = "DELETE FROM client_notification WHERE client_id = '$clientNotification->clientId';";
            $db_connection->query($deleteQuery);

            $updateClientStatusQuery = "UPDATE client SET stt = '$clientNotification->status' WHERE id = '$clientNotification->clientId';";
            $db_connection->query($updateClientStatusQuery);

            $query = "INSERT INTO client_notification VALUES('$clientNotification->notificationId',
            '$clientNotification->clientId', DATE '$clientNotification->date', 
            '$clientNotification->frequency', '$clientNotification->status');";
            try {

                $result = $db_connection->query($query);
                return $result;
            } catch (Exception $ex) {
                echo $ex->getMessage();
            }catch(Error $er){
                echo $er->getMessage();
            }

        }

        public function updateClientNotification($clientNotification){
            global $db_connection;
            $query = "UPDATE client_notification SET date = DATE $clientNotification->date, frequency = '$clientNotification->frequency', 
            status = '$clientNotification->status' WHERE notification_id = '$clientNotification->notificationId';";
            try {
                $result = $db_connection->query($query);
                return $result;
            } catch (Exception $ex) {
                echo $ex->getMessage();
            }catch(Error $er){
                echo $er->getMessage();
            }
        }

        public function deleteClientNotification($notification_id){
            global $db_connection;
            $query = "DELETE FROM client_notification WHERE notification_id = '$notification_id';";
            try {
                $result = $db_connection->query($query);
                return $result;
            } catch (Exception $ex) {
                echo $ex->getMessage();
            }catch(Error $er){
                echo $er->getMessage();
            }
        }

        public function searchClientNotificationByStatus($status){
            global $db_connection;
            $query = "SELECT * FROM client_notification WHERE status = '$status';";
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