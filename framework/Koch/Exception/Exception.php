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

namespace Koch\Exception;

/**
 * Exception
 *
 * Sets up a custom Exceptionhandler.
 * @see Clansuite_CMS::initialize_Errorhandling()
 *
 * Developer Notice:
 * The "Fatal error: Exception thrown without a stack frame in Unknown on line 0"
 * is of PHP dying when an exception is thrown when running INSIDE an error or exception handler.
 * Avoid stacking Exceptions, e.g. try/catch Exception($e) and then throwing a Koch_Exception().
 *
 * @see http://php.net/manual/de/class.exception.php
 * @see http://php.net/manual/de/function.set-exception-handler.php
 *
 * @category    Koch
 * @package     Core
 * @subpackage  Exceptionhandler
 */
class Exception extends \Exception
{
    /**
     * Variables of a PHP Exception
     * They are used to store the content of incomming uncatched Exceptions.
     */

    /**
     * @var string exception message
     */
    protected $message = 'Unknown exception';

    /**
     * @var string debug backtrace string
     */
    private $string;

    /**
     * @var int user-defined exception code
     */
    protected $code = 0;

    /**
     * @var string source filename of exception
     */
    protected $file;

    /**
     * @var int source line of exception
     */
    protected $line;

    /**
     * @var string trace
     */
    private $trace;

    /**
     * Variables for the content of exception templates
     */

    /**
     * @var string HTML Representation of the Exception Template
     */
    private static $exception_template = '';

    /**
     * @var string HTML Representation of the Exception Development (RAD) Template
     */
    private static $exception_dev_template = '';

    /**
     * Exception Handler Callback
     * Rethrows uncatched Exceptions in our presentation style.
     *
     * @see http://php.net/manual/de/function.set-exception-handler.php
     * @param $exception PHP Exception Objects are valid (Type Hint).
     */
    public function exception_handler(\Exception $exception)
    {
        // re/assign variables from an uncatched exception to this exception object
        $this->message = $exception->getMessage();
        $this->string = $exception->getTraceAsString();
        $this->code = $exception->getCode();
        $this->file = $exception->getFile();
        $this->line = $exception->getLine();
        $this->trace = $exception->getTrace();

        // if no errorcode is set, say that it's an rethrow
        if ($this->code === '0') {
            $this->code = '0 (This exception is uncatched and rethrown.)';
        }

        // fetch exceptionTemplates, but not for $code = 0
        if ($this->code > 0) {
            self::fetchExceptionTemplates($this->code);
        }

        /**
         * @todo
         * 1. catch Smarty "Template Syntax" Errors
         * 2. provide link to templateeditor (file:line) to fix the error
         */
        #$smartyError = (false !== stristr($this->message, 'Syntax Error in template')) ? true : false;

        /**
         * @todo
         * 1. catch Smarty "Unable to load template file" Errors
         * 2. provide link to templatefilemanager (module:file)
         */

        echo $this->yellowScreenOfDeath();
    }

    /**
     * Fetches the normal and rapid development templates for exceptions and sets them to class.
     * Callable via self::getExceptionTemplate() and self::getExceptionDevelopmentTemplate($placeholders).
     *
     * @param int $code Exception Code
     */
    private static function fetchExceptionTemplates($code)
    {
        // normal exception template
        self::fetchExceptionTemplate($code);

        // development template
        if (defined('DEVELOPMENT') and DEVELOPMENT == 1) {
            self::fetchExceptionDevelopmentTemplate($code);
        }
    }

    /**
     * Fetches a Helper Template for this exception by it's exception code.
     *
     * You find the Exception Template in the folder: /themes/core/exceptions/
     * The filename has to be "exception-ID.html", where ID is the exception id.
     *
     * @example
     * <code>
     * throw new Koch_Exception('My Exception Message: ', 20);
     * </code>
     * The file "exception-20.html" will be retrieved.
     *
     * @param $code The exception code.
     */
    private static function fetchExceptionTemplate($code)
    {
        $file = ROOT . 'themes/core/exceptions/exception-' . $code . '.html';

        if (is_file($file) === true) {
            self::$exception_template = file_get_contents($file);
        }
    }

