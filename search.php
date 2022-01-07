<?php


    require "model/ClientManager.php";
    require "model/EmployeeManager.php";
    require "model/ClientNotificationManager.php";
    require "model/EmployeeNotificationManager.php";
    require "model/inc/functions.php";
    require "model/inc/database.php";

    
    $clientManager = new ClientManager();

    $clientTableHeaders = array('ID','Company', 'Business number', 'Name', 'Phone', 'Cellphone', 'Carrier', 'HST', 'Website', 'Status');

    $employeeTableHeaders = array('ID','First Name', 'Last Name', 'Email', 'Cellphone', 'Position', 'Status');


    $employeeManager = new EmployeeManager();

    $clientNotificationManager = new ClientNotificationManager();

    $employeeNotificationManager = new EmployeeNotificationManager();

    $notificationHeaders = array();
    $notificationHeaders['employee'] = array('Id', 'Name', 'Type', 'Status');
    $notificationHeaders['client'] = array('Notification Id', 'Client Id','Date', 'Frequency', 'Status');

    // $page = 'searchClient';
    
    if(isset($_GET['page'])){
        
        $page = $_GET['page'];
        
    }
    require "view/inc/head.inc.phtml";

    /* Search client by company */
    if(isset($_GET['search_company'])){
        $company = filter_input(INPUT_GET, 'search_company', FILTER_SANITIZE_STRING);
        $clientList = $clientManager->searchByCompany($company); 
        if(!$clientList){
            $clientList = "No result found";
        }
    }else{
        $clientList = 'No result found';
    }

    
    /* Search employee by last name */
    if(isset($_GET['search_last_name'])){
        $lastName = filter_input(INPUT_GET, 'search_last_name', FILTER_SANITIZE_STRING);
        $employeeList = $employeeManager -> searchByLastName(ucfirst($lastName));
        if(!$employeeList){
            $employeeList = 'No result found';
        }
    }else{
        $employeeList = 'No result found';
    }
    

    /* Search client notification by status */
    if(isset($_GET['search_client_notification_status'])){
        $status = filter_input(INPUT_GET, 'search_client_notification_status', FILTER_SANITIZE_STRING);
        $clientNotificationList = $clientNotificationManager->searchClientNotificationByStatus($status);
        if(!$clientNotificationList){
            $clientNotificationList = 'No result found';
            
        }
    }else{
        $clientNotificationList = "No result found";
    }

    /* Search employee notification by name */
    if(isset($_GET['search_employee_notification_name'])){
        $name = filter_input(INPUT_GET,'search_employee_notification_name', FILTER_SANITIZE_STRING);

        $employeeNotificationList = $employeeNotificationManager->searchEmployeeNotificationByName($name);
        if(!$employeeNotificationList){
            $employeeNotificationList = 'No result found';
            
        }
    }else{
        $employeeNotificationList = 'No result found';
        
    }

    switch ($page) {
        case 'searchClient':
            require "view/search/searchClient.phtml";
            break;
        case 'searchEmployee':
            require "view/search/searchEmployee.phtml";
            break;
        case 'searchClientNotification':
            require "view/search/searchClientNotification.phtml";
            break;
        case 'searchEmployeeNotification':
            require "view/search/searchEmployeeNotification.phtml";
            break;
        default:
            require('view/error.phtml');
            break;
    }

    require "view/inc/footer.inc.phtml";
    echo "<hr>";
    show_source(__FILE__);
?>