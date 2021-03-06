
<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 *
 * Name:         gravatar
 * Type:         function
 * Purpose: This TAG inserts a valid Gravatar Image.
 *
 * See http://en.gravatar.com/ for further information.
 *
 * Parameters:
 * - email      = the email to fetch the gravatar for (required)
 * - size       = the images width
 * - rating     = the highest possible rating displayed image [ G | PG | R | X ]
 * - default    = full url to the default image in case of none existing OR
 *                invalid rating (required, only if "email" is not set)
 *
 * Example usage:
 *
 * {gravatar email="example@example.com" size="40" rating="R" default="http://myhost.com/myavatar.png"}
 *
 * @param array $params as described above (emmail, size, rating, defaultimage)
 * @param Smarty $smarty
 * @return string
 */
function smarty_function_gravatar($params, $smarty)
{
    $email = $defaultImage = $size = $rating = '';

    // check for email adress
    if ($params['email'] !== null) {
        $email = trim(mb_strtolower($params['email']));
    } else {
        trigger_error("Gravatar Image couldn't be loaded! Parameter 'email' not specified!");

        return;
    }

    // default avatar
    if ($params['default'] !== null) {
        $defaultImage = urlencode($params['default']);
    }

    // size
    if ($params['size'] !== null) {
        $size = $params['size'];
    }

    // rating
    if ($params['rating'] !== null) {
        $rating = $params['rating'];
    }

    // initialize gravatar library
    if (false === class_exists('clansuite_gravatar', false)) {
        include ROOT_FRAMEWORK . 'viewhelper/gravatar.core.php';
    }

    return new clansuite_gravatar($email, $rating, $size, $defaultImage);
}
?>
