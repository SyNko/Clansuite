<?php
   /**
    * Clansuite - just an eSports CMS
    * Jens-Andr� Koch � 2005 - onwards
    * http://www.clansuite.com/
    *
    * This file is part of "Clansuite - just an eSports CMS".
    *
    * LICENSE:
    *
    *    This program is free software; you can redistribute it and/or modify
    *    it under the terms of the GNU General Public License as published by
    *    the Free Software Foundation; either version 2 of the License, or
    *    (at your option) any later version.
    *
    *    This program is distributed in the hope that it will be useful,
    *    but WITHOUT ANY WARRANTY; without even the implied warranty of
    *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    *    GNU General Public License for more details.
    *
    *    You should have received a copy of the GNU General Public License
    *    along with this program; if not, write to the Free Software
    *    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
    *
    * @license    GNU/GPL v2 or (at your option) any later version, see "/doc/LICENSE".
    * @author     Jens-Andr� Koch <vain@clansuite.com>
    * @copyright  Jens-Andr� Koch (2005 - onwards)
    * @link       http://www.clansuite.com
    *
    * @version    SVN: $Id$
    */

//Security Handler
if (defined('IN_CS') === false)
{
    die('Clansuite not loaded. Direct Access forbidden.');
}

/**
 * Clansuite Core Class for Errorhandling
 *
 * Sets up a custom Errorhandler.
 * @see Clansuite_CMS::initialize_Errorhandling()
 *
 * @example
 * <code>
 * 1) trigger_error('Errormessage', E_ERROR_TYPE);
 *    E_ERROR_TYPE as string or int
 * 2) trigger_error('Errorhandler Test - This should trigger a E_USER_NOTICE!', E_USER_NOTICE);
 * </code>
 *
 * @category    Clansuite
 * @package     Core
 * @subpackage  Errorhandler
 */
