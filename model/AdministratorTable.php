<?php
require_once 'Database.php';

class AdministratorTable
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function isValidUserLogin($username, $password)
    {
        $query = 'SELECT password FROM administrators
                  WHERE username = :username';
        $statement = $this->db->getDB()->prepare($query);
        $statement->bindValue(':username', $username);
        $statement->execute();
        $row = $statement->fetch();
        $statement->closeCursor();
        if (!$row) {
            return false;
        }
        $hash = $row['password'];
        return password_verify($password, $hash);
    }
}
