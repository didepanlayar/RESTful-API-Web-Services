<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: access');
header('Access-Control-Allow-Methods: POST');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

require __DIR__ . '/classes/Database.php';
require __DIR__ . '/classes/JWTHandler.php';

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
if($_SERVER["REQUEST_METHOD"] != "POST") {
    $returnData = messages(0, 404, 'Page not found!');
} elseif(!isset($data->email) || !isset($data->password) || empty(trim($data->email)) || empty(trim($data->password))) {
    $fields = ['fields' => ['email','password']];
    $returnData = messages(0, 422, 'Please fill in all required field!',$fields);
} else {
    $email = trim($data->email);
    $password = trim($data->password);
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $returnData = messages(0, 422, 'Invalid email address!');
    } elseif(strlen($password) < 8) {
        $returnData = messages(0, 422, 'Password must me at least 8 character!');
    } else {
        try {
            $fetch_user_by_email = "SELECT * FROM Users WHERE email = :email";
            $query_fetch = $conn->prepare($fetch_user_by_email);
            $query_fetch->bindValue(':email', $email,PDO::PARAM_STR);
            $query_fetch->execute();
            if($query_fetch->rowCount()) {
                $row = $query_fetch->fetch(PDO::FETCH_ASSOC);
                $check_password = password_verify($password, $row['password']);
                if($check_password) {
                    $jwt = new JwtHandler();
                    $token = $jwt->jwtEncodeData(
                        'http://localhost/RESTfulAPIWebServices/',
                        array("user_id"=> $row['id'])
                    );
                    $returnData = [
                        'success' => 1,
                        'message' => 'You have successfully logged in.',
                        'token' => $token
                    ];
                } else {
                    $returnData = messages(0, 422, 'Invalid password!');
                }
            } else {
                $returnData = messages(0, 422, 'Invalid email address!');
            }
        }
        catch(PDOException $e) {
            $returnData = messages(0, 500, $e->getMessage());
        }
    }
}

echo json_encode($returnData);

?>