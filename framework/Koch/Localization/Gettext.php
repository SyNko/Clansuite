<?php

/**
 * Koch Framework
 * Jens-Andr� Koch � 2005 - onwards
 *
 * This file is part of "Koch Framework".
 *
 * License: GNU/GPL v2 or any later version, see LICENSE file.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace Koch\Localization\Gettext;

/**
 * Gettext
 *
 * 1. Gettext extraction is normally performed by the "xgettext" tool.
 *    http://www.gnu.org/software/hello/manual/gettext/xgettext-Invocation.html
 *
 * 2. PHP as a platform is still missing essential features of the gettext toolchain.
 *    You wont' find a PECL extension for extraction NOR native PO/MO writing.
 *    Basically everything is missing - except the reading of compiled gettext files (--with-gettext).
 *
 * 3. The missing parts are implemented in PHP:
 *    a) gettext extractor basedon preg_matching.
 *       The extractor matches certain translation functions, like translate('term') or t('term') or _('term')
 *       and their counterparts in templates, often {t('term')} or {_('term')}.
 *    b) POT/PO/MO File Handling = reading and writing.
 *
 * The Koch_Gettext is based on and inspired by
 *  - Karel Klima's "GettextExtractor v2" (new BSD)
 *  - Drupals "translation_extraction" (GPL)
 *  - Matthias Bauer's work on PO/MO Filehandling for Wordpress during GSoC 2007 (GPL)
 *  - Heiko Rabe's "Codestyling Localization" Plugin for Wordpress (GPL)
 *
 * @category    Koch
 * @package     Core
 * @subpackage  Gettext
 */
class Extractor extends Extractor_Tool
{
    /**
     * Setup mandatory extractors
     */
    public function __construct()
    {
        // clean up
        $this->removeAllExtractors();

        // set basic extractors for php and smarty template files
        $this->setExtractor('php', 'PHP')
             ->setExtractor('tpl', 'Template');

        // register the tags/functions to extract
        $this->getExtractor('PHP')->addTags(array('translate', 't', '_'));

        // register the tags/placeholders to extract
        $this->getExtractor('Template')->addTags(array('_', 't'));
    }

    /**
     * Scans given files or directories and extracts gettext keys from the content
     *
     * @param string|array $resource
     *
     * @return Koch_Gettext_Extractor
     */
    public function multiScan($resource)
    {
        $this->inputFiles = array();

        if (false === is_array($resource)) {
            $resource = array($resource);
        }

        foreach ($resource as $item) {
            $this->log('Scanning ' . $item);
            $this->scan($item);
        }

        $this->extract($this->inputFiles);

        return $this;
    }
}

namespace Koch\Localization\Gettext\Extractor;

/**
 * Gettext_Extractor_Tool
 *
 * Gettext extraction is normally performed by the "xgettext" tool.
 * http://www.gnu.org/software/hello/manual/gettext/xgettext-Invocation.html
 *
 * This is a php implementation of a gettext extractor on basis of preg_matching.
 * The extractor matches certain translation functions, like translate('term') or t('term') or _('term').
 * and their counterparts in templates, often {t('term')} or {_('term')}.
 *
 * @category    Koch
 * @package     Core
 * @subpackage  Gettext
 */
class Tool
{
    /**
     * @var resource
     */
    public $logHandler;

    /**
     * @var array
     */
    public $inputFiles = array();

    /**
     * @var array
     */
    public $extractors = array(
        'php' => array('PHP'),
        'tpl' => array('PHP', 'Template')
    );

    /**
     * @var array
     */
    public $data = array();

    /**
     *  @var array
     */
    protected $extractorStore = array();

    /**
     * Log setup
     * @param string|bool $logToFile Bool or path of custom log file
     */
    public function __construct($logToFile = false)
    {
        // default log file
        if (false === $logToFile) {
            $this->logHandler = fopen(ROOT_LOGS . 'gettext-extractor.log', 'w');
        } else { // custom log file
            $this->logHandler = fopen($logToFile, 'w');
        }
    }

    /**
     * Close the log handler if needed
     */
    public function __destruct()
    {
        if (is_resource($this->logHandler) === true) {
            fclose($this->logHandler);
        }
    }

    /**
     * Writes messages into log or dumps them on screen
     *
     * @param string $message
     *
     * @return string Html Log Message if logHandler resource is false.
     */
    public function log($message)
    {
        if (is_resource($this->logHandler) === true) {
            fwrite($this->logHandler, $message . "\n");
        } else {
            echo $message . "\n <br/>";
        }
    }

