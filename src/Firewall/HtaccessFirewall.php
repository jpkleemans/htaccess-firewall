<?php

namespace HtaccessFirewall\Firewall;

use HtaccessFirewall\Filesystem\Filesystem;
use HtaccessFirewall\Firewall\Exception\FileException;
use HtaccessFirewall\Firewall\Exception\FileNotFoundException;
use HtaccessFirewall\Firewall\Exception\FileNotReadableException;
use HtaccessFirewall\Firewall\Exception\FileNotWritableException;

/**
 * Firewall using Htaccess files.
 */
class HtaccessFirewall implements Firewall
{
    /**
     * @var string
     */
    public static $sectionLabel = 'Firewall';

    /**
     * @var string
     */
    private $path;

    /**
     * @var Filesystem
     */
    private $fileSystem;

    /**
     * Initialize HtaccessFirewall.
     *
     * @param $path
     * @param Filesystem $fileSystem
     *
     * @throws FileNotFoundException
     * @throws FileNotReadableException
     * @throws FileNotWritableException
     */
    public function __construct($path, Filesystem $fileSystem = null)
    {
        if (!$fileSystem->exists($path)) {
            throw new FileNotFoundException('Htaccess file not found.');
        }
        if (!$fileSystem->readable($path)) {
            throw new FileNotReadableException('Htaccess file not readable.');
        }
        if (!$fileSystem->writable($path)) {
            throw new FileNotWritableException('Htaccess file not writable.');
        }

        $this->path = $path;
        $this->fileSystem = $fileSystem;
    }

    /**
     * Block host.
     *
     * @param Host $host
     */
    public function block(Host $host)
    {
        $this->addLine('deny from ' . $host->toString());
    }

    /**
     * Unblock host.
     *
     * @param Host $host
     */
    public function unblock(Host $host)
    {
        $this->removeLine('deny from ' . $host->toString());
    }

    /**
     * Get all blocked hosts.
     *
     * @return array
     */
    public function getBlocks()
    {
        $lines = $this->readLinesWithPrefix('deny from ');

        foreach ($lines as $key => $line) {
            $host = substr($line, 10);
            $lines[$key] = Host::fromString($host);
        }

        return $lines;
    }

    /**
     * Add single line.
     *
     * @param string $line
     */
    private function addLine($line)
    {
        $insertion = array_merge(
            ['order allow,deny'],
            $this->readLinesWithPrefix('deny from '),
            [$line],
            ['allow from all']
        );

        $this->writeLines(array_unique($insertion));
    }

    /**
     * Remove single line.
     *
     * @param string $line
     */
    private function removeLine($line)
    {
        $insertion = $this->readLines();

        $lineToRemove = array_search($line, $insertion);
        if ($lineToRemove === false) {
            return;
        }

        unset($insertion[$lineToRemove]);

        $this->writeLines($insertion);
    }

    /**
     * Get array of prefixed lines in section.
     *
     * @param string|array $prefixes
     *
     * @return array
     */
    private function readLinesWithPrefix($prefixes)
    {
        if (!is_array($prefixes)) {
            $prefixes = [$prefixes];
        }

        $lines = $this->readLines();

        $prefixedLines = [];
        foreach ($lines as $line) {
            foreach ($prefixes as $prefix) {
                if (strpos($line, $prefix) === 0) {
                    $prefixedLines[] = $line;
                }
            }
        }

        return $prefixedLines;
    }

    /**
     * Get array of all lines in section.
     *
     * @return array
     */
    private function readLines()
    {
        $lines = $this->fileSystem->read($this->path);

        $linesInSection = [];
        $inSection = false;
        foreach ($lines as $line) {
            if ($this->isEndOfSection($line)) {
                break;
            }
            if ($inSection) {
                $linesInSection[] = $line;
            }
            if ($this->isBeginOfSection($line)) {
                $inSection = true;
            }
        }

        return $linesInSection;
    }

    /**
     * @param $lines
     *
     * @throws FileException
     */
    private function writeLines($lines)
    {
        $oldLines = $this->fileSystem->read($this->path);

        $newLines = [];
        $sectionExists = false;
        $inSection = false;
        foreach ($oldLines as $oldLine) {
            if ($this->isBeginOfSection($oldLine)) {
                $inSection = true;
            }
            if (!$inSection) {
                $newLines[] = $oldLine;
            }
            if ($this->isEndOfSection($oldLine)) {
                $newLines = array_merge(
                    $newLines,
                    ['# BEGIN ' . self::$sectionLabel],
                    $lines,
                    ['# END ' . self::$sectionLabel]
                );

                $sectionExists = true;
                $inSection = false;
            }
        }

        if ($inSection && !$sectionExists) {
            throw new FileException('Missing END marker in Htaccess file.');
        }

        if (!$sectionExists) {
            $newLines = array_merge(
                $oldLines,
                ['# BEGIN ' . self::$sectionLabel],
                $lines,
                ['# END ' . self::$sectionLabel]
            );
        }

        $this->fileSystem->write($this->path, $newLines);
    }

    /**
     * Check whether line is the begin of the section.
     *
     * @param $line
     *
     * @return bool
     */
    private function isBeginOfSection($line)
    {
        return strpos($line, '# BEGIN ' . self::$sectionLabel) !== false;
    }

    /**
     * Check whether line is the end of the section.
     *
     * @param $line
     *
     * @return bool
     */
    private function isEndOfSection($line)
    {
        return strpos($line, '# END ' . self::$sectionLabel) !== false;
    }
}