class Clansuite_Errorhandler
{
    /**
     * Clansuite Error callback.
     *
     * This is basically a switch defining the actions taken,
     * in case of serveral PHP error states
     *
     * @link http://www.usegroup.de/software/phptutorial/debugging.html
     * @link http://www.php.net/manual/de/function.set-error-handler.php
     * @link http://www.php.net/manual/de/errorfunc.constants.php
     *
     * @param integer $errornumber contains the error as integer
     * @param string $errorstring contains error string info
     * @param string $errorfile contains the filename with occuring error
     * @param string $errorline contains the line of error
     * @param string $errorcontext
     */
    public static function errorhandler( $errornumber, $errorstring, $errorfile, $errorline, $errorcontext )
    {
        #  do just return, if the error is suppressed - cases: (silenced with @ operator or DEBUG mode active)
        if(error_reporting() === 0)
        {
            return;
        }

        /**
         * Assemble the error informations
         */

        # set the error time
        $errortime = date(DATE_FORMAT);

        /**
         * Definition of PHP Errortypes Array - with names for all the php error codes
         * @link http://php.net/manual/de/errorfunc.constants.php
         */
        $errorTypes = array (    1      => 'E_ERROR',               # fatal run-time errors, like php is failing memory allocation
                                 2      => 'E_WARNING',             # Run-time warnings (non-fatal errors)
                                 4      => 'E_PARSE',               # compile-time parse errors - generated by the parser
                                 8      => 'E_NOTICE',              # Run-time notices (could be an indicator for an error)
                                 16     => 'E_CORE_ERROR',          # PHP Core reports errors in PHP's initial startup
                                 32     => 'E_CORE_WARNING',        # PHP Core reports warning (non-fatal errors)
                                 64     => 'E_COMPILE_ERROR',       # Zend Script Engine reports fatal compile-time errors
                                 128    => 'E_COMPILE_WARNING',     # Zend Script Engine reports compile-time warnings (non-fatal errors)
                                 256    => 'E_USER_ERROR',          # trigger_error() / user_error() reports user-defined error
                                 512    => 'E_USER_WARNING',        # trigger_error() / user_error() reports user-defined warning
                                 1024   => 'E_USER_NOTICE',         # trigger_error() / user_error() reports user-defined notice
                                #2047   => 'E_ALL 2047 PHP <5.2.x', # all errors and warnings + old value of E_ALL of PHP Version below 5.2.x
                                 2048   => 'E_STRICT',              # PHP suggests codechanges to ensure interoperability / forwad compat
                                 4096   => 'E_RECOVERABLE_ERROR',   # catchable fatal error, if not catched it's an e_error (since PHP 5.2.0)
                                #6143   => 'E_ALL 6143 PHP5.2.x',   # all errors and warnings + old value of E_ALL of PHP Version 5.2.x
                                 8191   => 'E_ALL 8191',            # PHP 6 -> 8191
                                 8192   => 'E_DEPRECATED',          # notice marker for 'in future' deprecated php-functions (since PHP 5.3.0)
                                 16384  => 'E_USER_DEPRECATED',     # trigger_error() / user_error() reports user-defined deprecated functions
                                 30719  => 'E_ALL 30719 PHP5.3.x',  # all errors and warnings - E_ALL of PHP Version 5.3.x
                                 32767  => 'E_ALL 32767 PHP6'       # all errors and warnings - E_ALL of PHP Version 6
                                 );

        # check if the error number exists in the errortypes array
        if(true === isset($errorTypes[$errornumber]))
        {
            # get the errorname from the array via $errornumber
            $errorname = $errorTypes[$errornumber];
        }

        # Handling the ErrorType via Switch
        switch ($errorname)
        {
            # What are the errortypes that can be handled by a user-defined errorhandler?
            case 'E_WARNING':
                $errorname .= ' [php warning]';
                break;
            case 'E_NOTICE':
                $errorname .= ' [php notice]';
                break;
            case 'E_USER_ERROR':
                $errorname .= ' [Clansuite Internal Error]';
                break;
            case 'E_USER_WARNING':
                $errorname .= ' [Clansuite Internal Error]';
                break;
            case 'E_USER_NOTICE':
                $errorname .= ' [Clansuite Internal Error]';
                break;
            #case 'E_ALL':
            case 'E_STRICT':
                $errorname .= ' [php strict]';
                break;
            case 'E_RECOVERABLE_ERROR':
                $errorname .= ' [php not-unstable]';
                break;
            # when it's not in there, its an unknown errorcode
            default:
                $errorname .= ' Unknown Errorcode ['. $errornumber .']: ';
        }

        # make the errorstring more useful by linking it to the php manual
        $errorstring = preg_replace("/<a href='(.*)'>(.*)<\/a>/", '<a href="http://php.net/$1" target="_blank">?</a>', $errorstring);

        # shorten errorfile string by removing the root path
        $errorfile = str_replace(ROOT, '', $errorfile);

        # if DEBUG is set, display the error
        if ( defined('DEBUG') and DEBUG == 1 )
        {
            # SMARTY ERRORS are thrown by trigger_error() - so they bubble up as E_USER_ERROR
            # and in order to handle smarty errors with a seperated error display
            # we need to detect, if an E_USER_ERROR is incoming from SMARTY or from a template_c file (extension tpl.php)
            if( (true === (bool) mb_strpos(mb_strtolower($errorfile),'smarty')) or
                (true === (bool) mb_strpos(mb_strtolower($errorfile),'tpl.php')) )
            {
                # ok it's an Smarty Template Error - show the error via smarty_error_display inside the template
                echo self::smarty_error_display( $errornumber, $errorname, $errorstring, $errorfile, $errorline, $errorcontext );
            }
            else # give normal Error Display
            {
                # All Error Informations (except backtraces)
                echo self::yellowScreenOfDeath($errornumber, $errorname, $errorstring, $errorfile, $errorline, $errorcontext );
            }
        }

        # Skip PHP internal error handler
        return true;
    }

