<?php
    //Authentication: checking username and password when employee log in
    function checkEmployeeCredentialsAndStatus($username, $password, &$id, $employeeList){
        foreach ($employeeList as $employee) {
            if($username == $employee['email'] && $password == $employee['password']) {
                if($employee['status'] == 'inactive'){
                    //Can't log in if status is inactive -> Throw exception
                    throw new Exception("Employee is currently inactive");
                }else{
                    // Get the id of the user (for log)
                    $id = $employee['employee_id'];
                    return true;
                }
                
            }
        }

        // If username or password does not match, throw exception
        throw new Exception("Username or password is incorrect");
        
    }

    //Validate cellphone and phone number. They can only include numbers
    function checkCellPhoneAndPhoneNumber($cellphone, $phone){
        if($phone != ''){
            // Loop through and check every character
            for ($i=0; $i < strlen($phone) ; $i++) { 
                if($phone[$i] < '0' || $phone[$i] > '9'){
                    throw new Exception("Can not include special characters or letters in phone number");
                }
            }
        }else{
            // Loop through and check every character
            for ($i=0; $i < strlen($cellphone) ; $i++) { 
                if($cellphone[$i] < '0' || $cellphone[$i] > '9'){
                    throw new Exception("Can not include special characters or letters in cell phone number");
                }
            }
    
        }
        return true;
    }

    //Get the picture from database and decode it. Put the decode picture in src attribute of img tag
    function decodePictureContent($picture){
        $decodedContent = "data:picture/png;charset=utf8;base64,". base64_encode($picture);
        $renderedPicture = "src='$decodedContent'";
        return $renderedPicture;
    }
?>