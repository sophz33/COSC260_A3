<?php

ini_set('display_errors', TRUE); // remove before submitting, just for troubleshooting

/* AUTOLOAD CLASSES */
spl_autoload_register(function($class_name){
    require_once __DIR__ . "/class/" . $class_name . ".php";
});


$db = new Database();


// check name/age/email/phone parameters 
// validation for name and email modified from https://www.w3schools.com/php/php_form_url_email.asp
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = getParameter("name");
    $age = getParameter("age");
    $email = getParameter("email");
    $phone = getParameter("phone");

    // name
    if ($name !== NULL) {
        if (preg_match("/^[a-zA-Z-']*$/", $name)) {
            $result["name"] = $name;
            $id = $db->getName($name);
            echo "name:", $result;
        } else {
            $errors[] = $customMsgs["nameEr"];
        }
    } else {
        $errors[] = "name not provided";
    }

    // age
    if ($age !== NULL) {
        if (($age < 131) && ($age > 12)) {
            $result["age"] = $age;
            $id = $db->getAge($age);
        }
    } else {
        $errors[] = "age not provided";
    }

    // email
    if ($email !== NULL) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)){
            $result["email"] = $email;
            $id = $db->getEmail($email);
        } else {
            $errors[] = $customMsgs["emailEr"];
        }
    } else {
        $errors[] = "email not provided";
    }

    // phone
    if ($phone !== NULL) {
        if (!preg_match("/^04([0-9]+)+$/", $phone)) {
            $errors[] = $customMsgs["phoneEr"];
            $id = $db->getPhone($phone);
        } else {
            $result["phone"] = $phone;
        }
    } else {
        $result["phone"] = "NA";
    }
    
} else {
    send_error(405, $responses[405], "Request method must be POST", $error_arr);
    print(json_encode($error_arr));
    die();
}


//  add any errors to results array
if (!empty($errors)){
    $result["errors"] = $errors;
} else {
    $result["user_id"] = rand(10000, 99999);
    // ENCODE HERE?
}



// Return JSON response 
// header('Content-Type: application/json; charset=utf-8');
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
echo $result;
echo(json_encode($db($result)));


/*********************************************************************/

// reponse codes
$responses = [
    400 => "Bad Request",
    404 => "Not Found",
    405 => "Method Not Allowed",
    500 => "Internal server error"
];


// empty arrays for validation results and/or errors
$result = array();
$errors = array();


// custom messages for errors
$customMsgs = [
    "nameEr" => "Name must only contain characters and between 2-100 long",
    "ageEr" => "Age must be between 13-130",
    "emailEr" => "Email is not valid",
    "phoneEr" => "Phone must start with '04' and be a valid Australian mobile number"
];

// header function NEEDS TO BE CORRECTED AND FINISHED
// $PROTOCOL = $_SERVER['SERVER_PROTOCOL']; 
// $CODE = http_response_code(); 
// $REASON = $responses[$CODE]; 
// $MESSAGE = $customMsgs[$errors];


// error function NEEDS TO BE MODIFIED FOR THIS REQUIREMENT
function send_error($CODE, $REASON, $MESSAGE, &$ERR_ARR){ 
    $PROTOCOL = $_SERVER['SERVER_PROTOCOL'];
    $HEADER = "Error: $PROTOCOL $CODE - $REASON";
    header($HEADER, true, $CODE);
    $ERR_ARR[] = "$CODE - $REASON: $MESSAGE";
}


function getParameter($k){
    if (isset($_POST[$k])) {
        echo "post[k] is";
        echo ($_POST[$k]);
        return $_POST[$k];
    } else {
        return NULL;
    }
}




