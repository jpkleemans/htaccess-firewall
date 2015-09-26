<?php

namespace HtaccessFirewall\Filesystem;

/**
 * Filesystem using PHP's built-in filesystem functions.
 */
class BuiltInFilesystem implements FileSystem
{
    /**
     * @var bool
     */
    private $writeLock;

    /**
     * Initialize BuiltInFilesystem.
     *
     * @param bool $writeLock whether to use a write lock (LOCK_EX).
     */
    public function __construct($writeLock = true)
    {
        $this->writeLock = $writeLock;
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
        return file($file, FILE_IGNORE_NEW_LINES);
    }

    /**
     * Write an array to a file.
     *
     * @param string $file Path to the file.
     * @param array $lines Array of lines to write to the file.
     */
    public function write($file, $lines)
    {
        $contents = implode(PHP_EOL, $lines);
        file_put_contents($file, $contents, $this->writeLock ? LOCK_EX : 0);
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
        return file_exists($file);
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
        return is_readable($file);
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
        return is_writable($file);
    }
}