    /**
     * Smarty Error Display
     *
     * This method defines the html-output when an Smarty Template Error occurs.
     * It's output is a shortened version of the normal error report, presenting
     * only errorname, filename and the line of the error.
     * The parameters used for the small report are $errorname, $errorfile, $errorline.
     * If you need a full errorreport, you can add more parameters from the methodsignature
     * to the $errormessage output.
     *
     * A Smarty Template Error is only displayed, when Clansuite is in DEBUG Mode.
     * @see clansuite_error_handler()
     *
     * The directlink to the templateeditor to edit the template file with the error is only available,
     * when Clansuite runs in DEVELOPMENT Mode.
     * @see addTemplateEditorLink()
     *
     * @param integer $errornumber contains the error as integer
     * @param string $errorstring contains error string info
     * @param string $errorfile contains the filename with occuring error
     * @param string $errorline contains the line of error
     * @param $errorcontext $errorline contains context
     */
    private static function smarty_error_display( $errornumber, $errorname, $errorstring, $errorfile, $errorline, $errorcontext )
    {
        # small errorreport
        $errormessage = '';
        $errormessage .= '<span>';
        $errormessage .=  '<h3><font color="#ff0000">&raquo; Smarty Template Error &laquo;</font></h3>';
        $errormessage .=  '<u>'. $errorname . ' (' . $errornumber .'): </u><br/>';
        $errormessage .=  '<b>'. wordwrap($errorstring,50,"\n") .'</b><br/>';
        $errormessage .=  'File: '. $errorfile. '<br/>Line: ' .$errorline;
        $errormessage .= self::getTemplateEditorLink($errorfile, $errorline, $errorcontext);
        $errormessage .=  '<br/></span>';

        return $errormessage;
    }

    /**
     * getTemplateEditorLink
     *
     * a) determines the path to the invalid template file
     * b) provides the html-link to the templateeditor for this file
     *
     * @param $errorfile Template File with the Error.
     * @param $errorline Line Number of the Error.
     * @todo correct link to the templateeditor
     */
    private static function getTemplateEditorLink($errorfile, $errorline, $errorcontext)
    {
        # display the link to the templateeditor, if we are in DEVELOPMENT MODE
        # and more essential if the error relates to a template file
        if(defined('DEVELOPMENT') and DEVELOPMENT === 1 and (mb_strpos(mb_strtolower($errorfile),'.tpl') === true))
        {
            # ok, it's a template, so we have a template context to determine the templatename
            $tpl_vars = $errorcontext['this']->getTemplateVars();

            # maybe the templatename is defined in tpl_vars
            if(true === isset($tpl_vars['templatename']))
            {
                $errorfile = $tpl_vars['templatename'];
            }
            else # else use resource_name from the errorcontext
            {
                $errorfile = $errorcontext['resource_name'];
            }

            # construct the link to the tpl-editor
            $html  = '<br/><a href="index.php?mod=templatemanager&amp;sub=admin&amp;action=editor';
            $html .= '&amp;file='.$errorfile.'&amp;line='.$errorline;
            $html .= '">Edit the Template</a>';

            # return the link
            return $html;
        }
    }

    /**
     * Returns colorname for errornumber
     *
     * @param int $errornumber the errornumber to get the colorname for
     * @return string
     */
    private static function getColornameForErrornumber($errornumber)
    {
        $color = 'beige';
        $colors = array( 256  => 'red', 512  => 'orange', 1024 => 'beige');
        $color = isset($colors[$errornumber]) ? $colors[$errornumber] : $color;
        return $color;
    }

