<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: access');
header('Access-Control-Allow-Methods: POST');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

require __DIR__ . '/classes/Database.php';

$db_connection = new Database();
$conn = $db_connection->dbConnection();

function messages($success, $status, $message, $extra = [])
{
    return array_merge([
        'success'   => $success,
        'status'    => $status,
        'message'   => $message
    ], $extra);
}

// Data from on request
$data = json_decode(file_get_contents('php://input'));
$returnData = [];
if($_SERVER['REQUEST_METHOD'] != 'POST') {
    $returnData = messages(0, 404, 'Page not found!');
} elseif(!isset($data->name) || !isset($data->email) || !isset($data->password) || empty(trim($data->name)) || empty(trim($data->email)) || empty(trim($data->password))) {
    $fields = ['fields' => ['name', 'email', 'password']];
    $returnData = messages(0, 422, 'Please fill in all required field!', $fields);
} else {
    $name = trim($data->name);
    $email = trim($data->email);
    $password = trim($data->password);
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $returnData = messages(0, 422, 'Invalid email address!');
    } elseif(strlen($password) < 8) {
        $returnData = messages(0, 422, 'Password must me at least 8 character!');
    } elseif(strlen($name) < 3) {
        $returnData = messages(0, 422, 'Name must me at least 3 character!');
    } else {
        try{
            $check_email = "SELECT `email` FROM `Users` WHERE `email` = :email";
            $query_check = $conn->prepare($check_email);
            $query_check->bindValue(':email', $email, PDO::PARAM_STR);
            $query_check->execute();
            if($query_check->rowCount()) {
                $returnData = messages(0, 422, 'Email address already in use!');
            } else {
                $insert_email = "INSERT INTO `Users`(`name`, `email`, `password`) VALUES(:name, :email, :password)";
                $query_insert = $conn->prepare($insert_email);
                // Data Binding
                $query_insert->bindValue(':name', htmlspecialchars(strip_tags($name)), PDO::PARAM_STR);
                $query_insert->bindValue(':email', $email, PDO::PARAM_STR);
                $query_insert->bindValue(':password', password_hash($password, PASSWORD_DEFAULT), PDO::PARAM_STR);
                $query_insert->execute();
                $returnData = messages(1, 201, 'You have successfully registered!');
            }
        }
        catch(PDOException $e) {
            $returnData = messages(0, 500, $e->getMessage());
        }
    }
}

echo json_encode($returnData);

?>