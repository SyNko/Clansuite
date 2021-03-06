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
 * Validates the given language is really a language.
 */
class Locale extends Validator
{
    public function getValidationHint()
    {
        return _('Please select a valid locale.');
    }

    public function getErrorMessage()
    {
        return _('The value is not a valid locale.');
    }

    public static function isLocale($locale)
    {
        require KOCH . 'localization/locales.php';

        // turns "de_DE" into "de"
        $short_code = mb_substr($locale, 0, 2);

        if (($l10n_langs[$short_code] !== null) or (array_key_exists($short_code, $l10n_langs) === true)) {
            // looks in "de" array, returns "de_AT", "de_CH", "de_DE"...
            $sublocales = $l10n_langs[$short_code];
        } else {
            // there are no sublocales for this locale short code
            return false;
        }

        if (true === in_array($locale, array_flip($sublocales))) {
            return true;
        } else {
            return false;
        }

        unset($l10n_langs);
    }

    protected function processValidationLogic($value)
    {
        if (true === self::isLocale($value)) {
            return true;
        } else {
            return false;
        }
    }
}