    /**
     * Yellow Screen of Death (YSOD) is used to display a Clansuite Error
     *
     * @param int $errornumber
     * @param string $errorname
     * @param string $errorstring
     * @param string $errorfile
     * @param int $errorline
     * @param string $errorcontext
     */
    private static function yellowScreenOfDeath($errornumber, $errorname, $errorstring, $errorfile, $errorline, $errorcontext )
    {
        if(mb_strlen($errorstring) > 70)
        {
            $trimed_errorstring = mb_substr($errorstring, 0, mb_strpos($errorstring, ' ', 70)) . ' ...';
        }
        else
        {
            $trimed_errorstring = $errorstring;
        }

        # Header
        $errormessage = '<html><head>';
        $errormessage .= '<title>Clansuite Error | ' . $trimed_errorstring . ' | Code: ' . $errornumber . '</title>';
        $errormessage .= '<link rel="stylesheet" href="' . WWW_ROOT_THEMES_CORE . 'css/error.css" type="text/css" />';
        $errormessage .= '</head>';

        # Body
        $errormessage .= '<body>';

        # Fieldset colored (error_red, error_orange, error_beige)
        $errormessage .= '<fieldset class="error_' . self::getColornameForErrornumber($errornumber) . '">';

        # Errorlogo
        $errormessage .= '<div style="float: left; margin: 5px; margin-right: 25px; border:1px inset #bf0000; padding: 20px;">';
        $errormessage .= '<img src="' . WWW_ROOT_THEMES_CORE . 'images/Clansuite-Toolbar-Icon-64-error.png" style="border: 2px groove #000000;"/></div>';

        # Fieldset Legend
        $errormessage .= '<legend>Clansuite Error : [ ' . $trimed_errorstring . ' ] </legend>';

        # Error Messages
        $errormessage .= '<table>';
        $errormessage .= '<tr><td>';

        # The inner Error Table
        $errormessage .= '<table>';
        $errormessage .= '<tr><td colspan="2"><h3>Error</td></tr>';
        $errormessage .= '<tr><td colspan="2"><h4>' . $errorstring . '</h4></td></tr>';
        $errormessage .= '<tr><td width=15%><strong>Type: </strong></td><td>' . $errorname . ' '. $errornumber . '</td></tr>';
        $errormessage .= '<tr><td><strong>Path: </strong></td><td>' . dirname($errorfile) . '</td></tr>';
        $errormessage .= '<tr><td><strong>File: </strong></td><td>' . basename($errorfile) . '</td></tr>';
        $errormessage .= '<tr><td><strong>Line: </strong></td><td>' . $errorline . '</td></tr>';
        $errormessage .= '</table>';

        # HR Split
        $errormessage .= '<tr><td colspan="2">&nbsp;</td></tr>';

        # Error Context
        $errormessage .= '<tr><td colspan="2"><h3>Context</h3></td></tr>';
        $errormessage .= '<tr><td colspan="2">' . self::getErrorContext($errorfile, $errorline, 8) . '</td></tr>';

        # HR Split
        $errormessage .= '<tr><td colspan="2">&nbsp;</td></tr>';

        # Add Debug Backtracing
        $errormessage .= '<tr><td>' . self::getDebugBacktrace($trimed_errorstring) . '</td></tr>';

        # HR Split
        # $errormessage .= '<tr><td colspan="2">&nbsp;</td></tr>';

        #
        # $errormessage .= '<tr><td>' . self::getBugtrackerSearch() . '</td></tr>';

        # HR Split
        $errormessage .= '<tr><td colspan="2">&nbsp;</td></tr>';

        # Environmental Informations at Errortime ( $errorcontext is not displayed )
        $errormessage .= '<tr><td><table width="95%">';
        $errormessage .= '<tr><td colspan="2"><h3>Server Environment</h3></td></tr>';
        $errormessage .= '<tr><td><strong>Date: </strong></td><td>' . date('r') . '</td></tr>';
        $errormessage .= '<tr><td><strong>Remote: </strong></td><td>' . $_SERVER['REMOTE_ADDR'] . '</td></tr>';
        $errormessage .= '<tr><td><strong>Request: </strong></td><td>' . htmlentities($_SERVER['QUERY_STRING'], ENT_QUOTES) . '</td></tr>';
        $errormessage .= '<tr><td><strong>PHP: </strong></td><td>' . PHP_VERSION .' '. PHP_EXTRA_VERSION . '</td></tr>';
        $errormessage .= '<tr><td><strong>Server: </strong></td><td>' . $_SERVER['SERVER_SOFTWARE'] . '</td></tr>';
        $errormessage .= '<tr><td><strong>Agent: </strong></td><td>' . $_SERVER['HTTP_USER_AGENT'] . '</td></tr>';
        $errormessage .= '<tr><td><strong>Clansuite: </strong></td><td>' . CLANSUITE_VERSION . ' ' . CLANSUITE_VERSION_STATE;
        $errormessage .= ' (' . CLANSUITE_VERSION_NAME . ') [Revision #' . CLANSUITE_REVISION . ']</td></tr>';
        $errormessage .= '</table>';

        # HR Split
        $errormessage .= '<tr><td colspan="2">&nbsp;</td></tr>';

        # Backlink to Bugtracker with Errormessage -> http://trac.clansuite.com/newticket
        $errormessage .= self::getBugtrackerMessage($errorstring, $errorfile, $errorline, $errorcontext);

        # close html elements: table
        $errormessage .= '</table>';

        # Footer with Support-Backlinks
        $errormessage .= Clansuite_Errorhandler::getSupportBacklinks();

        # close all html elements: fieldset, body+page
        $errormessage .= '</fieldset><br /><br />';
        $errormessage .= '</body></html>';

        # Output the errormessage
        return $errormessage;
    }

