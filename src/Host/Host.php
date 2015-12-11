<?php

namespace HtaccessFirewall\Host;

interface Host
{
    /**
     * Compare equality with another Host.
     *
     * @param Host $host
     *
     * @return bool
     */
    public function equals(Host $host);

    /**
     * Get string representation of Host.
     *
     * @return string
     */
    public function toString();
}
