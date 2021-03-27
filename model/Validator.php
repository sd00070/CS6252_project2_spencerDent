<?php
require_once 'Fields.php';

class Validator
{
    private $fields;

    public function __construct()
    {
        $this->fields = new Fields();
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function foundErrors()
    {
        return $this->fields->hasErrors();
    }

    public function addField($name, $message = '')
    {
        $this->fields->addField($name, $message);
    }

    public function addFields($names_array = [])
    {
        foreach ($names_array as $field_name) {
            $this->addField($field_name);
        }
    }

    public function checkText(
        $field_name,
        $value,
        $required = true,
        $min = 1,
        $max = 255
    ) {

        // Get Field object
        $field = $this->fields->getField($field_name);

        // If field is not required and empty, remove error and exit
        if (!$required && empty($value)) {
            $field->clearErrorMessage();
            return;
        }

        // Check field and set or clear error message
        if ($required && empty($value)) {
            $field->setErrorMessage('Required');
        } else if (strlen($value) < $min) {
            $field->setErrorMessage('Too short');
        } else if (strlen($value) > $max) {
            $field->setErrorMessage('Too long');
        } else {
            $field->clearErrorMessage();
        }
    }

    public function checkPattern(
        $field_name,
        $value,
        $pattern,
        $message,
        $required = true
    ) {
        $field = $this->fields->getField($field_name);

        if (!$required && empty($value)) {
            $field->clearErrorMessage();
            return;
        }

        $match = preg_match($pattern, $value);
        if ($match === false) {
            $field->setErrorMessage('Error testing field.');
        } else if ($match != 1) {
            $field->setErrorMessage($message);
        } else {
            $field->clearErrorMessage();
        }
    }

    public function checkPhone($field_name, $phone)
    {
        $field = $this->fields->getField($field_name);

        $this->checkText($field_name, $phone);
        if ($field->hasError()) {
            return;
        }

        $pattern = '/^\(\d{3}\) ?\d{3}-\d{4}$/';

        $this->checkPattern(
            $field_name,
            $phone,
            $pattern,
            'Use (999) 999-9999 format.'
        );
    }

    public function checkEmail($field_name, $email)
    {
        $field = $this->fields->getField($field_name);

        $this->checkText($field_name, $email, true, 1, 50);
        if ($field->hasError()) {
            return;
        }

        // Split email address on @ sign and check parts
        $parts = explode('@', $email);
        if (count($parts) < 2) {
            $field->setErrorMessage('At sign required.');
            return;
        }
        if (count($parts) > 2) {
            $field->setErrorMessage('Only one at sign allowed.');
            return;
        }

        $local = $parts[0];
        $domain = $parts[1];

        // Check lengths of local and domain parts
        if (strlen($local) > 64) {
            $field->setErrorMessage('Username part too long.');
            return;
        }
        if (strlen($domain) > 255) {
            $field->setErrorMessage('Domain name part too long.');
            return;
        }

        // Patterns for address formatted local part
        $atom = '[[:alnum:]_!#$%&\'*+\/=?^`{|}~-]+';
        $dot_atom = '(\.' . $atom . ')*';
        $address = '(^' . $atom . $dot_atom . '$)';

        // Patterns for quoted text formatted local part
        $char = '([^\\\\"])';
        $esc  = '(\\\\[\\\\"])';
        $text = '(' . $char . '|' . $esc . ')+';
        $quoted = '(^"' . $text . '"$)';

        // Combined pattern for testing local part
        $localPattern = '/' . $address . '|' . $quoted . '/';

        // Call the pattern method and exit if it yields an error
        $this->checkPattern(
            $field_name,
            $local,
            $localPattern,
            'Invalid username part.'
        );
        if ($field->hasError()) {
            return;
        }

        // Patterns for domain part
        $hostname = '([[:alnum:]]([-[:alnum:]]{0,62}[[:alnum:]])?)';
        $hostnames = '(' . $hostname . '(\.' . $hostname . ')*)';
        $top = '\.[[:alnum:]]{2,6}';
        $domainPattern = '/^' . $hostnames . $top . '$/';

        // Call the pattern method
        $this->checkPattern(
            $field_name,
            $domain,
            $domainPattern,
            'Invalid domain name part.'
        );
    }
}