    /**
     * getDebugBacktrace
     *
     * Transforms the output of php's debug_backtrace() to a more readable html format.
     *
     * @return string $backtrace_string contains the backtrace
     */
    public static function getDebugBacktrace($backtrace = null)
    {
        # provide backtrace only when we are in Clansuite DEBUG Mode, otherwise just return
        if ( defined('DEBUG') == false xor DEBUG == 0 )
        {
            return;
        }

        # if a trace is incoming, then this trace comes from an exception
        if(isset($backtrace) === false)
        {
            # else (normally) the errorhandler has to fetch the backtrace
            $backtrace = debug_backtrace();

            /**
             * Now we get rid of several last calls in the backtrace stack
             * to get nearer to the relevant position for the error in the stack.
             *
             * What exactly happens is: we shift-off the calls to
             * 1) getDebugBacktrace()   [this method]
             * 2) yellowScreenOfDeath() [our exception and error display method]
             * 3) trigger_error()       [php core function call]
             */
            $backtrace = array_slice($backtrace, 3);
        }

        # prepare a new backtrace_string
        $backtrace_string = '';
        $backtrace_string .= '<tr><td><h3>Backtrace</h3></td></tr>';
        $backtrace_string .= '<tr><td width="95%">';
        $backtrace_string .= '<table class="cs-backtrace-table" width="95%">';
        $backtrace_string .= '<tr><th><strong>Callstack</strong></td><th colspan="2">(Recent function calls last)</td></tr>';

        $backtrace_string .= '<tr><th width="2%">#</th><th>Function</th><th width="40%">Location</th></tr>';

        $backtraces_count = count($backtrace)-1;
        for($i = 0; $i <= $backtraces_count; $i++)
        {
            $backtrace_string .= '<tr>';

            # Call #
            $backtrace_string .= '<td align="center">'.(($backtraces_count-$i)+1).'</td>';

            if(isset($backtrace[$i]['class']) === false)
            {
                $backtrace_string .= '<td>[PHP Core Function called]</td>';
            }
            else
            {
                $backtrace_string .= '<td>' . $backtrace[$i]['class'] . '::' . $backtrace[$i]['function'] . '(';

                if(true === isset($backtrace[$i]['args']) and empty($backtrace[$i]['args']) === false)
                {
                    $backtrace_counter_j = count($backtrace[$i]['args']) - 1;
                    for($j = 0; $j <= $backtrace_counter_j; $j++)
                    {
                        $backtrace_string .= self::formatBacktraceArgument($backtrace[$i]['args'][$j]);

                        # if we have several arguments to loop over
                        if($j !== $backtrace_counter_j)
                        {
                            # we split them by comma
                            $backtrace_string .= ', ';
                        }
                    }
                }

                $backtrace_string .= ')</td>';
            }

            if(true === isset($backtrace[$i]['file']))
            {
                $backtrace[$i]['file'] = str_replace(ROOT, '..'.DS, $backtrace[$i]['file']);
                $backtrace_string .= '<td>' . $backtrace[$i]['file'] . ':' . $backtrace[$i]['line'] . '</td>';
            }

            # spacer
            $backtrace_string .= '</tr>';
        }

        # spacer
        $backtrace_string .= '</table></td></tr>';

        # returns the Backtrace String
        return $backtrace_string;
    }

