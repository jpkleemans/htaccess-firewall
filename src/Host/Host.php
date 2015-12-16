<?php

namespace HtaccessFirewall\Host;

interface Host
{
    /**
     * Compare equality with another Host.
     *
     * @param Host $other
     *
     * @return bool
     */
    public function equals(Host $other);

    /**
     * Get string representation of Host.
     *
     * @return string
     */
    public function toString();
}
