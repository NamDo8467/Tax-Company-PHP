<?php

    session_start();
    spl_autoload_register(function ($class_name) {
        include 'model/'. $class_name . '.php';
    });
    require "model/inc/database.php";
    require "model/inc/functions.php";
    
    $logManager = new LogManager();

    $page = 'login';
    $clientManager = new ClientManager();
    $clientList = $clientManager->getClientList();

    
    $employeeManager = new EmployeeManager();
    $employeeList = $employeeManager -> getEmployeeList();
    
    $username = $password = '';
    $clientTableHeaders = array('ID','Company', 'Business number', 'Name', 'Phone', 'Cellphone', 'Carrier', 'HST', 'Website', 'Status');
    $notify_message = "";
    $success = "";
    $clientOption = '';

    $frequencyList = array('30 days', '120 days', '365 days');

    $employeeTableHeaders = array('ID','First Name', 'Last Name', 'Email', 'Cellphone', 'Position', 'Status');
    $employeePositionLists = array('Manager', 'Senior Accountant', 'Junior Accountant', 'Chartered Accountant', 'Book Keeper');

    $carriers = array("Bell"=>"@txt.bellmobility.ca", "Bell Mobility" => "@txt.bell.ca",
    "Fido" => "@fido.ca", "Microcell"=>"@fido.ca", "Rogers"=>"@pcs.rogers.com", 
    "Solo Mobile" => "@txt.bell.ca", "Telus"=>"@msg.telus.com",
    "Virgin Mobile"=>"@vmobile.ca", "Koodo"=>'@msg.koodomobile.com', "Chatr"=>"@pcs.rogers.com",
    "Sasktel"=>"@sms.Sasktel.com");

    $clientId = $client_company = $client_businessNumber = $client_firstName = $client_lastName = $client_phoneNumber = '';
    $client_cellphone = $client_carrier = $client_hst = $client_website = '';
      $client_status = 'active';

    $employeeId = $employee_firstName = $employee_lastName = '';
    $employee_email = $employee_password = $employee_cellphone = '';
    $employee_position = $employee_picture =  '';
    $employee_status = 'active';
    $picture_content = '';

    $cellphone_error = $phone_error = '';

    $notificationHeaders = array();
    $notificationHeaders['employee'] = array('Id', 'Name', 'Type', 'Status');
    $notificationHeaders['client'] = array('Notification Id', 'Client Id','Date', 'Frequency', 'Status');

    $client_notification_frequency = '';
    $client_notification_status = 'active';
    $client_notificationId = uniqid();
    $client_notification_date = '';

    $employeeNotificationManager = new EmployeeNotificationManager();

    $logTableHeaders = array('Employee ID', 'Module name', 'Action', 'Date time', 'IP address');
    $logList = $logManager->getLogList();


    /* Login */
    $id = '';
    if(isset($_COOKIE['user'])){
        $page = 'main';
        if(isset($_GET['page'])){
            $page = $_GET['page'];
           
        }

    }else if(isset($_POST['username']) && isset($_POST['password'])){
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
        $page = 'main';
        try {
            if(checkEmployeeCredentialsAndStatus($username, $password, $id, $employeeList)){
                $_SESSION['user'] = $username;
                $_SESSION['id'] = $id;
                setcookie('user', $_SESSION['user'], (time() + (60 * 60 * 1)), "/"); //2 hours
                setcookie('id', $_SESSION['id'], (time() + (60 * 60 * 1)), "/");

                // setCookies($username, $id);

                $date_time = date("Y/m/d") . ' ' . date("H:i:s");
                $log = new Log($_SESSION['id'], 'employee', 'login', $date_time, $_SERVER['REMOTE_ADDR']);
                $logManager->addLog($log);
            }
        } catch (Exception $e) {
            $notify_message = $e->getMessage();
            $page = '';
        }
        
    }else{
        $page = '';
    }

    /* Log out */
    if(array_key_exists('logout_yes_button', $_POST)){
        if(isset($_SESSION['user'])){
            unset($_SESSION['user']);
        }
        if(isset($_SESSION['id'])){
            $date_time = date("Y/m/d") . ' ' . date("H:i:s");
            $log = new Log($_SESSION['id'], 'employee', 'logout', $date_time, $_SERVER['REMOTE_ADDR']);
            $logManager->addLog($log);
            unset($_SESSION['id']);
            
        }
        if(isset($_COOKIE['user'])){
            setcookie('user', null, -1,"/");
        }
        if(isset($_COOKIE['id'])){
            setcookie('id', null, -1,"/");
        }
        
        session_destroy();
        
        header("Location:/comp1230/assignments/assignment4");
    }else if(array_key_exists('logout_no_button', $_POST)){
        header("Location:/comp1230/assignments/assignment4");
    }

    
    require "view/inc/head.inc.phtml";

    date_default_timezone_set('Canada/Eastern');

    /* Get employee notification list */
    $employeeNotificationList = $employeeNotificationManager->getEmployeeNotificationList();

    /* Delete employee notification */
    if(isset($_GET['delete-employee-notification'])){
        $delete_employee_notification_id = filter_input(INPUT_GET, 'delete-employee-notification', FILTER_SANITIZE_STRING);

        if(isset($_POST['delete_employee_notification_yes_button'])){
            $id = filter_input(INPUT_POST, 'employee-notification-id', FILTER_SANITIZE_STRING);
            if($employeeNotificationManager->deleteEmployeeNotification(intval($delete_employee_notification_id))){
                $date_time = date("Y/m/d") . '' . date("H:i:s");
                $log = new Log($_COOKIE['id'], 'employee notification', 'delete employee notification', $date_time, $_SERVER['REMOTE_ADDR']);
                $logManager->addLog($log);
                $notify_message = "Deleted successfully";
            }else{
                $notify_message = "Error occurred. Please try again";
            }
        }
    }
    /* Update employee notification */
    if(isset($_GET['update-employee-notification-page'])){
        $update_notification_id = filter_input(INPUT_GET, 'employee-notification-id', FILTER_SANITIZE_STRING);
        $update_notification_name = filter_input(INPUT_GET, 'employee-notification-name', FILTER_SANITIZE_STRING);
        $update_notification_type = filter_input(INPUT_GET, 'employee-notification-type', FILTER_SANITIZE_STRING);
        $update_notification_status = filter_input(INPUT_GET, 'status', FILTER_SANITIZE_STRING);

        if(isset($_POST['update-employee-notification-button'])){
            $update_notification_id = filter_input(INPUT_POST, 'employee-notification-id', FILTER_SANITIZE_STRING);
            $update_notification_name = filter_input(INPUT_POST, 'employee-notification-name', FILTER_SANITIZE_STRING);
            $update_notification_type = filter_input(INPUT_POST, 'employee-notification-type', FILTER_SANITIZE_STRING);
            $update_notification_status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
            
            if($employeeNotificationManager->updateEmployeeNotification(intval($update_notification_id), $update_notification_name, $update_notification_type, $update_notification_status)){
                $notify_message = "Update notification successfully";
                $update_notification_name = $update_notification_type = $update_notification_status = '';

                $date_time = date("Y/m/d") . '' . date("H:i:s");
                $log = new Log($_COOKIE['id'], 'employee notification', 'update employee notification', $date_time, $_SERVER['REMOTE_ADDR']);
                $logManager->addLog($log);
            }else{
                $notify_message = "Error occurred. Please try again";
            }
        }
        
    }

     /* Add employee notification */
     
     $employee_notification_status = 'disabled';
     if(isset($_POST['add-employee-notification-button'])){
         $employee_notification_name = filter_input(INPUT_POST, 'employee-notification-name', FILTER_SANITIZE_STRING);
         $employee_notification_type  = filter_input(INPUT_POST, 'employee-notification-type', FILTER_SANITIZE_STRING);
         $employee_notification_status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);

         $employeeNotification = new EmployeeNotification($employee_notification_name, $employee_notification_type, $employee_notification_status);
        
         if($employeeNotificationManager -> addEmployeeNotification($employeeNotification)){
            $notify_message = "Added notification successfully";

            $date_time = date("Y/m/d") . '' . date("H:i:s");
            $log = new Log($_COOKIE['id'], 'employee notification', 'add employee notification', $date_time, $_SERVER['REMOTE_ADDR']);
            $logManager->addLog($log);
         }else{
             $notify_message = "Error occurred. Please try again";
         }
         
     }


    /* Delete employee */
    if(isset($_GET['delete-employee'])){
        $page = 'deleteEmployee';
        $employeeId = $_GET['delete-employee'];
        
    }
    if(isset($_POST['delete_employee_yes_button'])){
        $employeeId = filter_input(INPUT_POST, 'delete-employee-id', FILTER_SANITIZE_STRING);
        if($employeeManager->deleteEmployee($employeeId)){
            $date_time = date("Y/m/d") . '' . date("H:i:s");
            $log = new Log($_COOKIE['id'], 'employee', 'delete employee', $date_time, $_SERVER['REMOTE_ADDR']);
            $logManager->addLog($log);
            $notify_message = "Deleted successfully";
        }else{
            $notify_message = "Error occurred. Please try again";
        }
        
    }

    /* Get employee details */
    if(isset($_GET['employeeId'])){
        $employee_id = filter_input(INPUT_GET, 'employeeId', FILTER_SANITIZE_STRING);
        $employee = $employeeManager->getEmployeeDetail($employee_id);

        $date_time = date("Y/m/d") . '' . date("H:i:s");
        $log = new Log($_COOKIE['id'], 'employee', 'check employee detail', $date_time, $_SERVER['REMOTE_ADDR']);
        $logManager->addLog($log);
        if($employee){
            if($employee[0]['picture'] != ''){
                $pictureContent = decodePictureContent($employee[0]['picture']);
            }else{
                $pictureContent = '';
            }
        }else{
            $employee = '';
            header('Location: /comp1230/assignments/assignment4/?page=error');
        }
        
        
    }

    /* Get update employee */
    if(isset($_GET['employee-id'])){
        $employeeId = filter_input(INPUT_GET, 'employee-id', FILTER_SANITIZE_STRING);
        $employee_firstName = filter_input(INPUT_GET, 'first-name', FILTER_SANITIZE_STRING);
        $employee_lastName = filter_input(INPUT_GET, 'last-name', FILTER_SANITIZE_STRING);
        $employee_email = filter_input(INPUT_GET, 'email', FILTER_SANITIZE_STRING);
        $employee_cellphone = filter_input(INPUT_GET, 'cellphone', FILTER_SANITIZE_STRING);
        $employee_position = filter_input(INPUT_GET, 'position', FILTER_SANITIZE_STRING);
        $employee_status = filter_input(INPUT_GET, 'status', FILTER_SANITIZE_STRING);

    }
    /* Add and update employee */
    if(isset($_POST['first-name']) && isset($_POST['last-name']) && isset($_POST['email'])  
    && isset($_POST['cellphone']) && isset($_POST['position']) ){
            $employeeId = filter_input(INPUT_POST, 'employee-id', FILTER_SANITIZE_STRING);
            $employee_firstName = filter_input(INPUT_POST, 'first-name', FILTER_SANITIZE_STRING);
            $employee_lastName = filter_input(INPUT_POST, 'last-name', FILTER_SANITIZE_STRING);
            $employee_email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
            $employee_cellphone = filter_input(INPUT_POST, 'cellphone', FILTER_SANITIZE_STRING);
            $employee_position = filter_input(INPUT_POST, 'position', FILTER_SANITIZE_STRING);

            isset($_POST['status']) ? $employee_status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING): null;      
            
            try {
                checkCellPhoneAndPhoneNumber($employee_cellphone, '');
                if(isset($_POST['update-employee-button'])){
                    if($_FILES['picture']['tmp_name'] != ''){
                        $employee_picture = $_FILES['picture']['tmp_name']; 
                        $picture_content = addslashes(file_get_contents($employee_picture));
                    }
                    
                    $employee = new Employee($employeeId, $employee_firstName, $employee_lastName, 
                                    $employee_email, $employee_cellphone, $employee_position, 
                                    $picture_content, $employee_status);
                    if($employeeManager -> updateEmployeeInformation($employee)){
                        $notify_message = "Employee updated successfully";
                        $employee_firstName = $employee_lastName = $employee_email = '';
                        $employee_cellphone = $employee_status = $employee_position = '';
                    
                        $cellphone_error = '';

                        $date_time = date("Y/m/d") . '' . date("H:i:s");
                        $log = new Log($_COOKIE['id'], 'employee', 'update employee', $date_time, $_SERVER['REMOTE_ADDR']);
                        $logManager->addLog($log);
                    }else{
                        $notify_message = "Employee can not be updated";
                    }
                }else if(isset($_POST['add-employee-button'])){
                    $employeeId = uniqid();
                    $employee_password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
                    if($_FILES['picture']['tmp_name'] != ''){
                        $employee_picture = $_FILES['picture']['tmp_name']; 
                        $picture_content = addslashes(file_get_contents($employee_picture));
                    }
                     
                    $employee = new Employee($employeeId, $employee_firstName, $employee_lastName, 
                                    $employee_email, $employee_cellphone, $employee_position, $employee_password,
                                    $picture_content, $employee_status);
                    if($employeeManager -> addEmployee($employee)){
                        $notify_message = "Employee added successfully";
                        $employee_firstName = $employee_lastName = $employee_email = '';
                        $employee_password = $employee_cellphone = $position = $employee_status = '';

                    
                        $cellphone_error = "";

                        $date_time = date("Y/m/d") . '' . date("H:i:s");
                        $log = new Log($_COOKIE['id'], 'employee', 'add employee', $date_time, $_SERVER['REMOTE_ADDR']);
                        $logManager->addLog($log);
                    }else{
                        $notify_message = "Employee can not be added";
                    }
                }
                
            } catch (Exception $e) {
                $cellphone_error = $e->getMessage();
                $notify_message = "Error occurred. Please try again";
                
            }
     }
     
     $clientNotificationManager = new ClientNotificationManager();

     $clientNotificationList = $clientNotificationManager-> getClientNotificationList();

     /* Delete client notification */
     if(isset($_GET['delete-client-notification'])){
        $delete_client_notification_id = filter_input(INPUT_GET,'delete-client-notification', FILTER_SANITIZE_STRING);
        if(isset($_POST['delete_client_notification_yes_button'])){
            $id = filter_input(INPUT_POST, 'client-notification-id', FILTER_SANITIZE_STRING);
            if($clientNotificationManager->deleteClientNotification($delete_client_notification_id)){
                $date_time = date("Y/m/d") . '' . date("H:i:s");
                $log = new Log($_COOKIE['id'], 'client notification', 'delete client notification', $date_time, $_SERVER['REMOTE_ADDR']);
                $logManager->addLog($log);
                $notify_message = "Deleted successfully";
            }else{
                $notify_message = "Error occurred. Please try again";
            }
        }
     }

     /* Update client notification */
     if(isset($_GET['update-client-notification-page'])){
        $update_notification_id = filter_input(INPUT_GET, 'client-notification-id', FILTER_SANITIZE_STRING);
        $update_client_id = filter_input(INPUT_GET, 'client-id', FILTER_SANITIZE_STRING);
        $update_notification_date = filter_input(INPUT_GET, 'client-notification-date', FILTER_SANITIZE_STRING);
        $update_notification_frequency = filter_input(INPUT_GET, 'client-notification-frequency', FILTER_SANITIZE_STRING);
        $update_notification_status = filter_input(INPUT_GET, 'status', FILTER_SANITIZE_STRING);

        if(isset($_POST['update-client-notification-button'])){
            $update_notification_id = filter_input(INPUT_POST, 'client-notification-id', FILTER_SANITIZE_STRING);
            $update_client_id = filter_input(INPUT_POST, 'client-id', FILTER_SANITIZE_STRING);
            $update_notification_date = filter_input(INPUT_POST, 'client-notification-date', FILTER_SANITIZE_STRING);
            $update_notification_frequency = filter_input(INPUT_POST, 'client-notification-frequency', FILTER_SANITIZE_STRING);
            $update_notification_status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
            
            $clientNotification = new ClientNotification($update_client_id, $update_notification_id, $update_notification_date,$update_notification_frequency, $update_notification_status);
            
            if($clientNotificationManager->updateClientNotification($clientNotification)){
                $notify_message = "Update notification successfully";
                $update_notification_date = $update_notification_frequency = $update_notification_status = '';

                $date_time = date("Y/m/d") . '' . date("H:i:s");
                $log = new Log($_COOKIE['id'], 'client notification', 'update client notification', $date_time, $_SERVER['REMOTE_ADDR']);
                $logManager->addLog($log);
            }else{
                $notify_message = "Error occurred. Please try again";
            }
        }
        
    }
     /* Add client notification */
    if(isset($_POST['add-client-notification-button'])){
        $clientId = filter_input(INPUT_POST, 'client-id', FILTER_SANITIZE_STRING);
        $client_notificationId = filter_input(INPUT_POST, 'notification-id', FILTER_SANITIZE_STRING);
        $client_notification_frequency = filter_input(INPUT_POST, 'frequency', FILTER_SANITIZE_STRING);
        $client_notification_status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
        $client_notification_date = filter_input(INPUT_POST, 'notification-date', FILTER_SANITIZE_STRING);
        
        $clientNotification = new ClientNotification($clientId, $client_notificationId, $client_notification_date, 
        $client_notification_frequency, $client_notification_status);

        if($clientNotificationManager -> addClientNotification($clientNotification)){
            $notify_message = "Added notification successfully";
            $date_time = date("Y/m/d") . '' . date("H:i:s");
            $log = new Log($_COOKIE['id'], 'client notification', 'add client notification', $date_time, $_SERVER['REMOTE_ADDR']);
            $logManager->addLog($log);
        }else{
            $notify_message = "Error occurred. Please try again";
        }
        
    }

     /* Delete client */
    if(isset($_GET['delete-client'])){
        $page = 'deleteClient';
        $clientId = $_GET['delete-client'];
        
    }
    if(isset($_POST['delete_client_yes_button'])){
        $clientId = filter_input(INPUT_POST, 'delete-client-id', FILTER_SANITIZE_STRING);
        if($clientManager->deleteClient($clientId)){
            $date_time = date("Y/m/d") . '' . date("H:i:s");
            $log = new Log($_COOKIE['id'], 'client', 'delete client', $date_time, $_SERVER['REMOTE_ADDR']);
            $logManager->addLog($log);
            $notify_message = "Deleted successfully";
        }else{
            $notify_message = "Error occurred. Please try again";
        }
    }

   /* Get update client */
    if(isset($_GET['client-id'])){
        $clientId = filter_input(INPUT_GET, 'client-id', FILTER_SANITIZE_STRING);
        $client_company = filter_input(INPUT_GET, 'company', FILTER_SANITIZE_STRING);
        $client_businessNumber = filter_input(INPUT_GET, 'business-number', FILTER_SANITIZE_STRING);
        $client_firstName = filter_input(INPUT_GET, 'first-name', FILTER_SANITIZE_STRING);
        $client_lastName = filter_input(INPUT_GET, 'last-name', FILTER_SANITIZE_STRING);
        $client_phoneNumber = filter_input(INPUT_GET, 'phone-number', FILTER_SANITIZE_STRING);
        $client_cellphone = filter_input(INPUT_GET, 'cellphone', FILTER_SANITIZE_STRING);
        $client_carrier = filter_input(INPUT_GET, 'carrier', FILTER_SANITIZE_STRING);
        $client_hst = filter_input(INPUT_GET, 'hst', FILTER_SANITIZE_STRING);
        $client_website = filter_input(INPUT_GET, 'website', FILTER_SANITIZE_STRING);
        $client_status = filter_input(INPUT_GET, 'status', FILTER_SANITIZE_STRING);

    }

     /* Add client and Update client */
    if(isset($_POST['company']) && isset($_POST['business-number']) && (isset($_POST['first-name']))
        && isset($_POST['last-name']) && isset($_POST['phone-number']) && (isset($_POST['cellphone']))
        && isset($_POST['carrier'])){

            if(isset($_POST['client-id'])){
                $clientId = filter_input(INPUT_POST, 'client-id', FILTER_SANITIZE_STRING);
            }else{
                $clientId = uniqid();
            }
            // $clientId == '' ? $clientId = uniqid() : null;
            $client_company = filter_input(INPUT_POST, 'company', FILTER_SANITIZE_STRING);
            $client_businessNumber = filter_input(INPUT_POST, 'business-number', FILTER_SANITIZE_STRING);
            $client_firstName = filter_input(INPUT_POST, 'first-name', FILTER_SANITIZE_STRING);
            $client_lastName = filter_input(INPUT_POST, 'last-name', FILTER_SANITIZE_STRING);
            $client_phoneNumber = filter_input(INPUT_POST, 'phone-number', FILTER_SANITIZE_STRING);
            $client_cellphone = filter_input(INPUT_POST, 'cellphone', FILTER_SANITIZE_STRING);
            $client_carrier = filter_input(INPUT_POST, 'carrier', FILTER_SANITIZE_STRING);

            isset($_POST['hst']) ? $client_hst = filter_input(INPUT_POST, 'hst', FILTER_SANITIZE_STRING) : null;
            isset($_POST['website']) ? $client_website = filter_input(INPUT_POST, 'website', FILTER_SANITIZE_STRING) : null;
            isset($_POST['status']) ? $client_status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING) : nulll;

            $client = new Client($clientId, $client_company, $client_businessNumber, $client_firstName, 
            $client_lastName, $client_phoneNumber, $client_cellphone, $client_carrier, 
            $client_hst, $client_website, $client_status);
                        
            try {
                checkCellPhoneAndPhoneNumber($client_cellphone, $client_phoneNumber);
                if(isset($_POST['update-client-button'])){
                    if($clientManager -> updateClientInformation($client)){
                        $notify_message = "Client updated successfully";
                        $client_company = $client_businessNumber = $client_firstName = $client_lastName = $client_phoneNumber = '';
                        $client_cellphone = $client_carrier = $client_hst = $client_website =  $client_status = '';
                    
                        $cellphone_error = $phone_error = '';
                        $date_time = date("Y/m/d") . ' ' . date("H:i:s");
                        $log = new Log($_COOKIE['id'], 'client', 'update client', $date_time, $_SERVER['REMOTE_ADDR']);
                        $logManager->addLog($log);
                    }else{
                        $notify_message = "Client can not be updated";
                    }
                }else if(isset($_POST['add-client-button'])){
                    if($clientManager -> addClient($client)){
                        $notify_message = "Client added successfully";
                        $client_company = $client_businessNumber = $client_firstName = $client_lastName = $client_phoneNumber = '';
                        $client_cellphone = $client_carrier = $client_hst = $client_website =  $client_status = '';
                    
                        $cellphone_error = $phone_error = '';

                        $date_time = date("Y/m/d") . '' . date("H:i:s");
                        $log = new Log($_COOKIE['id'], 'client', 'add client', $date_time, $_SERVER['REMOTE_ADDR']);
                        $logManager->addLog($log);
                    }else{
                        $notify_message = "Client can not be added";
                    }
                }
                
            } catch (Exception $e) {
                $error_message = $e->getMessage();
                if(strpos($error_message, "cell phone")){
                    $cellphone_error = $error_message;
                    $notify_message = "Error occurred. Please try again";
                }else{
                    $phone_error = $error_message;
                    $notify_message = "Error occurred. Please try again";
                }
                
            }
           
        
    }
    
    switch ($page) {
        case '':
            require('view/login.phtml');
            break;
        case 'main':
            require('view/main.phtml');
            break;
        case 'client':
            require('view/client/client.phtml');
            break;
        case 'addClient':
            require('view/client/addClient.phtml');
            break;
        case 'updateClient':
            require('view/client/updateClient.phtml');
            break;
        case 'deleteClient':
            require('view/client/deleteClient.phtml');
            break;
        case 'employee':
            require('view/employee/employee.phtml');
            break;
        case 'addEmployee':
            require('view/employee/addEmployee.phtml');
            break;
        case 'updateEmployee':
            require('view/employee/updateEmployee.phtml');
            break;
        case 'deleteEmployee':
            require('view/employee/deleteEmployee.phtml');
            break;
        case 'employeeDetail':
            require('view/employee/employeeDetail.phtml');
            break;

        case 'employeeNotification':
            require('view/employeeNotification/employeeNotification.phtml');
            break;
        case 'addEmployeeNotification':
            require('view/employeeNotification/addEmployeeNotification.phtml');
            break;
        case 'updateEmployeeNotification':
            require('view/employeeNotification/updateEmployeeNotification.phtml');
            break;
        case 'deleteEmployeeNotification':
            require('view/employeeNotification/deleteEmployeeNotification.phtml');
            break;

        case 'clientNotification':
            require('view/clientNotification/clientNotification.phtml');
            break;
        case 'addClientNotification':
            require('view/clientNotification/addClientNotification.phtml');
            break;
        case 'updateClientNotification':
            require('view/clientNotification/updateClientNotification.phtml');
            break;
        case 'deleteClientNotification':
            require('view/clientNotification/deleteClientNotification.phtml');
            break;
        case 'log':
            require('view/log/log.phtml');
            break;
        case 'logout':
            require('view/logout.phtml');
            break;
        case 'error':
            require('view/error.phtml');
            break;
        case 'member':
            require('view/member.phtml');
            break;
        default:
            require('view/error.phtml');
            break;
    }
    require "view/inc/footer.inc.phtml";

    echo "<hr>";
    show_source(__FILE__);
?>
