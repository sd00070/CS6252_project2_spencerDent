<?php
class Database
{
    private $db;
    private $error_message;

    /**
     * connect to the database
     */
    public function __construct()
    {
        $dsn = 'mysql:host=localhost;dbname=tech_support';
        $username = 'ts_user';
        $password = 'pa55word';

        $this->error_message = '';
        try {
            $this->db = new PDO($dsn, $username, $password);
        } catch (PDOException $e) {
            $this->error_message = $e->getMessage();
        }
    }

    /**
     * check the connection to the database
     *
     * @return boolean - true if a connection to the database has been established
     */
    public function isConnected()
    {
        return ($this->db != Null);
    }

    public function getErrorMessage()
    {
        return $this->error_message;
    }

    public function getDB()
    {
        return $this->db;
    }

    public static function connectToDatabase()
    {
        $db = new Database();
        if (!$db->isConnected()) {
            $error_message = $db->getErrorMessage();
            include '../view/errors/database_error.php';
            exit();
        }
        return $db;
    }
}
