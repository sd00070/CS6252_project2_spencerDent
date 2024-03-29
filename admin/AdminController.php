<?php
require_once '../model/Database.php';
require_once '../model/AdministratorTable.php';
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
        $this->ensureSecureConnection();
        $this->startSession();
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
            'show_admin_login' => $this->showAdminLogin(),
            'login_admin' => $this->loginAdmin(),
            'logout_admin' => $this->logoutAdmin(),
            'under_construction' => $this->showUnderConstruction(),
            'list_products' => $this->displayProductList(),
            'delete_product' => $this->processDeleteProduct(),
            'show_add_form' => $this->showAddProductForm(),
            'add_product' => $this->processAddProduct(),
            'customer_search' => $this->showCustomerSearch(),
            'display_customer' => $this->displayCustomer(),
            'update_customer' => $this->processUpdateCustomer(),
            'display_customers' => $this->displayCustomerSearchResults(),
            default => $this->showAdminLogin()
        };
    }

    private function isAuthorized()
    {
        return isset($_SESSION['admin_isValid']) &&
            $_SESSION['admin_isValid'] &&
            isset($_SESSION['admin_username']);
    }

    /****************************************************************
     * Process Request
     ***************************************************************/
    private function showAdminLogin()
    {
        if ($this->isAuthorized()) {
            $this->showAdminMenu();
            return;
        }

        include '../view/admin/admin_login.php';
    }

    private function loginAdmin()
    {
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);

        $admin_table = new AdministratorTable($this->db);

        if ($admin_table->isValidUserLogin($username, $password)) {
            $_SESSION['admin_isValid'] = true;
            $_SESSION['admin_username'] = $username;

            $admin_username = $username;
            include '../view/admin/admin_menu.php';
            return;
        }

            $message = 'Invalid email or password.';
            include '../view/admin/admin_login.php';
    }

    private function logoutAdmin()
    {
        unset($_SESSION['admin_isValid']);
        unset($_SESSION['admin_username']);
        $message = 'You have successfully logged out.';
        include '../view/admin/admin_login.php';
    }

    private function showAdminMenu()
    {
        if (!$this->isAuthorized()) {
            $message = 'You must be logged in to perform this action.';
            include '../view/admin/admin_login.php';
            return;
        }
        $admin_username = $_SESSION['admin_username'];

        include '../view/admin/admin_menu.php';
    }

    private function showUnderConstruction()
    {
        include '../view/under_construction.php';
    }

    private function displayProductList()
    {
        if (!$this->isAuthorized()) {
            $message = 'You must be logged in to perform this action.';
            include '../view/admin/admin_login.php';
            return;
        }
        $admin_username = $_SESSION['admin_username'];

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

    private function showAddProductForm()
    {
        if (!$this->isAuthorized()) {
            $message = 'You must be logged in to perform this action.';
            include '../view/admin/admin_login.php';
            return;
        }
        $admin_username = $_SESSION['admin_username'];

        include '../view/admin/product_add.php';
    }

    private function processAddProduct()
    {
        if (!$this->isAuthorized()) {
            $message = 'You must be logged in to perform this action.';
            include '../view/admin/admin_login.php';
            return;
        }
        $admin_username = $_SESSION['admin_username'];

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

    private function showCustomerSearch()
    {
        if (!$this->isAuthorized()) {
            $message = 'You must be logged in to perform this action.';
            include '../view/admin/admin_login.php';
            return;
        }
        $admin_username = $_SESSION['admin_username'];

        $last_name = '';
        $customers = [];
        include '../view/admin/customer_search.php';
    }

    private function displayCustomer()
    {
        if (!$this->isAuthorized()) {
            $message = 'You must be logged in to perform this action.';
            include '../view/admin/admin_login.php';
            return;
        }
        $admin_username = $_SESSION['admin_username'];

        $customer_id = filter_input(INPUT_POST, 'customer_id', FILTER_VALIDATE_INT);
        $customer_table = new CustomerTable($this->db);
        $customer = $customer_table->getCustomer($customer_id);

        $customer['password'] = '******';

        $country_table = new CountryTable($this->db);
        $countries = $country_table->getCountryCodeAndNameAssociativeArray();

        $fields = $this->validator->getFields();

        include '../view/admin/customer_display.php';
    }

    private function processUpdateCustomer()
    {
        if (!$this->isAuthorized()) {
            $message = 'You must be logged in to perform this action.';
            include '../view/admin/admin_login.php';
            return;
        }
        $admin_username = $_SESSION['admin_username'];

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
        if ($password == '******') {
            $customer_table = new CustomerTable($this->db);
            $password = $customer_table->getPassword($customer_id);
        } else {
            $this->validator->checkText('password', $password, true, 6, 20);
            $password = password_hash($password, PASSWORD_BCRYPT);
        }

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

    private function displayCustomerSearchResults()
    {
        if (!$this->isAuthorized()) {
            $message = 'You must be logged in to perform this action.';
            include '../view/admin/admin_login.php';
            return;
        }
        $admin_username = $_SESSION['admin_username'];

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
        session_set_cookie_params(
            0,                      // lifetime - ends when the user closes the browser
            Util::getProjectPath()  // path
        );

        session_start();
    }

    private function ensureSecureConnection()
    {
        $https = filter_input(INPUT_SERVER, 'HTTPS');

        if (!$https) {
            $host = filter_input(INPUT_SERVER, 'HTTP_HOST');
            $uri = filter_input(INPUT_SERVER, 'REQUEST_URI');
            $url = 'https:' . $host . $uri;
            header("Location: $url");
            exit();
        }
    }
}