    /**
     * Exception factory
     *
     * @param string $message
     *
     * @throws Koch_Exception
     */
    protected function throwException($message)
    {
        if (empty($message) === true) {
            $message = 'Something unexpected occured. See Koch_Gettext_Extractor log for details.';
        }

        $this->log($message);

        throw new Koch_Exception($message);
    }

    /**
     * Scans given files or directories (recursively) for input files.
     *
     * @param string $resource File or directory
     */
    protected function scan($resource)
    {
        if (false === is_dir($resource) and false === is_file($resource)) {
            $this->throwException('Resource ' . $resource . ' is not a directory or file.');
        }

        if (true === is_file($resource)) {
            $this->inputFiles[] = realpath($resource);

            return;
        }

        // It's a directory
        $resource = realpath($resource);

        if (false === $resource) {
            return;
        }

        $iterator = dir($resource);

        if (false === $iterator) {
            return;
        }

        while (false !== ($entry = $iterator->read())) {
            if ($entry === '.' or $entry === '..' or  $entry === '.svn') {
                continue;
            }

            $path = $resource . DIRECTORY_SEPARATOR . $entry;

            if (false === is_readable($path)) {
                continue;
            }

            if (true === is_dir($path)) {
                $this->scan($path);
                continue;
            }

            if (true === is_file($path)) {
                $info = pathinfo($path);

                if (false === isset($this->extractors[$info['extension']])) {
                    continue;
                }

                $this->inputFiles[] = realpath($path);
            }
        }

        $iterator->close();
    }

    /**
     * Extracts gettext keys from multiple input files using multiple extraction adapters.
     *
     * @param array $inputFiles Array, defining a set of files.
     *
     * @return array All gettext keys of all input files.
     */
    protected function extract($inputFiles)
    {
        foreach ($inputFiles as $inputFile) {
            if (false === file_exists($inputFile)) {
                $this->throwException('Invalid input file specified: ' . $inputFile);
            }

            if (false === is_readable($inputFile)) {
                $this->throwException('Input file is not readable: ' . $inputFile);
            }

            $this->log('Extracting data from file ' . $inputFile);

            // Check file extension
            $fileExtension = pathinfo($inputFile, PATHINFO_EXTENSION);

            foreach ($this->extractors as $extension => $extractor) {
                // check, if the extractor handles a file extension like this
                if ($fileExtension !== $extension) {
                    continue;
                }

                $this->log('Processing file ' . $inputFile);

                foreach ($extractor as $extractorName) {
                    $extractor = $this->getExtractor($extractorName);
                    $extractorData = $extractor->extract($inputFile);

                    $this->log(' Extractor ' . $extractorName . ' applied.');

                    // do not merge if incomming array is empty
                    if (false === empty($extractorData)) {
                        $this->data = array_merge_recursive($this->data, $extractorData);
                    }
                }
            }
        }

        $this->log('Data exported successfully');

        return $this->data;
    }

    /**
     * Factory Method - Gets an instance of a Koch_Gettext_Extractor
     *
     * @param string $extractor
     *
     * @return object Extractor Object implementing Koch_Gettext_Extractor_Interface
     */
    public function getExtractor($extractor)
    {
        $extractor_classname = 'Koch_Gettext_Extractor_' . $extractor;

        if ($this->extractors[$extractor] !== null) {
            return $this->extractors[$extractor];
        }

        if (false === class_exists($extractor_classname, false)) {
            // /framework/Koch/Gettext/Extractors/*NAME*.gettext.php
            $extractor_file = ROOT_FRAMEWORK . 'Gettext/Extractors/' . $extractor . '.gettext.php';

            if (true === is_file($extractor_file)) {
                include_once $extractor_file;
            } else {
                $this->throwException('Extractor file ' . $extractor_file . ' not found.');
            }

            if (false === class_exists($extractor_classname)) {
                $this->throwException('File loaded, but Class ' . $extractor . ' not inside.');
            }
        }

        $this->extractors[$extractor] = new $extractor_classname;

        $this->log('Extractor ' . $extractor . ' loaded.');

        return $this->extractors[$extractor];
    }

    /**
     * Assigns an extractor to an extension
     *
     * @param string $extension
     * @param string $extractor
     *
     * @return Koch_Gettext_Extractor
     */
    public function setExtractor($extension, $extractor)
    {
        // not already set
        if (false === isset($this->extractor[$extension]) and false === in_array($extractor, $this->extractor[$extension])) {
            $this->extractor[$extension][] = $extractor;
        } else { // already set

            return $this;
        }
    }

    /**
     * Removes all extractor settings
     *
     * @return Koch_Gettext_Extractor
     */
    public function removeAllExtractors()
    {
        $this->extractor = array();

        return $this;
    }

