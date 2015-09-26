<?php

namespace HtaccessFirewall\Filesystem;

interface Filesystem
{
    /**
     * Read a file into an array.
     *
     * @param string $file Path to the file.
     *
     * @return array
     */
    public function read($file);

    /**
     * Write an array to a file.
     *
     * @param string $file Path to the file.
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
