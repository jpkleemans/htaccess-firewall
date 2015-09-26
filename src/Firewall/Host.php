<?php

namespace HtaccessFirewall\Firewall;

use HtaccessFirewall\Firewall\Exception\InvalidArgumentException;

/**
 * Host value object.
 */
class Host
{
    /**
     * @var string
     */
    private $value;

    /**
     * Initialize Host.
     *
     * @param $value
     *
     * @throws InvalidArgumentException
     */
    private function __construct($value)
    {
        if (!$this->validate($value)) {
            throw new InvalidArgumentException('The first parameter of Host must be a valid IP address or domain name.');
        };

        $this->value = $value;
    }

    /**
     * Create Host from string.
     *
     * @param $string
     *
     * @return Host
     */
    public static function fromString($string)
    {
        return new self($string);
    }

    /**
     * Create Host from string.
     *
     * @return Host
     */
    public static function fromCurrentRequest()
    {
        return new self($_SERVER['REMOTE_ADDR']);
    }

    /**
     * Get string representation of Host.
     *
     * @return string
     */
    public function toString()
    {
        return $this->value;
    }

    /**
     * Compare equality with another host.
     *
     * @param Host $host
     *
     * @return bool
     */
    public function equals(Host $host)
    {
        return $host->toString() === $this->value;
    }

    /**
     * Validate on IP address or domain name.
     *
     * @param $value
     *
     * @return bool
     */
    private function validate($value)
    {
        // PHP built-in IP validation
        if (filter_var($value, FILTER_VALIDATE_IP) !== false) {
            return true;
        }

        // Regex Hostname validation. Valid per RFC 1123.
        $validHostnameRegex = '/^(([a-zA-Z0-9]|[a-zA-Z0-9][a-zA-Z0-9\\-]*[a-zA-Z0-9])\\.)*([A-Za-z0-9]|[A-Za-z0-9][A-Za-z0-9\\-]*[A-Za-z0-9])$/';
        if (preg_match($validHostnameRegex, $value)) {
            return true;
        }

        return false;
    }

    /**
     * Cast Host to string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }
}
