<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * This smarty function is part of "Clansuite - just an eSports CMS"
 * @link http://www.clansuite.com
 *
 * @author Jens-Andr� Koch <jakoch@web.de>
 * @copyright Copyright (C) 2009 Jens-Andr� Koch
 * @license GNU General Public License v2 or any later version
 * @version SVN $Id: $
 *
 * Smarty {move_to} block plugin
 *
 * Filename: block_move_to.php<br>
 * Type:     block<br>
 * Name:     move_to<br>
 * Date:     Januar 11, 2009<br>
 * Purpose:  move all content in move_to blocks to the position in the html document which is defined by tag parameter
 *
 * Examples:<br>
 * <pre>
 * {move_to tag="pre_head_close"}
 *    <style type="text/css">
 *       h1{font-family:fantasy;}
 *    </style>
 * {/move_to}
 * </pre>
 *
 * @param array
 * @param string
 * @param Smarty
 * @param boolean
 * @return string
 */
function smarty_block_move_to($params, $content, &$smarty, &$repeat)
{
    if ( empty($content) )
    {
        return;
    }

    if( isset($params['tag']) )
    {
        $tag = strtoupper($params['tag']);
    }
    else
    {
        $smarty->trigger_error("Parameter 'tag' missing.");
        return;
    }

    /**
     * define possible moveto positions
     * The x marks the position, the content will be moved to.
     */
    $valid_movement_positions = array('PRE_HEAD_CLOSE',  #  x</head>
                                      'POST_BODY_OPEN',  #  <body>x
                                      'PRE_BODY_CLOSE'); #  x</body>

    # check if tag is a valid movement position
    if( !in_array($tag, $valid_movement_positions) )
    {
        $smarty->trigger_error("Parameter 'tag' needs one of the following values: pre_head_close, post_body_open, pre_body_close");
        return;
    }

    $content = '@@@SMARTY:'.$tag.':BEGIN@@@'.trim($content).'@@@SMARTY:'.$tag.':END@@@';

    return $content;
}
?>