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

namespace Koch\Module;

/**
 * Module_Manifest_Manager
 *
 * @todo
 * A. ModuleInfoScanner
 * B. ModuleInfoReader
 */
class ManifestManager
{
    /**
     * @var array contains the moduleinformations
     */
    private static $modulesinfo = false;

    /**
     * @var array contains the system-wide module registry
     */
    private static $modulesregistry  = false;

    private static $l10n_sys_locales = array();

    /**
     * Setter for module infos
     *
     * @param array $module_infos_array
     */
    public static function setModuleInformations($module_infos_array)
    {
        self::$modulesinfo = $module_infos_array;
    }

    /**
     * Setter for modules registry
     *
     * @param array $module_registry_array
     */
    public static function setModuleRegistry($module_registry_array)
    {
        self::$modulesregistry = $module_registry_array;
    }

    /**
     * Reads the CMS Module Registry
     *
     * This is the right method if you want to know if
     * a module is installed and active or deactivated.
     *
     * @return array Module Registry Array
     */
    public static function readModuleRegistry()
    {
        return Clansuite_CMS::getInjector()->instantiate('Koch\Config')
                ->readConfig(ROOT . 'configuration' . DIRECTORY_SEPARATOR . 'modules.config.php');
    }

    /**
     * Writes the Module Registry
     *
     * @param array $array The Module Registry Array to write.
     */
    public static function writeModuleRegistry($array)
    {
        Clansuite_CMS::getInjector()->instantiate('Koch\Config')
         ->writeConfig(ROOT . 'configuration' . DIRECTORY_SEPARATOR . 'modules.config.php');
    }

    /**
     * Returns the module configuration as array
     *
     * @param  string $modulename
     * @return array  Module Configuration Array
     */
    public static function readModuleConfig($modulename)
    {
        return Clansuite_CMS::getInjector()->instantiate('Koch\Config')
                ->readModuleConfig($modulename);
    }

    /**
     * Checks if a modulename belongs to the core modules.
     *
     * @param  string  $modulename The modulename
     * @return boolean True if modulename is a core module, false otherwise.
     */
    public static function isACoreModule($modulename)
    {
        // hardcoded map with core modules
        static $core_modules = array( 'account', 'categories', 'controlcenter', 'doctrine', 'menu', 'modulemanager',
                                      'users', 'settings', 'systeminfo', 'thememanager', 'templatemanager');

        // @todo extract from module info file if core module or not
        return in_array($modulename, $core_modules);
    }

    /**
     * Get a list of all the module directories
     *
     * @return array
     */
    public static function getModuleDirectories()
    {
        return glob( ROOT_MOD . '[a-zA-Z]*', GLOB_ONLYDIR );
    }

    /**
     * Get a list of all the module names
     *
     * 4 in 1 method, handling the following cases:
     * 1. array with module names
     * 2. named array with modulenames
     * 3. array with module names and paths
     * 4. named array with modulenames and paths
     *
     * @param  boolean $only_modulenames Toggle between only_names (true) and names+paths.
     * @param  boolean $named_array      Toggle between named (true) and unnamed array.
     * @return array(  $modulename => $module_path )
     */
    public static function getModuleNames($named_array = false, $only_modulenames = false)
    {
        $modules = array();

        $module_dirs = self::getModuleDirectories();

        foreach ($module_dirs as $module_path) {
            // strip path off
            $modulename = str_replace( ROOT_MOD, '', $module_path);

            if ($only_modulenames === true) {
                if ($named_array === false) {
                    $modules[] = $modulename;
                } else {
                    $modules[] = array ( 'name' => $modulename);
                }
            } else {
                if ($named_array === false) {
                    $modules[] = array( $modulename => $module_path );
                } else {
                    $modules[] = array ( 'name' => $modulename,
                                         'path' => $module_path);
                }
            }
        }

        return $modules;
    }

    /**
     * Returns all activated modules
     *
     * @return array $activated_modules_array
     */
    public static function getAllActivatedModules()
    {
        $activated_modules_array = array();

        $modules = self::getModuleNames(true);

        foreach ($modules as $module) {
            if (true === self::isModuleActive($module)) {
                $activated_modules_array[$module] = self::$modulesregistry[$module];
            }
        }

        return $activated_modules_array;
    }

