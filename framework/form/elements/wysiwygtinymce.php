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
 * along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 */

namespace Koch\Formelement;

/**
 * Formelement_Wysiwygtinymce
 *
 * @link http://tinymce.moxiecode.com/ Official Website
 * @link http://tinymce.moxiecode.com/js/tinymce/docs/api/index.html API Documentation
 * @link http://tinymce.moxiecode.com/examples/ Examples
 */
class Wysiwygtinymce extends Textarea implements FormelementInterface
{
    public function __construct()
    {
        self::checkDependencies();
    }

    /**
     * Ensure, that the library is available, before the client requests a non-existant file.
     */
    public static function checkDependencies()
    {
        if (!is_file(ROOT_THEMES_CORE . 'javascript/tiny_mce/tiny_mce.js')) {
            exit('TinyMCE Library missing!');
        }
    }

    /**
     * This renders a textarea with the WYSWIWYG editor TinyMCE attached.
     */
    public function render()
    {
        // a) loads the tinymce javascript file
        $javascript = '<script src="'.WWW_ROOT_THEMES_CORE . 'javascript/tiny_mce/tiny_mce.js" type="text/javascript"></script>';

        // b) handler to attach tinymce to a textarea named "mceSimple" and "mceAdvanced"
        $javascript .= '<script type="text/javascript">// <![CDATA[
                            tinyMCE.init({
                                mode : "textareas",
                                theme : "simple",
                                editor_selector : "mceSimple"
                            });

                            tinyMCE.init({
                                mode : "textareas",
                                theme : "advanced",
                                editor_selector : "mceAdvanced"
                            });
                        // ]]></script>';
        return $javascript;
    }
}
