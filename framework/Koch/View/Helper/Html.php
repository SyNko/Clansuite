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
 * HTML
 *
 * The class provides helper methods to output html-tag elements.
 *
 * @category    Koch
 * @package     View
 * @subpackage  HTML
 */
class HTML /* extends DOMDocument */
{
    /**
     * Renders title tag.
     *
     * @param string $title
     * @access public
     * @return string
     */
    public static function title($title)
    {
        return '<title>'.$title.'</title>'.CR;
    }

    /**
     * Renders meta
     *
     * @param string $name  the meta name
     * @param string $value the meta value
     * @access public
     * @return string
     */
    public static function meta($name, $value)
    {
        return '<meta name="'.$name.'" content="'.$value.'">'.CR;
    }

    /**
     * Renders the HTML Tag <a href=""></a>
     *
     * @param string $url        The URL (href).
     * @param string $text       The text linking to the URL.
     * @param array  $attributes Additional HTML Attribute as string.
     *
     * @return string html
     */
    public static function a($url, $text, $attributes = array())
    {
        $html_attributes = '';
        $html_attributes .= self::renderAttributes($attributes);

        return '<a href="'.$url.'" '.$html_attributes.'>'.$text.'</a>';
    }

    /**
     * Render tag a-tag with mailto-target <a href="mailto:">text</a>
     *
     * @param  string $mail  the email address
     * @param  string $title the email title.
     * @return string
     */
    public static function mailto($mail = '', $title = '')
    {
        if(empty($title)) $title = $mail;

        return '<a href="mailto:'.$mail.'">'.$title.'</a>';
    }

    /**
     * Renders the HTML Tag <span></span>
     *
     * @param string $text
     * @param array  $attributes array of attributes
     *
     * @return string html
     */
    public static function span($text, $attributes = array())
    {
        $html_attributes = '';
        $html_attributes .= self::renderAttributes($attributes);

        return '<span'.$html_attributes.'>'.$text.'</div>';
    }

    /**
     * Renders the HTML Tag <div></div>
     *
     * @param string $text       string
     * @param array  $attributes array of attributes
     *
     * @return string html
     */
    public static function div($text, $attributes = array())
    {
        $html_attributes = '';
        $html_attributes .= self::renderAttributes($attributes);

        return '<div'.$html_attributes.'>'.$text.'</div>';
    }

    /**
     * Renders the HTML Tag <p></p>
     *
     * @param string $text       string
     * @param array  $attributes array of attributes
     *
     * @return string html
     */
    public static function p($text, $attributes = array())
    {
        $html_attributes = '';
        $html_attributes .= self::renderAttributes($attributes);

        return '<p'.$html_attributes.'>'.$text.'</p>';
    }

    /**
     * Renders the HTML Tag <img></img>
     *
     * @param  string $link_to_image
     * @param  array  $attributes
     * @return string html
     */
    public static function image($link_to_image, $attributes = array())
    {
        $html_attributes = '';
        $html_attributes .= self::renderAttributes($attributes);

        return '<img' . $html_attributes . ' src="' . $link_to_image . '" />';
    }

    /**
     * Convenience/Proxy Method for self::image()
     *
     * @param string $link_to_image
     * @param array  $attributes
     */
    public static function img($link_to_image, $attributes = array())
    {
        return self::image($link_to_image, $attributes = array());
    }

    /**
     * Renders icon tag
     *
     * @param string $url the url of the icon.
     * @access public
     * @return string
     */
    public static function icon($url)
    {
        return sprintf('<link rel="icon" href="%s" type="image/x-icon" />', $url);
    }

    /**
     * HTML Tag Rendering
     * Builds a list from an multidimensional attributes array
     *
     * @example
     * $attributes = array('UL-Heading-A',
     *                  array('LI-Element-A','LI-Element-B'),
     *                     'UL-Heading-1',
     *                  array('LI-Element-1','LI-Element-2')
     *                    );
     * self::liste($attributes);
     *
     * @param  array  $attributes array of attributes
     * @return string html
     */
    public static function liste($attributes)
    {
        $html = '';

        $html .= '<ul>';
        foreach ($attributes as $attribute) {
            if (is_array($attribute)) {
                // watch out! recursion
                $html .= self::liste($attribute);
            } else {
                $html .= '<li>' . $attribute . '</li>' . CR;
            }
        }
        $html .= '</ul>' . CR;

        return $html;
    }

    /**
     * HTML Tag <h1>
     *
     * @param  string $text string
     * @return string html
     */
    public static function h1($text)
    {
        return '<h1>'.$text.'</h1>';
    }

    /**
     * HTML Tag <h2>
     *
     * @param $text string
     * @return string html
     */
    public static function h2($text)
    {
        return '<h2>'.$text.'</h2>';
    }

    /**
     * HTML Tag <h3>
     *
     * @param  string $text string
     * @return string html
     */
    public static function h3($text)
    {
        return '<h3>'.$text.'</h3>';
    }

    /**
     * Render the attributes for usage in an tag element
     *
     * @param  array  $attributes array of attributes
     * @return string Renders the HTML String of Attributes
     */
    public static function renderAttributes(array $attributes = array())
    {
        $html = '';

        if (is_array($attributes)) {
            // insert all attributes
            foreach ($attributes as $key => $value) {
                // ignore null values
                if (is_null($value)) {
                    continue;
                }

                $html .= ' ' . $key . '"' . $value . '"';
            }
        }

        return $html;
    }

    /**
     * Render an HTML Element
     *
     * @example
     * echo self::renderElement('tagname', array('attribute_name'=>'attribut_value'), 'text');
     *
     * @param  string $tagname    Name of the tag to render
     * @param  string $text       string
     * @param  array  $attributes array of attributes
     * @return string html with Attributes
     */
    public static function renderElement($tagname, $text = null, $attributes = array())
    {
        if (method_exists('Koch_HTML', $tagname)) {
            if ($attributes['src'] !== null) {
                $link = $attributes['src'];
                unset($attributes['src']);

                return self::$tagname($link, $text, $attributes);
            } elseif ($attributes['href'] !== null) {
                $link = $attributes['href'];
                unset($attributes['href']);

                return self::$tagname($link, $text, $attributes);
            } else {
                return self::$tagname($text, $attributes);
            }
        } else {
            $html = '<' . $tagname;
            $html .= self::renderAttributes($attributes);

            // close tag with slash, if we got no text to append
            if ($text === null) {
                $html .= '/>';
            } else { // just close the opening tag
                $html .= '>';
                $html .= $text;
                $html .= '</' . $tagname . '>' . CR;
            }

            return $html;
        }
    }
}
