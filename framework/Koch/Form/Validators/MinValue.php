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

namespace Koch\Form\Validators;

use Koch\Form\Validator;

/**
 * Validates the value of an integer with minvalue given.
 */
class Minvalue extends Validator
{
    public $minvalue;

    public function getMinValue()
    {
        return $this->minvalue;
    }

    /**
     * Setter for the minimum length of the string.
     *
     * @param int|float $minvalue
     */
    public function setMinValue($minvalue)
    {
        if (is_string($minvalue) === true) {
            $msg = _('Parameter Minvalue must be numeric (int|float) and not %s.');
            $msg = sprintf($msg, gettype($minvalue));

            throw new \InvalidArgumentException($msg);
        }

        $this->minvalue = $minvalue;
    }

    public function getErrorMessage()
    {
        $msg = _('The value deceeds (is less than) the minimum value of %s.');

        return sprintf($msg, $this->getMinValue());
    }

    public function getValidationHint()
    {
        $msg = _('Please enter a value not deceeding (being less than) the minimum value of %s.');

        return sprintf($msg, $this->getMinValue());
    }

    protected function processValidationLogic($value)
    {
        if ($value < $this->getMinValue()) {
            return false;
        }

        return true;
    }

}
