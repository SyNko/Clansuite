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

class Widget
{
    /**
     * loadModul
     *
     * - constructs classname
     * - constructs absolute filename
     * - hands both to requireFile, returns true if successfull
     *
     * The classname for modules is prefixed 'module_' . $modname
     * The filename is 'clansuite/modules/'. $modname .'.module.php'
     *
     * String Variants to consider:
     * 1) admin
     * 2) module_admin
     * 3) module_admin_menueditor
     *
     * @param  string  $modulename The name of the module, which should be loaded.
     * @return boolean
     */
    public static function loadModul($modulename)
    {
        $modulename = mb_strtolower($modulename);

        // apply classname prefix to the modulename
        $modulename = \Koch\Functions::ensurePrefixedWith($modulename, 'clansuite_module_');

        // build classname from modulename
        $classname = \Koch\Functions::toUnderscoredUpperCamelCase($modulename);

        /**
         * now we have a common string like 'clansuite_module_admin_menu' or 'clansuite_module_news'
         * which we split at underscore, via explode, resulting in an array
         * like: Array ( [0] => clansuite [1] => module [2] => admin [3] => menu )
         * or  : Array ( [0] => clansuite [1] => module [2] => news )
         */
        $moduleinfos = explode('_', $modulename);
        unset($modulename);
        $filename = ROOT_MOD;

        // if there is a part [3], we have to require a submodule filename
        if ($moduleinfos['3'] !== null) {
            // and if part [3] is "admin", we have to require a admin submodule filename
            if ($moduleinfos['3'] == 'admin') {
                // admin submodule filename, like news.admin.php
                $filename .= $moduleinfos['2'] . DIRECTORY_SEPARATOR . 'controller' . DIRECTORY_SEPARATOR . $moduleinfos['2'] . '.admin.php';
            } else {
                // normal submodule filename, like menueditor.module.php
                $filename .= $moduleinfos['3'] . DIRECTORY_SEPARATOR . 'controller' . DIRECTORY_SEPARATOR . $moduleinfos['3'] . '.module.php';
            }
        } else {
            // module filename
            $filename .= $moduleinfos['2'] . DIRECTORY_SEPARATOR . 'controller' . DIRECTORY_SEPARATOR . $moduleinfos['2'] . '.module.php';
        }

        return class_exists($classname);
        #if(false === class_exists($classname))
        #{
        //    return \Koch\Autoload\Loader::requireFile($filename, $classname);
        #}
    }

}
