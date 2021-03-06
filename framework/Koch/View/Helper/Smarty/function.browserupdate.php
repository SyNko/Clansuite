<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**

 *
 *
 *
 * Smarty Function to output the browser update (notice) javascript.
 * @link http://www.browser-update.org/customize.html
 *
 * @example
 * {browserupdate}
 *
 * @return string
 */
function smarty_function_browserupdate()
{
$str = <<<EOD
<!--
     Browser-Update - Notice http://www.browser-update.org/
-->
<script type="text/javascript">
var $buoop = {vs:{i:7,f:3,o:10.01,s:2,n:9}}
$buoop.ol = window.onload;
window.onload=function(){
 if ($buoop.ol) $buoop.ol();
 var e = document.createElement("script");
 e.setAttribute("type", "text/javascript");
 e.setAttribute("src", "http://browser-update.org/update.js");
 document.body.appendChild(e);
}
</script>
EOD;

return $str;
}
