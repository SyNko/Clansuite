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

/**
 *  Koch_Formelement
 *  |
 *  \- Koch_Formelement_Input
 *     |
 *     \- Koch_Formelement_Resetbutton
 */
class Resetbutton extends Input implements FormelementInterface
{
    public function __construct()
    {
        $this->type = 'reset';
        $this->value = _('Reset');

        $this->class    = 'ResetButton ButtonGrey';
        $this->id       = 'ResetButton';
        $this->name     = 'ResetButton';
    }
}
