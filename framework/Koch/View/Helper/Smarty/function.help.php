<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage Plugins
 */

/**
 * Smarty pagination
 * Displays help text of this module
 *
 * Examples:
 * <pre>
 * {help}
 * </pre>
 *
 * Type:     function<br>
 * Name:     help<br>
 * Purpose:  displays help.tpl for a module, if existing<br>
 *

 * @param array $params
 * @param Smarty $smarty
 * @return string
 */
function smarty_function_help($params, $smarty)
{
    $modulename = $smarty->getTemplateVars('template_of_module');

    // check if file exists
    if ( $smarty->templateExists( $modulename. '/view/smarty/help.tpl') ) {
        // load the help template from modulepath ->  modulename/view/help.tpl
        return $smarty->fetch( $modulename. '/view/smarty/help.tpl');
    }
    /*elseif (DEBUG == true and DEVELOPMENT == true) {
        return $smarty->fetch( ROOT_THEMES . 'core/view/help_not_found.tpl');
    }*/
    else {
        return 'Help Template not found.';
    }
}