    /**
     * Saves extracted data into gettext file
     *
     * @param string $file
     * @param array  $data
     *
     * @return Koch_Gettext_Extractor
     */
    public function save($file, $data = null)
    {
        if ($data === null) {
            $data = $this->data;
        }

        // get dirname and check if dirs exist, else create it
        $dir = dirname($file);
        if (false === is_dir($dir) and false === @mkdir($dir, 0777, true)) {
           $this->throwException('ERROR: make directory failed!');
        }

        // check file permissions on output file
        if (true === is_file($file) and false === is_writable($file)) {
            $this->throwException('ERROR: Output file is not writable!');
        }

        // write data formatted to file
        file_put_contents($file, $this->formatData($data));

        $this->log('Output file ' . $file . ' created.');

        return $this;
    }

    /**
     * Returns a fileheader for a gettext portable object file.
     *
     * @param boolean $return_as_string True, returns a string (default) and false returns an array.
     *
     * @return mixed Array or String. Returns string by default.
     */
    public static function getPOFileHeader($return_as_string = true)
    {
        $output = array();
        $output[] = '// Gettext Portable Object Translation File.';
        $output[] = '#';
        $output[] = '// Koch Framework';
        $output[] = '// Copyright � Jens-Andr� Koch 2005 - onwards.';
        $output[] = '// The file is distributed under the GNU/GPL v2 or any later version.';
        $output[] = '#';
        $output[] = 'msgid ""';
        $output[] = 'msgstr ""';
        $output[] = '"Project-Id-Version: Koch Framework ' . CLANSUITE_VERSION . '\n"';
        $output[] = '"POT-Creation-Date: ' . date('Y-m-d H:iO') . '\n"';
        $output[] = '"PO-Revision-Date: ' . date('Y-m-d H:iO') . '\n"';
        $output[] = '"Content-Type: text/plain; charset=UTF-8\n"';
        // @todo http://trac.clansuite.com/ticket/224 - fetch plural form from locale description array
        $output[] = '"Plural-Forms: nplurals=2; plural=(n != 1);\n"';
        $output[] = '';

        if ($return_as_string === true) {
            return implode("\n", $output);
        } else { // return array

            return $output;
        }
    }

    /**
     * Formats fetched data to gettext portable object syntax
     *
     * @param array $data
     *
     * @return string
     */
    protected function formatData($data)
    {
        $pluralMatchRegexp = '#\%([0-9]+\$)*d#';

        $output = array();
        $output = self::getPOFileHeader(false);

        ksort($data);

        foreach ($data as $key => $files) {
            ksort($files);

            $slashed_key = self::addSlashes($key);

            foreach ($files as $file) {
                $output[] = '#: ' . $file; // = reference
            }

            $output[] = 'msgid "' . $slashed_key . '"';

            // check for plural
            if (0 < preg_match($pluralMatchRegexp, $key)) {
                $output[] = 'msgid_plural "' . $slashed_key . '"';
                $output[] = 'msgstr[0] "' . $slashed_key . '"';
                $output[] = 'msgstr[1] "' . $slashed_key . '"';
            } else { // no plural
                $output[] = 'msgstr "' . $slashed_key . '"';
            }

            $output[] = '';
        }

        return join("\n", $output);
    }

    /**
     * Escapes a string without breaking gettext syntax.
     *
     * @param string $string
     *
     * @return string
     */
    public static function addSlashes($string)
    {
        return addcslashes($string, self::ESCAPE_CHARS);
    }
}

/**
 * Base Class of all Gettext Extractors
 */
class Extractor_Base
{
    /**
     * @var array Definition of all the tags to scan.
     */
    protected $tags_to_scan;

    /**
     * Add a tag (placeholder/function) to scan for
     *
     * @param mixed|array|string $tag String or Array of Tags.
     *
     * @return Object Koch_Gettext_Extractor
     */
    public function addTags($tags)
    {
        // multiple tags to add
        if (is_array($tags) === true) {
            foreach ($tags as $tag) {
                if (false === array_key_exists($tag, array_flip($this->tags_to_scan))) {
                    $this->tags_to_scan[] = $tag;
                }
            }
        } else { // just one element (string)
            $this->tags_to_scan[] = $tags;
        }

        return $this;
    }

    /**
     * Excludes a tag from scanning
     *
     * @param string $tag
     *
     * @return Object Koch_Gettext_Extractor
     */
    public function removeTag($tag)
    {
        unset($this->tags_to_scan[$tag]);

        return $this;
    }

    /**
     * Removes all tags
     *
     * @return object Koch_Gettext_Extractor
     */
    public function removeAllTags()
    {
        $this->tags_to_scan = array();

        return $this;
    }
}

/**
 * Gettext_Extractor_Interface
 */
interface Extractor
{
    public function extract($file);
}
