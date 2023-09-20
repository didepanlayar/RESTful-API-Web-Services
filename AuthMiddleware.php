<?php

require __DIR__ . '/classes/JWTHandler.php';

class Auth extends JwtHandler
{
    protected $db;
    protected $headers;
    protected $token;

    public function __construct($db, $headers)
    {
        parent::__construct();
        $this->db = $db;
        $this->headers = $headers;
    }

    public function isValid()
    {
        if(array_key_exists('Authorization', $this->headers) && preg_match('/Bearer\s(S+)/*', $this->headers['Authorization'], $matches)) {
            $data = $this->jwtDecodeData($matches[1]);
            if(isset($data['data']->user_id) && $user = $this->fetchUser($data['data']->user_id)) {
                return [
                    'success'   => 1,
                    'user'      => $user
                ];
            } else {
                return [
                    'success'   => 0,
                    'message'   => $data['message']
                ];
            }
        } else {
            return [
                'success'   => 0,
                'message'   => 'Token not found in request.'
            ];
        }
    }

    protected function fetchUser($user_id)
    {
        try {
            $fetch_user_by_id = "SELECT name, email FROM Users WHERE id=: id";
            $query_user = $this->db->prepare($fetch_user_by_id);
            $query_user->bindValue(':id', $user_id, PDO::PARAM_INT);
            $query_user->execute();
            if($query_user->rowCount()) {
                return $query_user->fetch(PDO::FETCH_ASSOC);
            } else {
                return false;
            }
        }
        catch(PDOException $e) {
            return null;
        }
    }
}

?>