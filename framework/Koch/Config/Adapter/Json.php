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

namespace Koch\Config\Adapter;

/**
 * Koch Framework - Config Handler for Json Format
 *
 * @category    Koch
 * @package     Core
 * @subpackage  Configuration
 */
class Json
{
    /**
     * Read the config array from json file
     *
     * @param   string  The filename
     * @return mixed array | boolean false
     */
    public static function readConfig($filename)
    {
        if (is_file($filename) === false or is_readable($filename) === false) {
            throw new Koch_Exception( _('JSON Config File not existing or not readable.') );
        }

        // read file
        $json_content = file_get_contents($filename);

        // transform JSON to PHP Array
        $json = json_decode($json_content, true);

        // fetch any error
        $json_error_type = json_last_error();

        // handle the error
        if (($json === null) or ($json_error_type != JSON_ERROR_NONE)) {
            $json_error_message = self::getJsonErrorMessage($json_error_type);

            $msg = _('JSON Error in file %s : $s');

            throw new Koch_Exception( sprintf($msg, $filename, $json_error_message) );
        }

        // return json as PHP array
        return $json;
    }

    /**
     * Write the config array to json file
     *
     * @param   string  The filename
     * @param   array   The configuration array
     */
    public function writeConfig($file, array $array)
    {
        // transform array to json object notation
        $json_content = json_encode($array);

        // write to json to file
        file_put_contents($file, $json_content);
    }

    /**
     * Returns the proper json error message for the given JSON Error Type.
     * $json_error_type is generated by json_decode() and fetched via json_last_error(),
     * then passed to this method.
     *
     * @param  string $json_error_type The json error type to get the error message for.
     * @return string The json error message for the given error type.
     */
    public static function getJsonErrorMessage($json_error_type)
    {
        $json_error_messages = array(
            JSON_ERROR_DEPTH => _('The maximum stack depth has been exceeded.'),
            JSON_ERROR_STATE_MISMATCH => _('File contains invalid or malformed JSON.'),
            JSON_ERROR_CTRL_CHAR => _('Control character error, possibly invalid encoding.'),
            JSON_ERROR_SYNTAX => _('Syntax Error.'),
            JSON_ERROR_UTF8 => _('Malformed UTF-8 characters, possibly invalid encoding.')
        );

        return $json_error_messages[$json_error_type];
    }
}
