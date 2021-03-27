<?php
require_once 'Database.php';

class AdministratorTable
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function verifyAdmin($username, $password)
    {
        $query = 'SELECT * FROM administrators
                  WHERE username = :username
                  AND password = :password';
        $statement = $this->db->getDB()->prepare($query);
        $statement->bindValue(':username', $username);
        $statement->bindValue(':password', $password);
        $statement->execute();
        $customer = $statement->fetch();
        $statement->closeCursor();
        return $customer;
    }
}
