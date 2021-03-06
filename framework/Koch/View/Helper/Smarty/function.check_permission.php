<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage Plugins
 */

/**
 * Smarty Viewhelper for checking permissions
 *
 * Examples:
 * <pre>
 * {check_permission name="module.action"}
 * </pre>
 *
 * Type:    function<br>
 * Name:    check_permission<br>
 * Purpose: checks if a user has a certain permission<br>
 *
 * @param   array $params
 * @param   Smarty $smarty
 * @return  boolean True if user has permission, false otherwise.
 */
function smarty_function_check_permission($params, $smarty)
{
    // ensure we got parameter name
    if ( empty($params['name']) or is_string($params['name']) == false) {
        trigger_error('Parameter "name" is not a string or empty.
                                Please provide a name in the format "module.action".');

        return;
    }

    // ensure parameter name contains a dot
    if (false === strpos($params['name'], '.')) {
        trigger_error('Parameter "name" is not in the correct format.
                                Please provide a name in the format "module.action".');

        return;
    } else { // we got a permission name like "news.action_show"
        // split string by delimiter string
        $array = explode('.', $params['name']);
        $module = $array[0];
        $permission = $array[1];
    }

    // perform the permission check
    if ( false !== Koch\RBACL\ACL::checkPermission( $module, $permission ) ) {
        unset($array, $name, $permission);

        return true;
    } else {
        return false;
    }
}
