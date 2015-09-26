<?php

namespace HtaccessFirewall\Firewall;

interface Firewall
{
    /**
     * Block a host.
     *
     * @param Host $host
     */
    public function block(Host $host);

    /**
     * Unblock a host.
     *
     * @param Host $host
     */
    public function unblock(Host $host);

    /**
     * Get all blocked hosts.
     *
     * @return array
     */
    public function getBlocks();
}
