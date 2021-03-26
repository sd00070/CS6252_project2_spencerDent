<?php
require_once '../model/Database.php';
require_once '../model/ProductTable.php';
require_once '../model/CustomerTable.php';
require_once '../model/RegistrationTable.php';
require_once '../util/Util.php';

class CustomerController
{
    private $action;
    private $db;

    public function __construct()
    {
        $this->startSession();
        $this->db = Database::connectToDatabase();
        $this->action = Util::getAction();
    }

    public function invoke()
    {
        // get the action to be processed
        $this->action = Util::getAction($this->action);

        match ($this->action) {
            'customer_login' => $this->processCustomerLogin(),
            'get_customer' => $this->processGetCustomer(),
            'register_product' => $this->processRegisterProduct(),
            default => $this->processCustomerLogin()
        };
    }

    /****************************************************************
     * Process Request
     ***************************************************************/
    private function processCustomerLogin()
    {
        $email = '';
        include '../view/customer/customer_login.php';
    }

    private function processGetCustomer()
    {
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);

        $customer_table = new CustomerTable($this->db);
        $customer = $customer_table->verifyCustomer($email, $password);
        
        if ($customer == false) {
            $message = 'Invalid email address or password';
            include '../view/customer/customer_login.php';
        } else {
            $product_table = new ProductTable($this->db);
            $products = $product_table->getProducts();
            include '../view/customer/product_register.php';
        }
    }

    private function processRegisterProduct()
    {
        $customer_id = filter_input(INPUT_POST, 'customer_id', FILTER_VALIDATE_INT);
        $product_code = filter_input(INPUT_POST, 'product_code');
        $registration_table = new RegistrationTable($this->db);
        $registration_table->addRegistration($customer_id, $product_code);
        $message = "Product ($product_code) was registered successfully.";
        include '../view/customer/product_register.php';
    }

    private function startSession()
    {
        session_start();
    }

    private function clearSession()
    {
        $_SESSION = [];
        session_destroy();
    }
}
