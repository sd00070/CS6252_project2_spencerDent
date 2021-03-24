<?php
require_once 'Database.php';

class ProductTable
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    function get_products()
    {
        $query = 'SELECT * FROM products
              ORDER BY name';
        $statement = $this->db->getDB()->prepare($query);
        $statement->execute();
        $products = $statement->fetchAll();
        $statement->closeCursor();
        return $products;
    }

    function delete_product($product_code)
    {
        $query = 'DELETE FROM products
              WHERE productCode = :product_code';
        $statement = $this->db->getDB()->prepare($query);
        $statement->bindValue(':product_code', $product_code);
        $statement->execute();
        $statement->closeCursor();
    }

    function add_product($code, $name, $version, $release_date)
    {
        $query = 'INSERT INTO products
                 (productCode, name, version, releaseDate)
              VALUES
                 (:code, :name, :version, :release_date)';
        $statement = $this->db->getDB()->prepare($query);
        $statement->bindValue(':code', $code);
        $statement->bindValue(':name', $name);
        $statement->bindValue(':version', $version);
        $statement->bindValue(':release_date', $release_date);
        $statement->execute();
        $statement->closeCursor();
    }
}
