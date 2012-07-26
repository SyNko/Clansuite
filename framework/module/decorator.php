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
 * along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 */

namespace Koch\Module;

/**
 * Decorator for the ModuleController
 *
 * Purpose: attach plugins and methods at runtime to the module by nesting (wrapping) them.
 * Pattern: @book "GOF:175" - Decorator (structural pattern)
 *
 * @category    Koch
 * @package     Core
 * @subpackage  Module
 */
class ControllerDecorator
{
    // the moduleController to decorate
    protected $_moduleController;

    /**
     * Decorate
     */
    public function decorate(Koch_Module_Interface $moduleController)
    {
        $this->_moduleController = $moduleController;
    }

    /**
     * Checks if a decorator provides a certain method
     * Order of processing: first it checks the current decorator, then all encapsulated ones.
     */
    public function hasMethod($methodname)
    {
        // is the method provided by this decorator?
        if (method_exists($this, $methodname)) {
            // yes
            return true;
        }

        // is the method provided by an encapsulated decorator?
        if ($this->_moduleController instanceof Koch_Module_ControllerDecorator) {
            // dig into the encapsulated controller and ask for the method
            return $this->_moduleController->hasMethod($methodname);
        }

        // there was no method found
        return false;
    }

    /**
     * Magic Method __call()
     *
     * When a method call to the current decorator is not defined, it is catched by __call().
     * So the purpose of this method is to delegate method calls to the different decorators.
     * This result is, that you have the full combination of methods of the nested decorators
     * available, without losing methods.
     *
     * Several Performance-Issues:
     * 1) costs for calling __call
     * 2) costs for calling call_user_func_array()
     * 3) the nested call stack itself: the bigger the stack, the slower it becomes.
     */
    public function __call($method, $arguments)
    {
        return call_user_func_array($method, $arguments);
    }
}
