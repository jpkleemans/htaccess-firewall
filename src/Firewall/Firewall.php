<?php

namespace HtaccessFirewall\Firewall;

use HtaccessFirewall\Host\Host;

interface Firewall
{
    /**
     * Deny host.
     *
     * @param Host $host
     */
    public function deny(Host $host);

    /**
     * Undeny host.
     *
     * @param Host $host
     */
    public function undeny(Host $host);

    /**
     * Get all denied hosts.
     *
     * @return string[]
     */
    public function getDenied();
}
