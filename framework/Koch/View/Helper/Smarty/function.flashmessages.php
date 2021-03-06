<?php
/**
 * Koch FrameworkSmarty Viewhelper for rendering Flashmessages
 */
function smarty_function_flashmessages($params, $smarty)
{
    // render only a certain type of flashmessages
    if ($params['type'] !== null) {
        return Koch_Flashmessages::render($params['type']);
    } else { // render all

        return Koch_Flashmessages::render();
    }
}