    /**
     * Fetches a Helper Template for this exception by it's exception code.
     * This is for rapid development purposes.
     *
     * You find the Exception Development Template in the folder: /themes/core/exceptions/
     * The filename has to be "exception-dev-ID.html", where ID is the exception id.
     *
     * @example
     * <code>
     * throw new Koch_Exception('My Exception Message: ', 20);
     * </code>
     * The file "exception-dev-20.html" will be retrieved.
     *
     * @param $code The exception code.
     */
    private static function fetchExceptionDevelopmentTemplate($code)
    {
        // construct filename with code
        $file = ROOT . 'themes/core/exceptions/exception-dev-' . $code . '.html';

        if (is_file($file) === true) {
            self::$exception_dev_template = file_get_contents($file);

            define('RAPIDDEVTPL', true);
        }
        /*
        else {
           // @todo propose to create a new rapid development template via tpleditor
        }
        */
    }

    /**
     * Getter Method for the exception_development_template_content
     *
     * @return HTML Representation of $exception_development_template_content
     */
    private static function getExceptionDevelopmentTemplate($placeholders)
    {
        $original_file_content = self::$exception_dev_template;
        $replaced_content = '';

        if ($placeholders['modulename'] !== null) {
            $replaced_content = str_replace('{$modulename}', $placeholders['modulename'], $original_file_content);
        }

        if ($placeholders['classname'] !== null) {
            $replaced_content = str_replace('{$classname}', $placeholders['classname'], $replaced_content);
        }

        if ($placeholders['actionname'] !== null) {
            $replaced_content = str_replace('{$actionname}', $placeholders['actionname'], $replaced_content);
        }

        return $replaced_content;
    }

