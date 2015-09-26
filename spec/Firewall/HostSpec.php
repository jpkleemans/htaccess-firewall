<?php

namespace spec\HtaccessFirewall\Firewall;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class HostSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedThrough('fromString', ['123.0.0.1']);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('HtaccessFirewall\Firewall\Host');
    }

    function it_should_allow_ipv4_addresses()
    {
        $this->beConstructedThrough('fromString', ['123.0.0.1']);
        $this->shouldNotThrow('HtaccessFirewall\Firewall\Exception\InvalidArgumentException')->duringInstantiation();
    }

    function it_should_allow_ipv6_addresses()
    {
        $this->beConstructedThrough('fromString', ['3ffe:6a88:85a3:08d3:1319:8a2e:0370:7344']);
        $this->shouldNotThrow('HtaccessFirewall\Firewall\Exception\InvalidArgumentException')->duringInstantiation();
    }

    function it_should_allow_domain_names()
    {
        $this->beConstructedThrough('fromString', ['domain.com']);
        $this->shouldNotThrow('HtaccessFirewall\Firewall\Exception\InvalidArgumentException')->duringInstantiation();
    }

    function it_should_not_allow_invalid_host_names()
    {
        $this->beConstructedThrough('fromString', ['123..0.0.1']);
        $this->shouldThrow('HtaccessFirewall\Firewall\Exception\InvalidArgumentException')->duringInstantiation();

        $this->beConstructedThrough('fromString', ['1200::AB00:1234::2552:7777:1313']);
        $this->shouldThrow('HtaccessFirewall\Firewall\Exception\InvalidArgumentException')->duringInstantiation();

        $this->beConstructedThrough('fromString', ['domain..com']);
        $this->shouldThrow('HtaccessFirewall\Firewall\Exception\InvalidArgumentException')->duringInstantiation();
    }

    function it_converts_itself_to_a_string()
    {
        $this->toString()->shouldBe('123.0.0.1');
    }

    function it_is_initializable_from_the_current_request()
    {
        $this->beConstructedThrough('fromCurrentRequest', []);
        $this->shouldNotThrow('HtaccessFirewall\Firewall\Exception\InvalidArgumentException')->duringInstantiation();
    }

    function it_compares_equality_with_another_host()
    {
        $host1 = $this::fromString('123.0.0.1');
        $this->equals($host1)->shouldBe(true);

        $host2 = $this::fromString('192.0.0.1');
        $this->equals($host2)->shouldBe(false);
    }
}
