<?php

namespace HtaccessFirewall\Filesystem;

use HtaccessFirewall\Filesystem\Exception\FileNotFoundException;
use HtaccessFirewall\Filesystem\Exception\FileNotReadableException;
use HtaccessFirewall\Filesystem\Exception\FileNotWritableException;

interface Filesystem
{
    /**
     * Read a file into an array.
     *
     * @param string $file Path to the file.
     *
     * @throws FileNotFoundException
     * @throws FileNotReadableException
     *
     * @return array
     */
    public function read($file);

    /**
     * Write an array to a file.
     *
     * @param string $file Path to the file.
     *
     * @throws FileNotFoundException
     * @throws FileNotWritableException
     *
     * @param array $lines Array of lines to write to the file.
     */
    public function write($file, $lines);

    /**
     * Check whether a file exists.
     *
     * @param string $file Path to the file.
     *
     * @return bool
     */
    public function exists($file);

    /**
     * Check whether a file exists and is readable.
     *
     * @param string $file Path to the file.
     *
     * @return bool
     */
    public function readable($file);

    /**
     * Check whether a file exists and is writable.
     *
     * @param string $file Path to the file.
     *
     * @return bool
     */
    public function writable($file);
}
