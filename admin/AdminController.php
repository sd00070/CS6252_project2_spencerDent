<?php
require_once '../model/Database.php';
require_once '../model/ProductTable.php';
require_once '../model/CustomerTable.php';
require_once '../model/CountryTable.php';
require_once '../util/Util.php';

class AdminController
{
    private $action;

    public function __construct()
    {
        $this->action = '';
        $this->db = new Database();
        if (!$this->db->isConnected()) {
            $error_message = $this->db->getErrorMessage();
            include '../view/errors/database_error.php';
            exit();
        }
    }

    public function invoke()
    {
        // get the action to be processed
        $this->action = Util::getAction($this->action);

        switch ($this->action) {
            case 'under_construction':
                include '../view/under_construction.php';
                break;
            case 'list_products':
                $this->processListProducts();
                break;
            case 'delete_product':
                $this->processDeleteProduct();
                break;
            case 'show_add_form':
                $this->processShowAddForm();
                break;
            case 'add_product':
                $this->processAddProduct();
                break;
            case 'customer_search':
                $this->processCustomerSearch();
                break;
            case 'display_customer':
                $this->processDisplayCustomer();
                break;
            case 'update_customer':
                $this->processUpdateCustomer();
                break;
            case 'display_customers':
                $this->processDisplayCustomers();
                break;
            default:
                $this->processAdminMenu();
                break;
        }
    }

    /****************************************************************
     * Process Request
     ***************************************************************/
    private function processAdminMenu()
    {
        include '../view/admin/admin_menu.php';
    }

    private function processListProducts()
    {
        $product_table = new ProductTable($this->db);
        $products = $product_table->get_products();
        include '../view/admin/list_products.php';
    }

    private function processDeleteProduct()
    {
        $product_code = filter_input(INPUT_POST, 'product_code');
        $product_table = new ProductTable($this->db);
        $product_table->delete_product($product_code);
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
            $product_table->add_product($code, $name, $version, $release_date);
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
        $customer = $customer_table->get_customer($customer_id);

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
            $customer_table->update_customer(
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
            $customers = $customer_table->get_customers_by_last_name($last_name);
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
            $customers = $customer_table->get_customers_by_last_name($last_name);
            if (count($customers) == 0) {
                $message = 'No customer found';
            }
        }
        include '../view/admin/customer_search.php';
    }
}
