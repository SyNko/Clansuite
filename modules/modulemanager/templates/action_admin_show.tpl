{doc_raw}
<link rel="stylesheet" type="text/css" href="{$www_root_themes_core}/css/mocha/mocha.css" />
<script type="text/javascript" src="{$www_root_themes_core}/javascript/mootools/mootools.js"></script>
<script type="text/javascript" src="{$www_root_themes_core}/javascript/mootools/mootools-more.js"></script>
<script type="text/javascript" src="{$www_root_themes_core}/javascript/mootools/mocha/mocha.js"></script>
<script type="text/javascript" src="{$www_root_themes_core}/javascript/mootools/mocha/mocha-init.js"></script>
{/doc_raw}
{literal}
<script type="text/javascript">
window.addEvent('domready', function() {
    var confirm = function() {        
        new MochaUI.Window({
            class: 'delete',
            title: 'Please confirm...',
            loadMethod: 'xhr',
            content: 'Are you sure you want to delete the module?',
            width: 330,
            height: 180,
            x: 570,
            y: 280,
            resizeLimit: {'x': [275, 2500], 'y': [125, 2000]},
            toolbar: true
        });    
    }

    $$('.delete').addEvent('click', function(e) {
        new Event(e).stop();
        confirm();
    });
});
</script>
{/literal}
<table cellpadding="0" cellspacing="0" border="0" width="100%">
    <tr>
        <th class="td_header_small">{t}Modulename{/t}</th>
        <th class="td_header_small">{t}Information{/t}</th>
        <th class="td_header_small">{t}Actions{/t}</th>
    </tr>
    {foreach from=$modules item=mod}
    <tr>
        <td class="cell1" width="150" align="center">{$mod.name}</td>
        <td class="cell2"></td>
        <td class="cell1">
            <a href="#" class="delete" title="Delete">Delete</a>
        </td>
    </tr>
    {/foreach}
</table>
