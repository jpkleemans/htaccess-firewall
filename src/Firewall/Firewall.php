<?php

namespace HtaccessFirewall\Firewall;

interface Firewall
{
    /**
     * Block host.
     *
     * @param Host $host
     */
    public function block(Host $host);

    /**
     * Unblock host.
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
