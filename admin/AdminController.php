<?php
require_once '../model/Database.php';
require_once '../model/ProductTable.php';
require_once '../model/CustomerTable.php';
require_once '../model/CountryTable.php';
require_once '../model/Validator.php';
require_once '../util/Util.php';

class AdminController
{
    private $action;
    private $db;
    private $validator;

    public function __construct()
    {
        $this->db = Database::connectToDatabase();
        $this->action = Util::getAction();

        $this->validator = new Validator();
        $this->validator->addFields([
            'first_name',
            'last_name',
            'address',
            'city',
            'state',
            'email',
            'password',
            'postal_code',
            'phone',
            'email',
            'password'
        ]);
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
        $customers = [];
        include '../view/admin/customer_search.php';
    }

    private function processDisplayCustomer()
    {
        $customer_id = filter_input(INPUT_POST, 'customer_id', FILTER_VALIDATE_INT);
        $customer_table = new CustomerTable($this->db);
        $customer = $customer_table->getCustomer($customer_id);

        $country_table = new CountryTable($this->db);
        $countries = $country_table->getCountryCodeAndNameAssociativeArray();

        $fields = $this->validator->getFields();

        include '../view/admin/customer_display.php';
    }

    private function processUpdateCustomer()
    {
        $customer_id = filter_input(INPUT_POST, 'customer_id', FILTER_VALIDATE_INT);

        $first_name = filter_input(INPUT_POST, 'first_name');
        $this->validator->checkText('first_name', $first_name, true, 1, 50);

        $last_name = filter_input(INPUT_POST, 'last_name');
        $this->validator->checkText('last_name', $last_name, true, 1, 50);

        $address = filter_input(INPUT_POST, 'address');
        $this->validator->checkText('address', $address, true, 1, 50);

        $city = filter_input(INPUT_POST, 'city');
        $this->validator->checkText('city', $city, true, 1, 50);

        $state = filter_input(INPUT_POST, 'state');
        $this->validator->checkText('state', $state, true, 1, 50);

        $postal_code = filter_input(INPUT_POST, 'postal_code');
        $this->validator->checkText('postal_code', $postal_code, true, 1, 20);

        $country_code = filter_input(INPUT_POST, 'country_code');
        // no validation required

        $phone = filter_input(INPUT_POST, 'phone');
        $this->validator->checkPhone('phone', $phone);

        $email = filter_input(INPUT_POST, 'email');
        $this->validator->checkEmail('email', $email);

        $password = filter_input(INPUT_POST, 'password');
        $this->validator->checkText('password', $password, true, 6, 20);

        $fields = $this->validator->getFields();

        $customer_table = new CustomerTable($this->db);
        if ($this->validator->foundErrors()) {
            $customer = [
                'customerID' => $customer_id,
                'firstName' => $first_name,
                'lastName' => $last_name,
                'address' => $address,
                'city' => $city,
                'state' => $state,
                'postalCode' => $postal_code,
                'countryCode' => $country_code,
                'phone' => $phone,
                'email' => $email,
                'password' => $password
            ];

            $country_table = new CountryTable($this->db);
            $countries = $country_table->getCountryCodeAndNameAssociativeArray();

            include '../view/admin/customer_display.php';
            return;
        } else {
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
