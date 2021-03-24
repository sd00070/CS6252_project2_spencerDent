<?php
require_once 'Database.php';

/**
 * Provides a model to access the countries table in the tech_support database.
 * 
 * Thanks to https://phpdelusions.net/pdo/fetch_modes for informing me about PDO::FETCH_KEY_PAIR.
 * It made my life so much easier.
 *
 * @author Spencer Dent
 * @version 2021-03-24
 */
class CountryTable
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * @return 
     */
    public function getCountryCodeAndNameAssociativeArray()
    {
        $query = 'SELECT countryCode, countryName FROM countries';
        $statement = $this->db->getDB()->prepare($query);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_KEY_PAIR);
        $statement->closeCursor();
        return $result;
    }
}
