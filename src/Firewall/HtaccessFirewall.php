<?php

namespace HtaccessFirewall\Firewall;

use HtaccessFirewall\Filesystem\Filesystem;
use HtaccessFirewall\Filesystem\BuiltInFilesystem;
use HtaccessFirewall\Filesystem\Exception\FileException;
use HtaccessFirewall\Host\Host;

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
     */
    public function __construct($path, Filesystem $fileSystem = null)
    {
        $this->fileSystem = $fileSystem ?: new BuiltInFilesystem();

        $this->path = $path;
    }

    /**
     * Deny host.
     *
     * @param Host $host
     */
    public function deny(Host $host)
    {
        $this->addLine('deny from ' . $host->toString());
    }

    /**
     * Undeny host.
     *
     * @param Host $host
     */
    public function undeny(Host $host)
    {
        $this->removeLine('deny from ' . $host->toString());
    }

    /**
     * Get all denied hosts.
     *
     * @return string[]
     */
    public function getDenied()
    {
        $lines = $this->readLinesWithPrefix('deny from ');

        foreach ($lines as $key => $line) {
            $host = substr($line, 10);
            $lines[$key] = $host;
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
            array('order allow,deny'),
            $this->readLinesWithPrefix('deny from '),
            array($line),
            array('allow from all')
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
     * @param string|string[] $prefixes
     *
     * @return string[]
     */
    private function readLinesWithPrefix($prefixes)
    {
        if (!is_array($prefixes)) {
            $prefixes = array($prefixes);
        }

        $lines = $this->readLines();

        $prefixedLines = array();
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
     * @return string[]
     */
    private function readLines()
    {
        $lines = $this->fileSystem->read($this->path);

        $linesInSection = array();
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

        $newLines = array();
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
                    array('# BEGIN ' . self::$sectionLabel),
                    $lines,
                    array('# END ' . self::$sectionLabel)
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
                array('# BEGIN ' . self::$sectionLabel),
                $lines,
                array('# END ' . self::$sectionLabel)
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
        return strpos($line, '# BEGIN ' . self::$sectionLabel) === 0;
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
        return strpos($line, '# END ' . self::$sectionLabel) === 0;
    }
}
