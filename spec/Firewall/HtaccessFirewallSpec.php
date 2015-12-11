<?php

namespace spec\HtaccessFirewall\Firewall;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use HtaccessFirewall\Filesystem\Filesystem;
use HtaccessFirewall\Host\IP;

class HtaccessFirewallSpec extends ObjectBehavior
{
    function let(Filesystem $fileSystem)
    {
        $fileSystem->read('path/to/.htaccess')
            ->willReturn(array(
                '# BEGIN Firewall',
                'order allow,deny',
                'deny from 123.0.0.1',
                'deny from 123.0.0.2',
                'allow from all',
                '# END Firewall'
            ));

        $this->beConstructedWith('path/to/.htaccess', $fileSystem);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('HtaccessFirewall\Firewall\HtaccessFirewall');
        $this->shouldImplement('HtaccessFirewall\Firewall\Firewall');
    }

    function it_blocks_a_host($fileSystem)
    {
        $fileSystem->write('path/to/.htaccess', array(
            '# BEGIN Firewall',
            'order allow,deny',
            'deny from 123.0.0.1',
            'deny from 123.0.0.2',
            'deny from 123.0.0.3',
            'allow from all',
            '# END Firewall'
        ))->shouldBeCalled();

        $this->deny(IP::fromString('123.0.0.3'));
    }

    function it_does_not_block_an_already_blocked_host($fileSystem)
    {
        $fileSystem->write('path/to/.htaccess', array(
            '# BEGIN Firewall',
            'order allow,deny',
            'deny from 123.0.0.1',
            'deny from 123.0.0.2',
            'allow from all',
            '# END Firewall'
        ))->shouldBeCalled();

        $this->deny(IP::fromString('123.0.0.1'));
    }

    function it_blocks_a_host_for_the_first_time($fileSystem)
    {
        $fileSystem->read('path/to/.htaccess')->willReturn(array());

        $fileSystem->write('path/to/.htaccess', array(
            '# BEGIN Firewall',
            'order allow,deny',
            'deny from 123.0.0.1',
            'allow from all',
            '# END Firewall'
        ))->shouldBeCalled();

        $this->deny(IP::fromString('123.0.0.1'));
    }

    function it_does_not_block_when_end_marker_is_not_found($fileSystem)
    {
        $fileSystem->read('path/to/.htaccess')
            ->willReturn(array(
                '# BEGIN Firewall',
                'order allow,deny',
                'deny from 123.0.0.1',
                'deny from 123.0.0.2',
                'allow from all',
                //'# END Firewall'
            ));

        $this->shouldThrow('HtaccessFirewall\Filesystem\Exception\FileException')
            ->during('deny', array(IP::fromString('123.0.0.1')));
    }

    function it_unblocks_a_host($fileSystem)
    {
        $fileSystem->write('path/to/.htaccess', array(
            '# BEGIN Firewall',
            'order allow,deny',
            'deny from 123.0.0.2',
            'allow from all',
            '# END Firewall'
        ))->shouldBeCalled();

        $this->undeny(IP::fromString('123.0.0.1'));
    }

    function it_gets_all_blocked_hosts()
    {
        $hosts = $this->getDenied();

        $hosts->shouldBeArray();
        $hosts->shouldHaveCount(2);
        $hosts->shouldContain('123.0.0.1');
    }
}