    /**
     * Checks if a module is active or deactived.
     *
     * @param boolean $module True if module activated, false otherwise.
     */
    public static function isModuleActive($module)
    {
        // load module registry, if not available yet
        if (empty(self::$modulesregistry[$module])) {
            self::$modulesregistry = self::readModuleRegistry();
        }

        // check, if the module is
        if (isset(self::$modulesregistry[$module]['active']) and self::$modulesregistry[$module]['active'] == true) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Fetches all pieces of information of a certain module
     *
     * @param  string $module
     * @return array  Module Informations
     */
    public static function getModuleInformations($module = null)
    {
        $modulename = strtolower($module);

        // check if the infos of this specific module were catched before
        if (self::$modulesinfo[$modulename] !== null) {
            return self::$modulesinfo[$modulename];
        }

        // fetch infos for the requested $module
        return self::loadModuleInformations($module);
    }

    public static function buildModuleRegistry()
    {
        foreach ($module_directories as $module_path) {
            // strip off path info and get the modulename
            $modulename = str_replace( ROOT_MOD, '', $module_path);
        }

        self::writeModuleRegistry();
    }

    /**
     * Gather Module Informations from Manifest Files
     *
     * @staticvar array $modulesinfo
     * @param  mixed array|string $module array with modulenames or one modulename
     * @return moduleinformations (self::$modulesinfo)
     */
    public static function loadModuleInformations($module = null)
    {
        // Init vars
        $module_directories = array();
        $number_of_modules = 0;

        /**
         * either fetch the module requested via parameter $module
         * fetch all modules
         */
        if ($module === null) {
            $module_directories = self::getModuleDirectories();
        } else {
            // cast string to array
            $module_directories[] = ROOT_MOD . $module;
        }

        foreach ($module_directories as $modulepath) {
            /**
             * create array with pieces of information about a module
             */

            // 1) get the modulename, by stripping off the path info
            $modulename = str_replace( ROOT_MOD, '', $modulepath);

            self::$modulesinfo[$modulename]['name']   = $modulename;
            self::$modulesinfo[$modulename]['id']     = $number_of_modules;
            self::$modulesinfo[$modulename]['path']   = $modulepath;
            self::$modulesinfo[$modulename]['core']   = self::isACoreModule($modulename);

            // active - based on /configuration/modules.config.php
            self::$modulesinfo[$modulename]['active'] = self::isModuleActive($modulename);

            // hasMenu / ModuleNavigation
            self::$modulesinfo[$modulename]['menu']   = is_file($modulepath . DIRECTORY_SEPARATOR . $modulename .'.menu.php');

            // hasInfo
            $module_infofile = $modulepath . DIRECTORY_SEPARATOR . $modulename . '.info.php';
            $config_object = Clansuite_CMS::getInjector()->instantiate('Koch\Config');
            if (is_file($module_infofile) === true) {
                #Koch_Debug::firebug($module_infofile);

                self::$modulesinfo[$modulename]['info'] = $config_object->readConfig($module_infofile);
            } else { // create file in DEV MODE
                // if the info file for a module does not exists yet, create it
                $config_object->writeConfig($module_infofile);
            }

            // hasRoutes

            // hasConfig
            $config = self::readModuleConfig($modulename);
            if ($config[$modulename] !== null) {
                self::$modulesinfo[$modulename]['config'] = $config[$modulename];

                // properties
                if ( isset($config['properties'])) {
                    self::$modulesinfo[$modulename]['settings'] = $config['properties'];
                }

                // acl
                if ( isset($config['properties_acl'])) {
                    self::$modulesinfo[$modulename]['acl'] = $config['properties_acl'];
                }
            }
            /*else {
                $modules[$modulename]['config'] = $config;
            }*/

            // hasLanguages
            self::$modulesinfo[$modulename]['languages'] = self::getLanguageInfosForModule($modulepath);

            // take some stats: increase the module counter
            self::$modulesinfo['yy_summary']['counter'] = ++$number_of_modules;
        }

        ksort(self::$modulesinfo);

        #Koch_Debug::printR(self::$modulesinfo);

        return self::$modulesinfo;
    }

    public static function getLanguageInfosForModule($modulepath)
    {
        $langinfo = array();

        // we are looking at the languages folder for the given module path
        $module_lang_dir = $modulepath . DIRECTORY_SEPARATOR . 'languages';

        // return early, if languages directory does not exist
        if (false === is_dir($module_lang_dir)) {
            return 'No language dir.';
        }

        // lets recurse this directory
        $iterator = new \RecursiveIteratorIterator(
                        new \RecursiveDirectoryIterator($module_lang_dir),
                            \RecursiveIteratorIterator::LEAVES_ONLY);

        // some leaves found (dirs and files)
        foreach ($iterator as $file) {
            // proceed with iteration instantly, if file is not a gettext file
            if (0 === preg_match('/.(mo|po)$/', $file->getFileName())) {
                 continue;
            }

            // fetch locale from path (en_UK, de_DE)
            if (1 === preg_match('/[a-z]{2}_[A-Z]{2}/', $file->getPathName(), $match)) {
                $locale = $match[0];
            }

            // fetch file extension (mo|po)
            if (version_compare(PHP_VERSION, '5.3.6') >= 0) {
                $extension = $file->getExtension();
            } else { // php lower then 5.3.6
                $extension = pathinfo($file->getFilename(), PATHINFO_EXTENSION);
            }

            /**
             * Add some more pieces of information about the file
             */

            $langinfo[$locale][$extension]['pathName']       = realpath($file->getPathName());
            $langinfo[$locale][$extension]['fileName']       = $file->getFileName();
            $langinfo[$locale][$extension]['filePermString'] = self::file_permissions($langinfo[$locale][$extension]['pathName']);
            $langinfo[$locale][$extension]['fileReadable']   = $file->isReadable();
            $langinfo[$locale][$extension]['fileWriteable']  = $file->isWritable();
            $langinfo[$locale][$extension]['timestamp']      = date(DATE_FORMAT, $file->getCTime());
            $langinfo[$locale][$extension]['cssClass']        = '-' . ($file->isReadable() ? 'r' : '') . ($file->isWritable() ? 'w' : '');

        }

        /**
         * Add some more pieces of information about the locale
         */

         // if the language definitions are not already loaded, load them
        if (empty(self::$l10n_sys_locales)) {
            // fetch arrays containing locale data
            require ROOT_FRAMEWORK . 'gettext/locales.gettext.php';
            self::$l10n_sys_locales = $l10n_sys_locales;
        }

        foreach ($langinfo as $locale => $filedata) {
            // get more data about that locale from the locales array
            if (isset(self::$l10n_sys_locales[$locale]) == true) {
                $langinfo[$locale]['country_www']   = self::$l10n_sys_locales[$locale]['country-www'];
                $langinfo[$locale]['lang_native']   = self::$l10n_sys_locales[$locale]['lang-native'];
                $langinfo[$locale]['lang_www']      = self::$l10n_sys_locales[$locale]['lang-www'];
                $langinfo[$locale]['lang']          = self::$l10n_sys_locales[$locale]['lang'];
            } else { // locale not in locales array
                $langinfo[$locale]['country_www']   = 'unknown';
                $langinfo[$locale]['lang_native']   = '<em>locale: </em>' . $locale;
                $langinfo[$locale]['lang_www']  = '';
                $langinfo[$locale]['lang']   = $locale;
            }
        }

        #Koch_Debug::printR($langinfo);

        return $langinfo;
    }

    /**
     * Returns file permissions as string
     *
     * @staticvar array $permissions
     * @param  type   $filename
     * @return string File Permissions as string, e.h. "rwx", "rw-"
     */
    private static function file_permissions($filename)
    {
        static $permissions = array("---", "--x", "-w-", "-wx", "r--", "r-x", "rw-", "rwx");
        $perm_oct = substr(decoct(fileperms($filename)), 3);

        return "[" . $permissions[(int) $perm_oct[0]] . '|' . $permissions[(int) $perm_oct[1]] . '|' . $permissions[(int) $perm_oct[2]] . "]";
    }
}