    /**
     * formatBacktraceArgument
     *
     * Performs a type check on the backtrace argument and beautifies it.
     *
     * This formater is based on comments for debug-backtrace in the php manual
     * @link http://de2.php.net/manual/en/function.debug-backtrace.php#30296
     * @link http://de2.php.net/manual/en/function.debug-backtrace.php#47644
     *
     * @param backtraceArgument mixed The argument to identify the type upon and perform a string formatting on.
     *
     * @return string
     */
    public static function formatBacktraceArgument($backtraceArgument)
    {
        $args = '';

        switch (gettype($backtraceArgument))
        {
            case 'boolean':
                $args .= '<span>bool</span> ';
                $args .= $backtraceArgument ? 'TRUE' : 'FALSE';
                break;
            case 'integer':
                $args .= '<span>int</span> ';
                $args .= $backtraceArgument;
                break;
            case 'float':
                $args .= '<span>float</span> ';
                $args .= $backtraceArgument;
                break;
            case 'double':
                $args .= '<span>double</span> ';
                $args .= $backtraceArgument;
                break;
            case 'string':
                $args .= '<span>string</span> ';
                if((mb_strlen($backtraceArgument) > 64))
                {
                    $backtraceArgument = htmlspecialchars(mb_substr($backtraceArgument, 0, 64));
                    $backtraceArgument . '...';
                }
                $args .= '"'. $backtraceArgument .'"';
                break;
            case 'array':
                $args .= '<span>array</span> ('.count($backtraceArgument).')';
                break;
            case 'object':
                $args .= '<span>object</span> ('.get_class($backtraceArgument).')';
                break;
            case 'resource':
                $args .= '<span>resource</span> ('.mb_strstr($backtraceArgument, '#').' - '. get_resource_type($backtraceArgument) .')';
                break;
            case 'NULL':
                $args .= '<span>null</span> ';
                break;
            default:
                $args .= 'Unknown';
        }
        return $args;
    }

    /**
     * getErrorContext displayes some additional lines of sourcecode around the line with error.
     *
     * This is based on a code-snippet posted on the php manual website by
     * @author dynamicflurry [at] gmail dot com
     * @link http://us3.php.net/manual/en/function.highlight-file.php#92697
     *
     * @param string $file file with the error in it
     * @param int $scope the context scope (defining how many lines surrounding the error are displayed)
     * @param int $line the line with the error in it
     *
     * @return string sourcecode of file
     */
    public static function getErrorContext($file, $line, $scope)
    {
        # ensure error context is only shown, when in debug mode
        if(defined('DEVELOPMENT') and DEVELOPMENT == 1  and defined('DEBUG') and DEBUG == 1)
        {
            # ensure that sourcefile is readable
            if (true === is_readable($file))
            {
                # Scope Calculations
                $surrounding_lines          = round($scope/2);
                $errorcontext_starting_line = $line - $surrounding_lines;
                $errorcontext_ending_line   = $line + $surrounding_lines;

                # create linenumbers array
                $lines_array = range($errorcontext_starting_line, $errorcontext_ending_line);

                # colourize the errorous linenumber red
                $lines_array[$surrounding_lines] = '<span style="color: white; background-color:#BF0000;">'.$lines_array[$surrounding_lines].'</span>';

                # transform linenumbers array to string for later display
                $lines_html = implode($lines_array, '<br />');

                # get ALL LINES syntax highlighted source-code of the file and explode it into an array
                $array_content = explode('<br />', highlight_file($file, true));

                # get the ERROR SURROUNDING LINES from ALL LINES
                $array_content_sliced = array_slice($array_content, $errorcontext_starting_line-1, $scope, true);

                $result = array_values($array_content_sliced);

                # now colourize the background of the errorous line RED
                #$result[$surrounding_lines] = '<span style="background-color:#BF0000;">'. $result[$surrounding_lines] .'</span>';

                /**
                 * transform the array into html string
                 * enhance readablility by imploding the array with linebreaks (CR)
                 */
                $errorcontext_lines  = implode($result, '<br />');

                $sprintf_html = '<table>
                                    <tr>
                                        <td class="num">'.CR.'%s'.CR.'</td>
                                        <td><code>'.CR.'%s'.CR.'</code></td>
                                    </tr>
                                </table>';

                return sprintf($sprintf_html, $lines_html, $errorcontext_lines);
            }
        }
    }

