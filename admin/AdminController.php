<?php
require_once '../model/Database.php';
require_once '../model/ProductTable.php';
require_once '../model/CustomerTable.php';
require_once '../model/CountryTable.php';
require_once '../util/Util.php';

class AdminController
{
    private $action;
    private $db;

    public function __construct()
    {
        $this->action = Util::getAction();
        $this->db = Database::connectToDatabase();
    }

    public function invoke()
    {
        // get the action to be processed
        $this->action = Util::getAction($this->action);

        match ($this->action) {
            'under_construction' => $this->processUnderConstruction(),
            'list_products' => $this->processListProducts(),
            'delete_product' => $this->processDeleteProduct(),
            'show_add_form' => $this->processShowAddForm(),
            'add_product' => $this->processAddProduct(),
            'customer_search' => $this->processCustomerSearch(),
            'display_customer' => $this->processDisplayCustomer(),
            'update_customer' => $this->processUpdateCustomer(),
            'display_customers' => $this->processDisplayCustomers(),
            default => $this->processAdminMenu()
        };
    }

    /****************************************************************
     * Process Request
     ***************************************************************/
    private function processAdminMenu()
    {
        include '../view/admin/admin_menu.php';
    }

    private function processUnderConstruction()
    {
        include '../view/under_construction.php';
    }

    private function processListProducts()
    {
        $product_table = new ProductTable($this->db);
        $products = $product_table->getProducts();
        include '../view/admin/list_products.php';
    }

    private function processDeleteProduct()
    {
        $product_code = filter_input(INPUT_POST, 'product_code');
        $product_table = new ProductTable($this->db);
        $product_table->deleteProduct($product_code);
        header("Location: .?action=list_products");
    }

    private function processShowAddForm()
    {
        include '../view/admin/product_add.php';
    }

    private function processAddProduct()
    {
        $code = filter_input(INPUT_POST, 'code');
        $name = filter_input(INPUT_POST, 'name');
        $version = filter_input(INPUT_POST, 'version', FILTER_VALIDATE_FLOAT);
        $release_date = filter_input(INPUT_POST, 'release_date');

        // Validate the inputs
        if (
            $code === NULL || $name === FALSE ||
            $version === NULL || $version === FALSE ||
            $release_date === NULL
        ) {
            $error = "Invalid product data. Check all fields and try again.";
            include('../view/errors/error.php');
        } else {
            $product_table = new ProductTable($this->db);
            $product_table->addProduct($code, $name, $version, $release_date);
            header("Location: .?action=list_products");
        }
    }

    private function processCustomerSearch()
    {
        $last_name = '';
        $customers = array();
        include '../view/admin/customer_search.php';
    }

    private function processDisplayCustomer()
    {
        $customer_id = filter_input(INPUT_POST, 'customer_id', FILTER_VALIDATE_INT);
        $customer_table = new CustomerTable($this->db);
        $customer = $customer_table->getCustomer($customer_id);

        $country_table = new CountryTable($this->db);
        $countries = $country_table->getCountryCodeAndNameAssociativeArray();

        include '../view/admin/customer_display.php';
    }

    private function processUpdateCustomer()
    {
        $customer_id = filter_input(INPUT_POST, 'customer_id', FILTER_VALIDATE_INT);
        $first_name = filter_input(INPUT_POST, 'first_name');
        $last_name = filter_input(INPUT_POST, 'last_name');
        $address = filter_input(INPUT_POST, 'address');
        $city = filter_input(INPUT_POST, 'city');
        $state = filter_input(INPUT_POST, 'state');
        $postal_code = filter_input(INPUT_POST, 'postal_code');
        $country_code = filter_input(INPUT_POST, 'country_code');
        $phone = filter_input(INPUT_POST, 'phone');
        $email = filter_input(INPUT_POST, 'email');
        $password = filter_input(INPUT_POST, 'password');

        if (empty($last_name)) {
            $error = 'You must enter a last name.';
            include('../view/errors/error.php');
        } else {
            $customer_table = new CustomerTable($this->db);
            $customer_table->updateCustomer(
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
            );
            $customer_table = new CustomerTable($this->db);
            $customers = $customer_table->getCustomersByLastName($last_name);
            include '../view/admin/customer_search.php';
        }
    }

    private function processDisplayCustomers()
    {
        $last_name = filter_input(INPUT_POST, 'last_name');
        if (empty($last_name)) {
            $message = 'Please enter a last name.';
        } else {
            $customer_table = new CustomerTable($this->db);
            $customers = $customer_table->getCustomersByLastName($last_name);
            if (count($customers) == 0) {
                $message = 'No customer found';
            }
        }
        include '../view/admin/customer_search.php';
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
