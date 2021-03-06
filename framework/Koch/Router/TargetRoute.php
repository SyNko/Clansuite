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

namespace Koch\Router;

use Koch\Mvc\Mapper;
use Koch\Mvc\HttpRequest;

/**
 * Router_TargetRoute (processed RequestObject)
 */
class TargetRoute extends Mapper
{
    public static $parameters = array(
        // File
        'filename'      => null,
        'classname'     => null,
        // Call
        'controller'    => 'index',
        'subcontroller' => null,
        'action'        => 'index',
        'method'        => null,
        'params'        => null,
        // Output
        'format'        => 'html',
        'language'      => 'en',
        'request'       => 'get',
        'layout'        => true,
        'ajax'          => false,
        'renderer'      => 'smarty',
        'themename'     => null,
        'modrewrite'    => false
    );

    /**
     * TargetRoute is a Singleton
     *
     * @return instance of Koch_TargetRoute class
     */
    public static function getInstance()
    {
        static $instance = null;

        if ($instance === null) {
            $instance = new TargetRoute();
        }

        return $instance;
    }

    public static function setFilename($filename)
    {
        self::$parameters['filename'] = $filename;
    }

    public static function getFilename()
    {
        if (empty(self::$parameters['filename'])) {
            self::setFilename(self::mapControllerToFilename(self::getModulePath(), self::getController(), self::getSubController()));
        }

        return self::$parameters['filename'];
    }

    public static function setClassname($classname)
    {
        self::$parameters['classname'] = $classname;
    }

    public static function getClassname()
    {
        if (empty(self::$parameters['classname'])) {
            $classname = self::mapControllerToClassname(self::getController(), self::getSubController());

            self::setClassname($classname);
        }

        return self::$parameters['classname'];
    }

    public static function setController($controller)
    {
        self::$parameters['controller'] = $controller;
    }

    /**
     * Returns Name of the Controller
     *
     * @return string Controller/Modulename
     */
    public static function getController()
    {
        return self::$parameters['controller'];
    }

    /**
     * Convenience/shorthand Method for getController()
     *
     * @return string Controller/Modulename
     */
    public static function getModuleName()
    {
        return self::$parameters['controller'];
    }

    public static function setSubController($subcontroller)
    {
        self::$parameters['subcontroller'] = $subcontroller;
    }

    public static function getSubController()
    {
        return self::$parameters['subcontroller'];
    }

    /**
     * Method to get the SubModuleName
     *
     * @return $string
     */
    public static function getSubModuleName()
    {
        return self::$parameters['subcontroller'];
    }

    public static function setAction($action)
    {
        self::$parameters['action'] = $action;
    }

    public static function getAction()
    {
        return self::$parameters['action'];
    }

    public static function getActionNameWithoutPrefix()
    {
        $action = str_replace('action_', '', self::$parameters['action']);
        $action = str_replace('admin_', '', $action);

        return $action;
    }

    public static function setId($id)
    {
        self::$parameters['params']['id'] = $id;
    }

    public static function getId()
    {
        return self::$parameters['params']['id'];
    }

    /**
     * Method to get the Action with Prefix
     *
     * @return $string
     */
    public static function getActionName()
    {
        return self::$parameters['method'];
    }

    public static function setMethod($method)
    {
        self::$parameters['method'] = $method;
    }

    public static function getMethod()
    {
        // check if method is correctly prefixed with 'action_'
        if (self::$parameters['method'] !== null and mb_strpos(self::$parameters['method'], 'action_')) {
            return self::$parameters['method'];
        } else {
            // add method prefix (action_) and subcontroller prefix (admin_)
            $method = self::mapActionToActioname(self::getAction(), self::getSubController());
            self::setMethod($method);
        }

        return self::$parameters['method'];
    }

    public static function setParameters($params)
    {
        self::$parameters['params'] = $params;
    }

    public static function getParameters()
    {
        return self::$parameters['params'];
    }

    public static function getFormat()
    {
        return self::$parameters['format'];
    }

    public static function setRequestMethod()
    {
        self::$parameters['request'];
    }

    public static function getRequestMethod()
    {
        return HttpRequest::getRequestMethod();
    }

    public static function getLayoutMode()
    {
        return (bool) self::$parameters['layout'];
    }

    public static function getAjaxMode()
    {
        return HttpRequest::isAjax();
    }

    public static function getRenderEngine()
    {
        return self::$parameters['renderer'];
    }

    public static function setRenderEngine($renderEngineName)
    {
        self::$parameters['renderer'] = $renderEngineName;
    }

    public static function getBackendTheme()
    {
        return ($_SESSION['user']['backend_theme'] !== null) ? $_SESSION['user']['backend_theme'] : 'admin';
    }

    public static function getFrontendTheme()
    {
        return ($_SESSION['user']['frontend_theme'] !== null)  ? $_SESSION['user']['frontend_theme'] : 'standard';
    }

    public static function getThemeName()
    {
        if (empty(self::$parameters['themename'])) {
            if (self::getModuleName() == 'controlcenter' or self::getSubModuleName() == 'admin') {
                self::setThemeName(self::getBackendTheme());
            } else {
                self::setThemeName(self::getFrontendTheme());
            }
        }

        return self::$parameters['themename'];
    }

    public static function setThemeName($themename)
    {
        self::$parameters['themename'] = $themename;
    }

    public static function getModRewriteStatus()
    {
        return (bool) self::$parameters['modrewrite'];
    }

    public static function getModulePath()
    {
        return ROOT_MOD . self::getController() . DIRECTORY_SEPARATOR;
    }

    /**
     * Method to check if the TargetRoute relates to correct file, controller and action.
     * Ensures route is valid.
     *
     * @return boolean True if TargetRoute is dispatchable, false otherwise.
     */
    public static function dispatchable()
    {
        $classname = self::getClassname();
        $filename = self::getFilename();
        $method = self::getMethod();

        // was the class loaded before? no? then autoload it.
        if (class_exists($classname) === false) {
            // if still no luck, lets try loading manually
            if (is_file(ROOT_FRAMEWORK . $filename)) {
                include ROOT_FRAMEWORK . $filename;
            }
        }

        if (class_exists($classname, false) === true) {
            if (is_callable($classname, $method) === true) {
                return true;
            }
        }

        unset($filename, $classname, $method);

        return false;
    }

    public static function reset()
    {
        $reset_params = array(
            // File
            'filename' => null,
            'classname' => null,
            // Call
            'controller' => 'index',
            'subcontroller' => null,
            'action' => 'index',
            'method' => null,
            'params' => null,
            // Output
            'format' => 'html',
            'language' => 'en',
            'request' => 'get',
            'layout' => true,
            'ajax' => false,
            'renderer' => 'smarty',
            'themename' => null,
            'modrewrite' => false
        );

        #self::$parameters = array_merge(self::$parameters, $reset_params);
        self::$parameters = $reset_params;
    }

    public static function getRoute()
    {
        return self::$parameters;
    }

    public static function debug()
    {
        \Koch\Debug\Debug::printR(self::$parameters);
    }

    /**
     * Sets the given key
     *
     * @param  mixed       $key
     * @param  mixed       $value
     * @return TargetRoute
     */
    public function set($key, $value)
    {
        $this[$key] = $value;

        return $this;
    }

    /**
     * Returns the value of the given key, if the key is not set, returns the default.
     *
     * @param  mixed $key     Key.
     * @param  mixed $default Default Value.
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return isset($this[$key]) ? $this[$key] : $default;
    }

    public function toArray()
    {
        return $this->getArrayCopy();
    }
}
