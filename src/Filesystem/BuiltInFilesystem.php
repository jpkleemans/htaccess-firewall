<?php

namespace HtaccessFirewall\Filesystem;

/**
 * Filesystem using PHP's built-in filesystem functions.
 */
class BuiltInFilesystem implements FileSystem
{
    /**
     * Initialize BuiltInFilesystem.
     *
     * @param bool $writeLock whether to use a write lock (LOCK_EX).
     */
    public function __construct($writeLock = true)
    {
        // TODO: write logic here
    }

    /**
     * Read a file into an array.
     *
     * @param string $file Path to the file.
     *
     * @return array
     */
    public function read($file)
    {
        // TODO: Implement read() method.
    }

    /**
     * Write an array to a file.
     *
     * @param string $file Path to the file.
     * @param array $lines Array of lines to write to the file.
     */
    public function write($file, $lines)
    {
        // TODO: Implement write() method.
    }

    /**
     * Check whether a file exists.
     *
     * @param string $file Path to the file.
     *
     * @return bool
     */
    public function exists($file)
    {
        // TODO: Implement exists() method.
    }

    /**
     * Check whether a file exists and is readable.
     *
     * @param string $file Path to the file.
     *
     * @return bool
     */
    public function readable($file)
    {
        // TODO: Implement readable() method.
    }

    /**
     * Check whether a file exists and is writable.
     *
     * @param string $file Path to the file.
     *
     * @return bool
     */
    public function writable($file)
    {
        // TODO: Implement writable() method.
    }
}
