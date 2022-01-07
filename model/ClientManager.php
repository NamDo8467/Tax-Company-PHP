<?php
    class ClientManager{

        private $clientList; 

        public function __construct(){
            $this->clientList = array();
            
        }
        public function getClientList(){
            global $db_connection;
            $query = "SELECT * from client;";
            $data = $db_connection->query($query);
            $clientList = $data->fetch_all(MYSQLI_ASSOC);
            return $clientList;
        }
        public function addClient($client){
            global $db_connection;
            $query = "INSERT INTO client VALUES('$client->clientId', '$client->companyName', 
            '$client->businessNumber', '$client->firstName', '$client->lastName', '$client->phoneNumber', 
            '$client->cellphone', '$client->carrier', '$client->hst', '$client->website', '$client->status');";
            $result = $db_connection->query($query);
            if($result == '1'){
                $this->clientList[] = $client;
                return true;
            }else{
                return false;
            }
           
        }
        public function updateClientInformation($client){
            global $db_connection;
            $clientList = $this->getClientList();
            foreach ($clientList as $c) {
                if($c['id'] == $client->clientId){
                    $query = "UPDATE client SET company = '$client->companyName', business_number = '$client->businessNumber',
                    first_name = '$client->firstName', last_name = '$client->lastName', phone_number = '$client->phoneNumber',
                    cellphone = '$client->cellphone', carrier = '$client->carrier', hst = '$client->hst',
                    website = '$client->website', stt = '$client->status' WHERE id = '$client->clientId';";
                    try {
                        $result = $db_connection->query($query);
                        break;
                    } catch (Exception $e) {
                        return $e.getMessage();
                    }
                    
                }
            }
            return $result;
            
        }

        
        public function deleteClient($clientId){
            global $db_connection;
            $deleteClientNotificationQuery = "DELETE FROM client_notification WHERE client_id = '$clientId';";
            $query = "DELETE FROM client WHERE id = '$clientId';";
            try {
                $deleteNotificationResult = $db_connection->query($deleteClientNotificationQuery);
                $result = $db_connection->query($query);
                return true;
            } catch (Exception $e) {
                return false;
            }
            
        }

        public function searchByCompany($client_company){
            global $db_connection;
            $query = "SELECT * FROM client WHERE company = '$client_company'";
            $data = $db_connection->query($query);
            $result = $data->fetch_all(MYSQLI_ASSOC);
            if($result){
                return $result;
            }else{
                return false;
            }
        }        
        public function __get($name){
            return $this->$name;
        }

       
        public function __destruct(){
    
        }
    }

?>