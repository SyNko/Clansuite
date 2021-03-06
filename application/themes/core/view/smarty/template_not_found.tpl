
<style type="text/css">
/* this defines the look of the red error box, for example: when an template is missing */
.error {
    background:#FFDDDD url({$www_root_themes_core}images/icons/error.png) no-repeat scroll 15px 12px;
    border:1px solid #FFBBBB;
    color:#BB0000;
    font-weight:bold;
    margin:0.5em 0 1em;
    padding:15px 20px 15px 50px;
}

/* this defines the look of box, providing the link to the editor, if an template is missing */
.create {
    background:#DDFFDD url({$www_root_themes_core}images/icons/page_edit.png) no-repeat scroll 15px 12px;
    border:1px solid #BBFFBB;
    color:#00BB00;
    font-weight:bold;
    margin:0.5em 0 1em;
    padding:15px 20px 15px 50px;
}
</style>


<div class="error">
    <strong>{t}The Template for the module and action you requested was{/t} <u> {t}not found{/t}</u> !</strong>
</div>

{if $smarty.const.DEBUG AND $smarty.const.DEVELOPMENT}
<div class="create">
        {t}You can create this template directly in the{/t}
        <a href="{$www_root}index.php?mod=templatemanager&amp;sub=admin&amp;action=edit&amp;file={$modulename}/view/{$templatename}&amp;tplmod={$modulename}">Templateeditor</a>
        {t} now.{/t}
</div>
{/if}
