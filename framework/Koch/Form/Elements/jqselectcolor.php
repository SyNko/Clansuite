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

namespace Koch\Form\Formelement;

use Koch\Form\Formelement;
use Koch\Form\FormelementInterface;

/**
 * Renders jQuery Colorpicker for Color selection, you know?
 */
class JQSelectColor extends Formelement implements FormelementInterface
{
    /**
     * JQSelectColor uses jQuery Farbtastic Colorpicker
     */
    public function __construct()
    {
        $this->type = 'color';
    }

    public function getValue()
    {
        if (empty($this->value)) {
            // set a default color as return value
            return '#123456';
        }

        return $this->value;
    }

    public function render()
    {
        // add the javascripts to the queue of the page (@todo queue, duplication check)
        $javascript = '<script type="text/javascript" src="'.WWW_ROOT_THEMES_CORE . 'javascript/jquery/jquery.farbtastic.js"></script>
                             <link rel="stylesheet" href="'.WWW_ROOT_THEMES_CORE . 'css/farbtastic.css" type="text/css" />';

        // Add the jQuery UI Date Select Dialog.
        // Watch out, that the div dialog is present in the dom, before you assign js function to it via $('#datepicker')
        $datepicker_js   = "<script type=\"text/javascript\">
                                          $(document).ready(function() {
                                            $('#colorpicker').farbtastic('#color');
                                            $('#colorpicker').hide();
                                            $('img#color').click(function(){
                                                $('#colorpicker').toggle();
                                            });
                                          });
                                        </script>";

        $html = '<input type="text" id="color" name="'.$this->getName().'" value="'.$this->getValue().'" /><img src="'.WWW_ROOT_THEMES_CORE . 'images/icons/colors.png" align="top" style="margin-top:1px; margin-left:3px;" id="color"></img><div id="colorpicker"></div>';

        return $javascript.$datepicker_js.$html;
    }
}
