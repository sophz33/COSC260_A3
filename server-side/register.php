<?php

ini_set('display_errors', TRUE); // remove before submitting, just for troubleshooting

/* AUTOLOAD CLASSES */
spl_autoload_register(function($class_name){
    require_once __DIR__ . "/class/" . $class_name . ".php";
});


// reponse codes
$responses = [
    200 => "Success",
    400 => "Bad Request",
    404 => "Not Found",
    405 => "Method Not Allowed",
    500 => "Internal server error"
];

//delete before sending - troubleshooting only
function console_log( $data ){
    echo '<script>';
    echo 'console.log('. json_encode( $data ) .')';
    echo '</script>';
  }


// empty arrays for validation results and/or errors
$result = [];
$errors = [];

  //  header variables
$CODE = "";
// $REASON = $responses[$CODE]; 
// $MESSAGE = $result["errors"];


// custom messages for errors
$customMsgs = [
    "nameEr" => "Name must only contain characters and between 2-100 long",
    "ageEr" => "Age must be between 13-130",
    "emailEr" => "Email is not valid",
    "phoneEr" => "Phone must start with '04' and be a valid Australian mobile number"
];

$db = new Database();


// check name/age/email/phone parameters 
// validation for name and email modified from https://www.w3schools.com/php/php_form_url_email.asp

    
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (sizeof($_POST) === 0){ 
        send_error(400, $responses[400], "Post request must contain data", $errors);
        print(json_encode($errors));
        die();
    } else {
        check_set("name", $responses, $errors);
        check_set("age", $responses, $errors);
        check_set("email", $responses, $errors);
        json_encode($result);
        if ($not_set) {
            print(json_encode($errors));
            die();
        }
    }

    $name = $_POST["name"];
    $age = $_POST["age"];
    $age = filter_var($age, FILTER_VALIDATE_INT);
    $email = $_POST["email"];
    $phone = $_POST["phone"];

    // name
    if ($name) {
        if (preg_match("/^[a-zA-Z-']*$/", $name)) {
            global $result;
            $result["name"] = $name;
        } else {
            $errors[] = $customMsgs["nameEr"];
        }
    } else {
        send_error(400, $responses[400], "this is a name test", ($errors));
    }

    // age
    if ($age) {
        if (($age < 131) && ($age > 12)) {
            global $result;
            $result["age"] = $age;
            // $id = $db->getAge($age);
        }
    } else {
        $errors[] = "age not provided";
    }

    // email
    if ($email) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)){
            $result["email"] = $email;
            // $id = $db->getEmail($email);
        } else {
            $errors[] = $customMsgs["emailEr"];
        }
    } else {
        $errors[] = "email not provided";
    }

    // phone
    if ($phone) {
        if (!preg_match("/^04([0-9]+)+$/", $phone)) {
            $errors[] = $customMsgs["phoneEr"];
        } else {
            $result["phone"] = $phone;
            // $id = $db->getPhone($phone);
        }
    } else {
        $result["phone"] = "";
    }
        //  add any errors to results array
    if (!empty($errors)) {
        $result["errors"] = $errors[0];
        send_error(400, $responses[400], $errors[0], ($errors));
        echo json_encode($errors);
    } else {
        $result["user_id"] = rand(10000, 99999);
        $db->getName($result["name"]);
        $db->getAge($result["age"]);
        $db->getEmail($result["email"]);
        $db->getPhone($result["phone"]);
        echo json_encode($result);
        file_put_contents('id.json', json_encode($db->jsonSerialize()) . "\n", FILE_APPEND); 
    }

    
} else {
    send_error(405, $responses[405], "Request method must be POST", $errors);
    print(json_encode($errors));
    die();
}


// Return JSON response 
header('Content-Type: application/json; charset=utf-8');
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');


/*********************************************************************/


// error function REVIEW THIS UPDATE
function send_error($CODE, $REASON, $MESSAGE, &$ERR_ARR){ 
    $PROTOCOL = $_SERVER['SERVER_PROTOCOL'];
    $HEADER = "Error: $PROTOCOL $CODE - $REASON";
    header($HEADER, true, $CODE);
    $ERR_ARR[] = "$CODE - $REASON: $MESSAGE";
}

//  check is post is set
function check_set($param, $responses, &$ERR_ARR){ 
    global $not_set;
    if (!isset($_POST[$param])) {
        send_error(400, $responses[400], "$param is missing, must set $param to submit form", $ERR_ARR);
        $not_set = true;
    } 
}



