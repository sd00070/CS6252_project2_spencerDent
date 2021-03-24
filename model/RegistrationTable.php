<?php
require_once 'Database.php';

class RegistrationTable
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    function add_registration($customer_id, $product_code)
    {
        $date = date('Y-m-d');  // get current date in yyyy-mm-dd format
        $query = 'INSERT INTO registrations VALUES
            (:customer_id, :product_code, :date)';
        $statement = $this->db->getDB()->prepare($query);
        $statement->bindValue(':customer_id', $customer_id);
        $statement->bindValue(':product_code', $product_code);
        $statement->bindValue(':date', $date);
        $statement->execute();
        $statement->closeCursor();
    }
}
