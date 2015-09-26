<?php

namespace spec\HtaccessFirewall\Firewall;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use HtaccessFirewall\Filesystem\Filesystem;
use HtaccessFirewall\Firewall\Host;

class HtaccessFirewallSpec extends ObjectBehavior
{
    function let(Filesystem $fileSystem)
    {
        $fileSystem->read('path/to/.htaccess')
            ->willReturn([
                '# BEGIN Firewall',
                'order allow,deny',
                'deny from 123.0.0.1',
                'deny from 123.0.0.2',
                'allow from all',
                '# END Firewall'
            ]);

        $fileSystem->exists('path/to/.htaccess')->willReturn(true);
        $fileSystem->readable('path/to/.htaccess')->willReturn(true);
        $fileSystem->writable('path/to/.htaccess')->willReturn(true);

        $this->beConstructedWith('path/to/.htaccess', $fileSystem);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('HtaccessFirewall\Firewall\HtaccessFirewall');
        $this->shouldImplement('HtaccessFirewall\Firewall\Firewall');

        $this->shouldNotThrow('HtaccessFirewall\Firewall\Exception\FileNotFoundException')->duringInstantiation();
        $this->shouldNotThrow('HtaccessFirewall\Firewall\Exception\FileNotReadableException')->duringInstantiation();
        $this->shouldNotThrow('HtaccessFirewall\Firewall\Exception\FileNotWritableException')->duringInstantiation();
    }

    function it_blocks_a_host($fileSystem)
    {
        $fileSystem->write('path/to/.htaccess', [
            '# BEGIN Firewall',
            'order allow,deny',
            'deny from 123.0.0.1',
            'deny from 123.0.0.2',
            'deny from 123.0.0.3',
            'allow from all',
            '# END Firewall'
        ])->shouldBeCalled();

        $this->block(Host::fromString('123.0.0.3'));
    }

    function it_does_not_block_an_already_blocked_host($fileSystem)
    {
        $fileSystem->write('path/to/.htaccess', [
            '# BEGIN Firewall',
            'order allow,deny',
            'deny from 123.0.0.1',
            'deny from 123.0.0.2',
            'allow from all',
            '# END Firewall'
        ])->shouldBeCalled();

        $this->block(Host::fromString('123.0.0.1'));
    }

    function it_blocks_a_host_for_the_first_time($fileSystem)
    {
        $fileSystem->read('path/to/.htaccess')->willReturn([]);

        $fileSystem->write('path/to/.htaccess', [
            '# BEGIN Firewall',
            'order allow,deny',
            'deny from 123.0.0.1',
            'allow from all',
            '# END Firewall'
        ])->shouldBeCalled();

        $this->block(Host::fromString('123.0.0.1'));
    }

    function it_does_not_block_when_end_marker_is_not_found($fileSystem)
    {
        $fileSystem->read('path/to/.htaccess')
            ->willReturn([
                '# BEGIN Firewall',
                'order allow,deny',
                'deny from 123.0.0.1',
                'deny from 123.0.0.2',
                'allow from all',
                //'# END Firewall'
            ]);

        $this->shouldThrow('HtaccessFirewall\Firewall\Exception\FileException')
            ->during('block', [Host::fromString('123.0.0.1')]);
    }

    function it_unblocks_a_host($fileSystem)
    {
        $fileSystem->write('path/to/.htaccess', [
            '# BEGIN Firewall',
            'order allow,deny',
            'deny from 123.0.0.2',
            'allow from all',
            '# END Firewall'
        ])->shouldBeCalled();

        $this->unblock(Host::fromString('123.0.0.1'));
    }

    function it_gets_all_blocked_hosts()
    {
        $hosts = $this->getBlocks();

        $hosts->shouldBeArray();
        $hosts->shouldHaveCount(2);
        $hosts->shouldContainHost(Host::fromString('123.0.0.1'));
    }

    public function getMatchers()
    {
        return [
            'containHost' => function ($array, $host) {
                foreach ($array as $item) {
                    if ($item->equals($host)) {
                        return true;
                    }
                }
                return false;
            }
        ];
    }
}