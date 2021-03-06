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

namespace Koch\Form\Decorators\Form;

use Koch\Form\Decorator;

class Form extends Decorator
{
    /**
     * Name of this decorator
     *
     * @var string
     */
    public $name = 'form';

    public function openOpenFormTag()
    {
        // init var
        $html_form = '';

        // init html form with an comment
        $html_form = '<!-- Start of Form "'. $this->getName() .'" -->' . CR;

        // open the opening form tag
        $html_form .= '<form ';

        return $html_form;
    }

    /**
     * @return string returns html of attributes inside the opening form tag
     */
    public function getFormTagAttributesAsHTML()
    {
        // init var
        $html_form = '';

        if ( mb_strlen($this->getID()) > 0 ) {
            $html_form .= 'id="'.$this->getID().'" ';
        }

        if ( mb_strlen($this->getAction()) > 0 ) {
            $html_form .= 'action="'.$this->getAction().'" ';
        }

        if ( mb_strlen($this->getMethod()) > 0 ) {
            $html_form .= 'method="'.$this->getMethod().'" ';
        }

        if ( mb_strlen($this->getEncoding()) > 0 ) {
            $html_form .= 'enctype="'.$this->getEncoding().'" ';
        }

        if ( mb_strlen($this->getTarget()) > 0 ) {
            $html_form .= 'target="'.$this->getTarget().'" ';
        }

        if ( mb_strlen($this->getName()) > 0 ) {
             $html_form .= 'name="'.$this->getName().'" ';
        }

        if ( mb_strlen($this->getAcceptCharset()) > 0 ) {
             $html_form .= 'accept-charset="'.$this->getAcceptCharset().'" ';
        }

        if ( $this->getAcceptCharset() === true ) {
             $html_form .= ' autocomplete ';
        }

        if ( $this->getNoValidation() === true ) {
             $html_form .= ' novalidation ';
        }

        $html_form .= 'class="form '.$this->getClass().'"';

        // return the attributes inside the opening form tag
        return $html_form;
    }

    public function closeOpenFormTag()
    {
        // close the opened form tag
        return '>' . CR;
    }

    public function addHeading()
    {
        $html_form = '';

        // add heading
        if ( mb_strlen($this->getHeading()) > 0 ) {
             $html_form = '<h2>'.$this->getHeading().'</h2>' . CR;
        }

        return $html_form;
    }

    public function addDescription()
    {
         $html_form = '';

        // add description
        if ( mb_strlen($this->getDescription()) > 0 ) {
             $html_form = '<p>'.$this->getDescription().'</p>' . CR;
        }

        return $html_form;
    }

    public function closeFormTag()
    {
        // close form
        return CR . '</form>' . CR . '<!--- End of Form "'. $this->getName() .'" -->' . CR;
    }

    public function render($html_form_content)
    {
        // put all the pieces of html together
        $html_form_content = $this->openOpenFormTag().              // <form
                             $this->getFormTagAttributesAsHTML().   //  id/method/action/...
                             $this->closeOpenFormTag().             // >
                             $this->addHeading().                   // heading
                             $this->addDescription().               // description
                             $html_form_content.                    // formelements
                             $this->closeFormTag();                 // </form>

        return $html_form_content;
    }
}
