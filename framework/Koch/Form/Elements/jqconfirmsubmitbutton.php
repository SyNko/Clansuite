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

namespace Koch\Formelement;

class JQConfirmSubmitButton extends Input implements FormelementInterface
{
    protected $message = 'Please Confirm';

    /**
     * @var string $formid Takes the name of the form (to trigger the original sumbit).
     */
    protected $formid;

    public function __construct()
    {
        $this->type = 'submit';
        $this->value = _('Confirm & Submit');
        $this->class = 'ButtonGreen';

        /**
         * Add the Form Submit Confirmation Javascript.
         * This is a jQuery UI Modal Confirm Dialog.
         *
         * a) To add the value of specific form.elements to the message use "+ form.elements['email'].value +"
         * b) Take care, that the div dialog is present in the DOM, BEFORE you assign function to it via $('#dialog')
         *
         */
        $this->description = "<div id=\"dialog\" title=\"Verify Form\">
                                  <p>If your is correct click Submit Form.</p>
                                  <p>To edit, click Cancel.<p>
                              </div>

                              <script type=\"text/javascript\">

                               // jQuery UI Dialog

                               $('#dialog').dialog({
                                    autoOpen: false,
                                    width: 400,
                                    modal: true,
                                    resizable: false,
                                    buttons: {
                                        \"Submit Form\": function() {
                                            document.".$this->formid.".submit();
                                        },
                                        \"Cancel\": function() {
                                            $(this).dialog(\"close\");
                                        }
                                    }
                                });


                              $('form#".$this->formid."').submit(function(){
                                $('#dialog').dialog('open');

                                 return false;
                               });
                              </script>
                             ";
    }

    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    public function setFormId($formid)
    {
        $this->formid = $formid;

        return $this;
    }
}
