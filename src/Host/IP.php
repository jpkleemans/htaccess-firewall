<?php

namespace HtaccessFirewall\Host;

use HtaccessFirewall\Host\Exception\InvalidArgumentException;

class IP implements Host
{
    const IPV4 = 'IPv4';
    const IPV6 = 'IPv6';

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
     * Get the version (IPv4 or IPv6) of the IP.
     *
     * @return string
     */
    public function getVersion()
    {
        $isIPv4 = filter_var($this->toString(), FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
        if ($isIPv4 !== false) {
            return self::IPV4;
        }

        return self::IPV6;
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
