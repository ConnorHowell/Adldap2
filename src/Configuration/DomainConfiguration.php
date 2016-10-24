<?php

namespace Adldap\Configuration;

use Adldap\Configuration\Validators\ArrayValidator;
use Adldap\Configuration\Validators\StringValidator;
use Adldap\Configuration\Validators\BooleanValidator;
use Adldap\Configuration\Validators\IntegerValidator;
use Adldap\Contracts\Connections\ConnectionInterface;

class DomainConfiguration
{
    /**
     * The configuration values array.
     *
     * The default values for each key indicate the type of value it requires.
     *
     * @var array
     */
    protected $values = [
        'domain_controllers' => [],
        'timeout' => 5,
        'port' => ConnectionInterface::PORT,
        'base_dn' => '',
        'use_ssl' => false,
        'use_tls' => false,
        'follow_referrals' => false,
        'account_prefix' => '',
        'account_suffix' => '',
        'admin_username' => '',
        'admin_password' => '',
        'admin_account_suffix' => '',
    ];

    /**
     * Constructor.
     *
     * @param array $values
     *
     * @throws ConfigurationException
     */
    public function __construct(array $values = [])
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * Sets a configuration option.
     *
     * Throws an exception if the specified option does
     * not exist, or if it's an invalid type.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @throws ConfigurationException
     */
    public function set($key, $value)
    {
        if($this->validate($key, $value)) {
            $this->values[$key] = $value;
        }
    }

    /**
     * Returns the value for the specified configuration key.
     *
     * Throws an exception if the specified option does not exist.
     *
     * @param string $key
     *
     * @return mixed
     *
     * @throws ConfigurationException
     */
    public function get($key)
    {
        if ($this->has($key)) {
            return $this->values[$key];
        }

        throw new ConfigurationException("Option {$key} does not exist.");
    }

    /**
     * Checks if a configuration key exists.
     *
     * @param string $key
     *
     * @return bool
     */
    public function has($key)
    {
        return array_key_exists($key, $this->values);
    }

    /**
     * Validates the new configuration value against its
     * default value to ensure it's the correct type.
     *
     * If an invalid type is given, an exception is thrown.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return bool
     *
     * @throws ConfigurationException
     */
    protected function validate($key, $value)
    {
        $default = $this->get($key);

        if (is_array($default)) {
            $validator = new ArrayValidator($key, $value);
        } elseif (is_int($default)) {
            $validator = new IntegerValidator($key, $value);
        } elseif (is_bool($default)) {
            $validator = new BooleanValidator($key, $value);
        } else {
            $validator = new StringValidator($key, $value);
        }

        return $validator->validate();
    }
}