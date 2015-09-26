<?php

namespace HtaccessFirewall\Firewall;

/**
 * Firewall using Htaccess files.
 */
class HtaccessFirewall implements Firewall
{
    /**
     * Initialize HtaccessFirewall.
     *
     * @param $path
     * @param Filesystem $fileSystem
     */
    public function __construct($path, Filesystem $fileSystem = null)
    {
        // TODO: write logic here
    }

    /**
     * Block a host.
     *
     * @param Host $host
     */
    public function block(Host $host)
    {
        // TODO: Implement block() method.
    }

    /**
     * Unblock a host.
     *
     * @param Host $host
     */
    public function unblock(Host $host)
    {
        // TODO: Implement unblock() method.
    }

    /**
     * Get all blocked hosts.
     *
     * @return array
     */
    public function getBlocks()
    {
        // TODO: Implement getBlocks() method.
    }
}
