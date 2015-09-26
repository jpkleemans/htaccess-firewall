<?php

namespace spec\HtaccessFirewall\Filesystem;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamWrapper;

class BuiltInFilesystemSpec extends ObjectBehavior
{
    function let()
    {
        vfsStream::setup('root', null, [
            'dummyfile.txt' => 'Lorem ipsum' . PHP_EOL . 'Dolor sit amet' . PHP_EOL . 'consectetur adipiscing elit'
        ]);

        // Disable write lock because LOCK_EX does not work with vfsStream
        $this->beConstructedWith(false);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('HtaccessFirewall\Filesystem\BuiltInFilesystem');
        $this->shouldImplement('HtaccessFirewall\Filesystem\Filesystem');
    }

    function it_reads_a_file_into_an_array()
    {
        $file = vfsStream::url('root') . DIRECTORY_SEPARATOR . 'dummyfile.txt';

        $result = $this->read($file);

        $result->shouldBeArray();
        $result->shouldHaveCount(3);
        $result->shouldContain('Dolor sit amet');
    }

    function it_writes_an_array_to_a_file()
    {
        $file = vfsStream::url('root') . DIRECTORY_SEPARATOR . 'dummyfile.txt';

        $this->write($file, [
            'Integer consequat',
            'accumsan orci'
        ]);

        $result = $this->read($file);

        $result->shouldBeArray();
        $result->shouldHaveCount(2);
        $result->shouldContain('Integer consequat');
        $result->shouldNotContain('Dolor sit amet');
    }

    function it_checks_whether_a_file_exists()
    {
        $rootPath = vfsStream::url('root') . DIRECTORY_SEPARATOR;

        $file1 = $rootPath . 'dummyfile.txt';
        $this->exists($file1)->shouldBe(true);

        $file2 = $rootPath . 'doesntexist.txt';
        $this->exists($file2)->shouldBe(false);
    }

    function it_checks_whether_a_file_is_readable()
    {
        $root = vfsStreamWrapper::getRoot();
        vfsStream::newFile('readable.txt', 0666)->at($root);
        vfsStream::newFile('not_readable.txt', 0111)->at($root);

        $rootPath = vfsStream::url('root') . DIRECTORY_SEPARATOR;

        $file1 = $rootPath . 'readable.txt';
        $this->readable($file1)->shouldBe(true);

        $file2 = $rootPath . 'not_readable.txt';
        $this->readable($file2)->shouldBe(false);
    }

    function it_checks_whether_a_file_is_writable()
    {
        $root = vfsStreamWrapper::getRoot();
        vfsStream::newFile('writable.txt', 0666)->at($root);
        vfsStream::newFile('not_writable.txt', 0444)->at($root);

        $rootPath = vfsStream::url('root') . DIRECTORY_SEPARATOR;

        $file1 = $rootPath . 'writable.txt';
        $this->writable($file1)->shouldBe(true);

        $file2 = $rootPath . 'not_writable.txt';
        $this->writable($file2)->shouldBe(false);
    }
}
