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

namespace Koch\View\Helper;

/**
 * Koch Framework - Class for BBCode Handling (Wrapper) and Syntax Highlighting
 *
 * It's a wrapper class for
 * a) GeShi Code/Syntax Highligther
 *    This is used with the code tags, like [code]<?php ... ?>[/code]
 * b) bbcode_stringparser.
 *
 * @category    Koch
 * @package     Core
 * @subpackage  BBCode
 */
class Bbcode
{
    /**
     * @var object instance of StringParser_BBCode
     */
    public $bbcode;

    public function __construct()
    {
        // Include Stringpaser_bbcode Class
        if (false === class_exists('StringParser_BBCode', false)) {
            include ROOT_LIBRARIES . 'bbcode/stringparser_bbcode.class.php';
        }

        // Instantiate the object
        $this->bbcode = new StringParser_BBCode();

        $this->setupDefaultBBCodes();

        $this->initializeBBCodesFromDatabase();
    }

    /**
     * Setup some default BBCodes
     * - Conversions and Filters
     * - Standard Elements like
     *   - url, link, img, code
     *
     */
    public function setupDefaultBBCodes()
    {
        /**
         * Conversions & Filters
         */

        $this->bbcode->addFilter (STRINGPARSER_FILTER_PRE, array( $this, 'convertlinebreaks' ) );
        $this->bbcode->addParser (array ('block', 'inline', 'link', 'listitem',), 'htmlspecialchars');
        $this->bbcode->addParser (array ('block', 'inline', 'link', 'listitem',), 'nl2br');

        /**
         * Generate Standard BB Codes
         */

        /**
         * BB Code: [url][/url]
         */
        $this->bbcode->addCode('url',
                'usecontent?',
                array($this, 'do_bbcode_url'),
                array('usecontent_param' => 'default'),
                'link',
                array('listitem', 'block', 'inline'),
                array('link'));

        /**
         * BB Code: [link][/link]
         */
        $this->bbcode->addCode('link',
                'callback_replace_single',
                array($this, 'do_bbcode_url'),
                array(),
                'link',
                array('listitem', 'block', 'inline'),
                array('link'));

        /**
         * BB Code: [link][/link]
         */
        $this->bbcode->addCode('img',
                'usecontent',
                array($this, 'do_bbcode_img'),
                array(), 'image',
                array('listitem', 'block', 'inline',
                    'link'),
                array());
        /**
         * BB Code: [code][/code]
         * This uses geshi syntax highlighting.
         */
        $this->bbcode->addCode('code',
                'usecontent?',
                array($this, 'do_bbcode_code'),
                array('usecontent_param' => 'default'),
                'code',
                array('listitem', 'block', 'inline'),
                array('code'));

        $this->bbcode->setOccurrenceType('img', 'image');
    }

    /**
     * loads all bbcodes stored in database and assigns them to the bbcode parser object
     */
    public function initializeBBCodesFromDatabase()
    {
        // Load all BB Code Definition from Database
        $bbcodes = Doctrine_Query::create()->select('*')->from('CsBbCode')->execute();

        /**
         * Add the BBCodes from DB via addCode
         */
        foreach ($bbcodes as $key => $code) {
            // allowed
            $allowed_in = explode(',', $code['allowed_in']);

            // not allowed
            $not_allowed_in = explode(',', $code['not_allowed_in']);

            /**
             * assign the code via stringparser object and its method addCode()
             */
            $this->bbcode->addCode ($code['name'], 'simple_replace', null,
                                    array ('start_tag' => $code['start_tag'], 'end_tag' => $code['end_tag']),
                                    $code['content_type'],
                                    $allowed_in, $not_allowed_in);
        }
    }

    /**
     * Parse the text and apply BBCode
     *
     * @param $text the string to parse and to apply the bbcode formatting to
     * @return bbcode parsed text
     */
    public function parse($text)
    {
        return $this->bbcode->parse($text);
    }

    /**
     * Handle BB Code URLs
     *
     * @param string
     * @param array
     * @param string
     * @param mixed
     * @param mixed
     * @return return url
     *
     * @todo $params and $node_objects are unuseed check
     */
    private function do_bbcode_url ($action, $attributes, $content, $params, $node_object)
    {
        if ($action == 'validate') {
            return true;
        }

        if (!isset ($attributes['default'])) {
            return '<a href="'.htmlspecialchars ($content).'">'.htmlspecialchars ($content).'</a>';
        }

        return '<a href="'.htmlspecialchars ($attributes['default']).'">'.$content.'</a>';
    }

    /**
     * Handle Pictures
     *
     * @todo comment params
     * @return image string
     */
    private function do_bbcode_img ($action, $attributes, $content, $params, $node_object)
    {
        if ($action == 'validate') {
            return true;
        }

        return '<img src="'.htmlspecialchars($content).'" alt="">';
    }

    /**
     * Handle PHP Code Hightlightning with GeShi
     *
     * @return codehighlighted string
     */
    private function do_bbcode_code ($action, $attributes, $content, $params, $node_object)
    {
        if ($action == 'validate') {
            return true;
        }

        // Include & Instantiate GeSHi
        if ( false === class_exists('GeSHi',false) ) {
            include ROOT_LIBRARIES . 'geshi/geshi.php';
        }

        $geshi = new GeSHi($content, $attributes['default']);

        return $geshi->parse_code();
    }

    /**
     * Convert linebreak of different OS
     *
     * @param string
     * @return line_break_converted string
     *
     * @todo note by vain: why is this needed? describe problem?
     */
    private function convertlinebreaks ($text)
    {
        return preg_replace ("/\015\012|\015|\012/", "\n", $text);
    }
}
