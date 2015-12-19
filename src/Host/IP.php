<?php

namespace HtaccessFirewall\Host;

use HtaccessFirewall\Host\Exception\InvalidArgumentException;

class IP implements Host
{
    /**
     * @var string
     */
    private $value;

    /**
     * Initialize IP.
     *
     * @param $value
     *
     * @throws InvalidArgumentException
     */
    private function __construct($value)
    {
        if (!self::validate($value)) {
            throw new InvalidArgumentException('The first parameter of IP must be a valid IP address.');
        }

        $this->value = $value;
    }

    /**
     * Create IP from string.
     *
     * @param $string
     *
     * @return IP
     */
    public static function fromString($string)
    {
        return new self($string);
    }

    /**
     * Create IP from current request.
     *
     * @return IP
     */
    public static function fromCurrentRequest()
    {
        return new self($_SERVER['REMOTE_ADDR']);
    }

    /**
     * Check if string is a valid IP.
     *
     * @param string $value
     *
     * @return bool
     */
    public static function validate($value)
    {
        return filter_var($value, FILTER_VALIDATE_IP) !== false;
    }

    /**
     * Compare equality with another Host.
     *
     * @param Host $other
     *
     * @return bool
     */
    public function equals(Host $other)
    {
        return $this->toString() === $other->toString();
    }

    /**
     * Get string representation of IP.
     *
     * @return string
     */
    public function toString()
    {
        return $this->value;
    }

    /**
     * Cast IP to string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }
}
