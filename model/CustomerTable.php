<?php
require_once 'Database.php';

class CustomerTable
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getCustomer($customer_id)
    {
        $query = 'SELECT * FROM customers
              WHERE customerID = :customer_id';
        $statement = $this->db->getDB()->prepare($query);
        $statement->bindValue(':customer_id', $customer_id);
        $statement->execute();
        $customer = $statement->fetch();
        $statement->closeCursor();
        return $customer;
    }

    public function getPassword($customer_id)
    {
        $query = 'SELECT password FROM customers
                  WHERE customerID = :customer_id';
        $statement = $this->db->getDB()->prepare($query);
        $statement->bindValue(':customer_id', $customer_id);
        $statement->execute();
        $password = $statement->fetch();
        $statement->closeCursor();
        return $password['password'];
    }

    public function getCustomerByEmail($email)
    {
        $query = 'SELECT * FROM customers
              WHERE email = :email';
        $statement = $this->db->getDB()->prepare($query);
        $statement->bindValue(':email', $email);
        $statement->execute();
        $customer = $statement->fetch();
        $statement->closeCursor();
        return $customer;
    }

    public function getCustomersByLastName($last_name)
    {
        $query = 'SELECT * FROM customers
              WHERE lastName = :last_name
              ORDER BY lastName';
        $statement = $this->db->getDB()->prepare($query);
        $statement->bindValue(':last_name', $last_name);
        $statement->execute();
        $customers = $statement->fetchAll();
        $statement->closeCursor();
        return $customers;
    }

    public function updateCustomer(
        $customer_id,
        $first_name,
        $last_name,
        $address,
        $city,
        $state,
        $postal_code,
        $country_code,
        $phone,
        $email,
        $password
    ) {
        $query = 'UPDATE customers
              SET firstName = :first_name,
                  lastName = :last_name,
                  address = :address,
                  city = :city,
                  state = :state,
                  postalCode = :postal_code,
                  countryCode = :country_code,
                  phone = :phone,
                  email = :email,
                  password = :password
              WHERE customerID = :customer_id';
        $statement = $this->db->getDB()->prepare($query);
        $statement->bindValue(':first_name', $first_name);
        $statement->bindValue(':last_name', $last_name);
        $statement->bindValue(':address', $address);
        $statement->bindValue(':city', $city);
        $statement->bindValue(':state', $state);
        $statement->bindValue(':postal_code', $postal_code);
        $statement->bindValue(':country_code', $country_code);
        $statement->bindValue(':phone', $phone);
        $statement->bindValue(':email', $email);
        $statement->bindValue(':password', $password);
        $statement->bindValue(':customer_id', $customer_id);
        $statement->execute();
        $statement->closeCursor();
    }

    public function isValidUserLogin($email, $password)
    {
        $query = 'SELECT password FROM customers
                  WHERE email = :email';
        $statement = $this->db->getDB()->prepare($query);
        $statement->bindValue(':email', $email);
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
