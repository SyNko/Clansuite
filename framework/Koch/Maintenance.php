<?php

/**
 * Koch Framework
 * Jens-André Koch © 2005 - onwards
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

namespace Koch;

/**
 * Maintenance
 *
 * @todo: Umstellen auf gettext
 *
 * @category    Koch
 * @package     Core
 * @subpackage  Maintenance
 */
class Maintenance
{
    private static $language;
    private static $reason = '1';
    private static $timeout = '60';
    private static $filePath;
    private static $aText = array(
        '1' => array(
            'de' => array(
                'title' => 'Wartungsmodus',
                'reason' => 'Es werden Arbeiten an der Datenbankstruktur durchgef&uuml;hrt.',
                'sorry' => 'Bitte entschuldigen Sie die Unannehmlichkeiten.',
                'back' => 'Bitte besuchen Sie uns in ca. %d %s wieder.',
                'min' => 'Minuten',
            ),
            'en' => array(
                'title' => 'Maintenance Mode',
                'reason' => 'SITE is currently undergoing scheduled maintenance.',
                'sorry' => 'Sorry for the inconvenience.',
                'back' => 'Please try back in %d %s.',
                'min' => 'minutes',
            ),
        ),
        '2' => array(
            'de' => array(
                'title' => '',
                'reason' => '',
            ),
            'en' => array(
                'title' => '',
                'reason' => '',
            ),
        )
    );

    public function configure(array $config, $filePath = null)
    {
        // set language for maintenance msg via $config value
        self::$language = $config['language']['default'];

        // fetch reason integer from $config
        if ($config['mainteance']['reason'] > 0) {
            self::$reason = $config['mainteance']['reason'];
        }

        // read timeout value from $config
        self::$timeout = $config['mainteance']['timeout'];

        if ($filePath !== null) {
            self::$filePath = $filePath;
        } else { // use the default maintenance template
            self::$filePath = ROOT_THEMES_CORE . 'view/smarty/maintenance.tpl';
        }
    }

    /**
     * output maintenance display
     *
     * @param array  $config   The Config Array.
     * @param string $filePath FQFP for the maintenance template.
     */
    public function show(array $config, $filePath = null)
    {
        self::configure($config, $filePath);

        $title = self::$aText[self::$reason][self::$language]['title'];
        $reason = self::$aText[self::$reason][self::$language]['reason'];
        $sorry = self::$aText[self::$reason][self::$language]['sorry'];

        // replacement for "Please try back in %d %s."
        $back = sprintf(self::$aText[self::$reason][self::$language]['back'],
                        self::$timeout, self::$aText[self::$reason][self::$language]['min']);

        $content = file_get_contents(self::$filePath);

        $content = str_replace('{title}', $title, $content);
        $content = str_replace('{reason}', $reason, $content);
        $content = str_replace('{back}', $back, $content);
        $content = str_replace('{sorry}', $sorry, $content);

        echo $content;

        exit;
    }

}
