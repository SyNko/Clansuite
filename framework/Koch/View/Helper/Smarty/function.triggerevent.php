<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Name:    triggerevent
 * Type:    function
 * Purpose: This TAG is acts a trigger to an possibly registered event for the eventname $name.
 *
 * @example
 *  {triggerEvent name="onRenderXY"}
 *
 * @param $params mixed $params['name'] the eventname
 * @param Smarty $smarty
 * @return string
 */
function smarty_function_triggerevent($params, $smarty)
{
    // we need an valid eventname to trigger it
    if (empty($params['name'])) {
        trigger_error("name: Please add an event name.");

        return;
    }

    // @todo consider passing smarty or more template infos as context to the event
    $context = array();
    $context['params'] = $params;

    // pass the modulename as info
    $info = array();
    $info['modulename'] = Koch_Module_Controller_Resolver::getModuleName();

    /**
     * direct return to the template
     * this implies that events should generate HTML output
     * or just transform the $context for the later occuring rendering process
     * @see todo at context above
     */

    return Koch_Eventdispatcher::instantiate()->triggerEvent($params['name'], $context, $info);
}