    /**
     * Yellow Screen of Death (YSOD) is used to display a Koch Framework Exception
     */
    public function yellowScreenOfDeath()
    {
        ob_start();

        /**
         * @todo add backlink to the exception codes list
         */
        if ($this->code > 0) {
            $code = '(// ' . $this->code . ')';
        } else {
            $code = '';
        }

        // Header
        $html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
        $html .= '<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">';
        $html .= '<head>';
        $html .= '<title>Clansuite Exception ' . $code . ' - ' . $this->message . '</title>';
        $html .= '<link rel="stylesheet" href="' . WWW_ROOT_THEMES_CORE . 'css/error.css" type="text/css" />';
        $html .= '</head>';

        // Body
        $html .= '<body>';

        // Fieldset
        $html .= '<fieldset id="top" class="error_yellow">';

        // Errorlogo
        $html .= '<div style="float: left; margin: 5px; margin-right: 25px; padding: 20px;">';
        $html .= '<img src="' . WWW_ROOT_THEMES_CORE . 'images/Clansuite-Toolbar-Icon-64-exception.png" ';
        $html .= 'style="border: 2px groove #000000;" alt="Clansuite Exception Icon" /></div>';

        // Fieldset Legend
        $html .= '<legend>Clansuite Exception</legend>';

        // Exception Table
        $html .= '<table width="80%"><tr><td>';

        /**
         * Panel 1
         *
         * Exception Message and File
         */

        $html .= '<div id="panel1" class="panel">';
        $html .= '<h3>Exception '.$code.'</h3><h4>'.$this->message.'</h4>';
        $html .= '<strong>in file "' . $this->file . '"&nbsp;on line ' . $this->line.'.</strong>';
        $html .= '</div>';

        /**
         * Panel 2
         *
         * Debug Backtrace
         */
        if (defined('DEBUG') and DEBUG == 1) {
            // lets get the backtrace as html table
            $html .= Errorhandler::getDebugBacktrace($this->trace);
        }

        /**
         * Panel 3
         *
         * Server Environment Informations
         */
        if (defined('DEBUG') and DEBUG == 1) {
            $html .= '<div id="panel3" class="panel">';
            $html .= '<h3>Server Environment</h3>';
            $html .= '<table width="95%">';
            $html .= '<tr><td><strong>Date: </strong></td><td>' . date('r') . '</td></tr>';
            $html .= '<tr><td><strong>Remote: </strong></td><td>' . $_SERVER['REMOTE_ADDR'] . '</td></tr>';
            $html .= '<tr><td><strong>Request: </strong></td><td>index.php?' . $_SERVER['QUERY_STRING'] . '</td></tr>';
            $html .= '<tr><td><strong>PHP: </strong></td><td>' . PHP_VERSION . ' ' . PHP_EXTRA_VERSION . '</td></tr>';
            $html .= '<tr><td><strong>Server: </strong></td><td>' . $_SERVER['SERVER_SOFTWARE'] . '</td></tr>';
            $html .= '<tr><td><strong>Agent: </strong></td><td>' . $_SERVER['HTTP_USER_AGENT'] . '</td></tr>';
            $html .= '<tr><td><strong>Clansuite: </strong></td><td>' . CLANSUITE_VERSION . ' ' . CLANSUITE_VERSION_STATE;
            $html .= ' (' . CLANSUITE_VERSION_NAME . ')</td></tr>';
            $html .= '</table></div>';
        }

        /**
         * Panel 4
         *
         * Additional Information
         */
        if (empty(self::$exception_template) === false) {
            $html .= '<div id="panel4" class="panel">';
            $html .= '<h3>Additional Information & Solution Suggestion</h3>';
            $html .= self::$exception_template . '</div>';
        }

        /**
         * Panel 5
         *
         * Rapid Development
         */
        $placeholders = array();
        // assign placeholders for replacements in the html
        if (strpos($this->message, 'action_')) {
            $placeholders['actionname'] = substr($this->message, strpos($this->message, 'action_'));
        } elseif (strpos($this->message, 'module_')) {
            $placeholders['classname'] = substr($this->message, strpos($this->message, 'module_'));
        }

        if (empty($_GET['mod']) == false) {
            $placeholders['modulename'] = (string) stripslashes($_GET['mod']);
        } else {
            $placeholders['modulename'] = '';
        }

        // add development helper template to exceptions
        if (defined('DEVELOPMENT') and DEVELOPMENT == 1 and defined('RAPIDDEVTPL') and RAPIDDEVTPL == 1) {
            $html .= '<div id="panel5" class="panel">';
            $html .= '<h3>Rapid Application Development</h3>';
            $html .= self::getExceptionDevelopmentTemplate($placeholders).'</div>';
        }

        /**
         * Panel 6
         *
         * Backlink to Bugtracker with Exceptionmessage
         * @link http://trac.clansuite.com/newticket
         */
        $html .= Errorhandler::getBugtrackerBacklinks($this->message, $this->file, $this->line, $this->trace);

        // close all html element table
        $html   .= '</table>';

        /**
         * Panel 7
         *
         * Footer with Support-Backlinks
         */
        $html  .= Errorhandler::getSupportBacklinks($this);

        // close all html elements: fieldset, body+page
        $html   .= '</fieldset>';
        $html   .= '</body></html>';

        // save session before exit - but only if this is not a pdo exception
        // that would trigger a fatal error, when trying to write to the db during session save
        if ((bool) session_id() and false === strpos($this->message, 'SQLSTATE')) {
            session_write_close();
        }

        // clear all output buffers
        if ( ob_get_length() ) {
            ob_end_clean();
        }

        // Output the errormessage
        return $html;
    }

    /**
     * Formats the debugtrace ($this->string) by applying linebreaks.
     *
     * @param $string The debug-trace string to format.
     * @return HTML with Debugtrace String
     */
    public static function formatGetTraceString($string)
    {
        $search  = array('#', '):');
        $replace = array('<br/><br/>Call #',')<br/>');
        $string  = str_replace($search, $replace, $string);
        $string  = ltrim($string, '<br/>');
        unset($search, $replace);

        return $string;
    }

    /**
     * Overwriteable Method of Class Exception
     * This is the String representation of the exception.
     * It is a pass-through to our presentation format (ysod);
     *
     * @see yellowScreenOfDeath()
     */
    public function __toString()
    {
        return $this->yellowScreenOfDeath();
    }
}
