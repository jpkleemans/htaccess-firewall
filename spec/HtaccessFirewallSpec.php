<?php

namespace spec\HtaccessFirewall;

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
                'ErrorDocument 403 "You are blocked!"',
                'deny from 123.0.0.1',
                'deny from 123.0.0.2',
                'allow from all',
                '# END Firewall',
            ));

        $this->beConstructedWith('path/to/.htaccess', $fileSystem);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('HtaccessFirewall\HtaccessFirewall');
    }

    function it_blocks_a_host($fileSystem)
    {
        $fileSystem->write('path/to/.htaccess', array(
            '# BEGIN Firewall',
            'order allow,deny',
            'ErrorDocument 403 "You are blocked!"',
            'deny from 123.0.0.1',
            'deny from 123.0.0.2',
            'deny from 123.0.0.3',
            'allow from all',
            '# END Firewall',
        ))->shouldBeCalled();

        $this->deny(IP::fromString('123.0.0.3'));
    }

    function it_does_not_block_an_already_blocked_host($fileSystem)
    {
        $fileSystem->write('path/to/.htaccess', array(
            '# BEGIN Firewall',
            'order allow,deny',
            'ErrorDocument 403 "You are blocked!"',
            'deny from 123.0.0.1',
            'deny from 123.0.0.2',
            'allow from all',
            '# END Firewall',
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
            '# END Firewall',
        ))->shouldBeCalled();

        $this->deny(IP::fromString('123.0.0.1'));
    }

    function it_does_not_block_when_end_marker_is_not_found($fileSystem)
    {
        $fileSystem->read('path/to/.htaccess')
            ->willReturn(array(
                '# BEGIN Firewall',
                'order allow,deny',
                'ErrorDocument 403 "You are blocked!"',
                'deny from 123.0.0.1',
                'deny from 123.0.0.2',
                'allow from all',
                //'# END Firewall'
            ));

        $this->shouldThrow('HtaccessFirewall\Filesystem\Exception\FileException')
            ->during('deny', array(IP::fromString('123.0.0.1')));
    }

    function it_converts_to_correct_htaccess_syntax_when_blocking($fileSystem)
    {
        $fileSystem->read('path/to/.htaccess')
            ->willReturn(array(
                '# BEGIN Firewall',
                'ErrorDocument 403 "You are blocked!"',
                '<FilesMatch ".*\.(php|html?|css|js|jpe?g|png|gif)$">',
                'order deny,allow',
                'deny from 123.0.0.1',
                'deny from 123.0.0.2',
                '</FilesMatch>',
                '# END Firewall',
            ));

        $fileSystem->write('path/to/.htaccess', array(
            '# BEGIN Firewall',
            'order allow,deny',
            'ErrorDocument 403 "You are blocked!"',
            'deny from 123.0.0.1',
            'deny from 123.0.0.2',
            'deny from 123.0.0.3',
            'allow from all',
            '# END Firewall',
        ))->shouldBeCalled();

        $this->deny(IP::fromString('123.0.0.3'));
    }


    function it_unblocks_a_host($fileSystem)
    {
        $fileSystem->write('path/to/.htaccess', array(
            '# BEGIN Firewall',
            'order allow,deny',
            'ErrorDocument 403 "You are blocked!"',
            'deny from 123.0.0.2',
            'allow from all',
            '# END Firewall',
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

    function it_deactivates_all_blocks($fileSystem)
    {
        $fileSystem->write('path/to/.htaccess', array(
            '# BEGIN Firewall',
            '#ErrorDocument 403 "You are blocked!"',
            '#deny from 123.0.0.1',
            '#deny from 123.0.0.2',
            '# END Firewall',
        ))->shouldBeCalled();

        $this->deactivate();
    }

    function it_reactivates_all_blocks($fileSystem)
    {
        $fileSystem->read('path/to/.htaccess')
            ->willReturn(array(
                '# BEGIN Firewall',
                '#ErrorDocument 403 "You are blocked!"',
                '#deny from 123.0.0.1',
                '#deny from 123.0.0.2',
                '# END Firewall',
            ));

        $fileSystem->write('path/to/.htaccess', array(
            '# BEGIN Firewall',
            'order allow,deny',
            'ErrorDocument 403 "You are blocked!"',
            'deny from 123.0.0.1',
            'deny from 123.0.0.2',
            'allow from all',
            '# END Firewall',
        ))->shouldBeCalled();

        $this->reactivate();
    }

    function it_sets_the_403_message($fileSystem)
    {
        $fileSystem->write('path/to/.htaccess', array(
            '# BEGIN Firewall',
            'order allow,deny',
            'ErrorDocument 403 "Blocked!"',
            'deny from 123.0.0.1',
            'deny from 123.0.0.2',
            'allow from all',
            '# END Firewall',
        ))->shouldBeCalled();

        $this->set403Message("Blocked!");
    }

    function it_sets_the_403_message_for_the_first_time($fileSystem)
    {
        $fileSystem->read('path/to/.htaccess')
            ->willReturn(array(
                '# BEGIN Firewall',
                'order allow,deny',
                'deny from 123.0.0.1',
                'deny from 123.0.0.2',
                'allow from all',
                '# END Firewall',
            ));

        $fileSystem->write('path/to/.htaccess', array(
            '# BEGIN Firewall',
            'order allow,deny',
            'ErrorDocument 403 "Blocked!"',
            'deny from 123.0.0.1',
            'deny from 123.0.0.2',
            'allow from all',
            '# END Firewall',
        ))->shouldBeCalled();

        $this->set403Message("Blocked!");
    }

    function it_sanitates_403_message($fileSystem)
    {
        $fileSystem->write('path/to/.htaccess', array(
            '# BEGIN Firewall',
            'order allow,deny',
            'ErrorDocument 403 "multi line quoted string"',
            'deny from 123.0.0.1',
            'deny from 123.0.0.2',
            'allow from all',
            '# END Firewall',
        ))->shouldBeCalled();

        $this->set403Message('
            multi
            line
            "quoted"
            string
        ');
    }

    function it_removes_the_403_message($fileSystem)
    {
        $fileSystem->write('path/to/.htaccess', array(
            '# BEGIN Firewall',
            'order allow,deny',
            'deny from 123.0.0.1',
            'deny from 123.0.0.2',
            'allow from all',
            '# END Firewall',
        ))->shouldBeCalled();

        $this->remove403Message();
    }
}