    public static function getSupportBacklinks()
    {
        $html  = '<div style="padding-top: 45px; float:right;">';
        $html  .= '<strong><!-- Live Support JavaScript -->
                           <a href="http://support.clansuite.com/chat.php" target="_blank">Contact Support (Start Chat)</a>
                           <!-- Live Support JavaScript --></strong> | ';
        $html  .= '<strong><a href="http://trac.clansuite.com/newticket/">Bug-Report</a></strong> |
                           <strong><a href="http://forum.clansuite.com/">Support-Forum</a></strong> |
                           <strong><a href="http://docs.clansuite.com/">Manuals</a></strong> |
                           <strong><a href="http://www.clansuite.com/">visit clansuite.com</a></strong>
                           </div>';
        return $html;
    }

    /**
     * Adds a link to our bugtracker, for creating a new ticket with the errormessage
     *
     * @param string $errorstring the errormessage
     * @return string html-representation of the bugtracker links
     */
    public static function getBugtrackerMessage($errorstring, $errorfile, $errorline, $errorcontext)
    {
        $message1 = '<tr><td colspan="2"><h3>' . _('Found a bug in Clansuite?') . '</h3>';
        $message2 = _('If you think this should work and you can reproduce the problem, please consider creating a bug report.');
        $message3 = _('Before creating a new bug report, please first try searching for similar issues, as it is quite likely that this problem has been reported before.');
        $message4 = _('Otherwise, please create a new bug report describing the problem and explain how to reproduce it.');

        $search_link = NL . NL . '&#9658; <a target="_blank" href="http://trac.clansuite.com/search?q=' . htmlentities($errorstring, ENT_QUOTES) . '&noquickjump=1&ticket=on">';
        $search_link .= _('Search for similar issue');
        $search_link .= '</a>';

        $newticket_link = '&nbsp; &#9658; <a target="_blank" href="'.self::getTracNewTicketURL($errorstring, $errorfile, $errorline, $errorcontext).'">';
        $newticket_link .= _('Create new ticket');
        $newticket_link .= '</a>';

        $close_table = '</td></tr>' . NL;

        return $message1 . $message2 . NL . $message3 . NL . $message4 . $search_link . $newticket_link . $close_table;
    }

    /**
     * Returns a New Ticket URL for a GET Request
     *
     * urlencode($errorstring);
     * urlencode($this->excetpion->getMessage(). chr(10));
     */
    public static function getTracNewTicketURL($summary, $errorfile, $errorline, $context)
    {
        /**
         * error description written in trac wiki formating style
         * @link http://trac.clansuite.com/wiki/WikiFormatting
         */
        $description = '[Error] ' . $summary . ' [[BR]] [File] ' . $errorfile . ' [[BR]] [Line] ' . $errorline;

        # options array for http_build_query
        $array = array(
            'summary'     => $summary,
            'description' => $description,
            'type'        => 'defect-bug',
            'milestone'   => 'Triage-Neuzuteilung',
            'version'     => 'Clansuite v' . CLANSUITE_VERSION,
            #'component'   => '',
            'author'      => isset($_SESSION['user']['email']) ? $_SESSION['user']['email'] : '',
        );

        return 'http://trac.clansuite.com/newticket/?' . http_build_query($array);
    }
}

?>