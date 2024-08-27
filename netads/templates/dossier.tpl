{if $dossiers == null || count($dossiers) == 0}
<div class="netads-dossiers">Dossier&nbsp;: aucun.</div>
{else}
<div class="netads-dossiers">
    <div>Dossiers&nbsp;:</div>
{foreach $dossiers as $doss }
    <table class="table table-condensed table-striped table-bordered lizmapPopupTable">
        <thead><tr><th>Champ</th><th>Valeur</th></tr></thead>
        <tbody>
        {foreach $fields as $fieldName }
            <tr><td>{$fieldName}</td><td>{$doss->$fieldName}</td></tr>
        {/foreach}
            <tr><td></td><td>
                {if $modeDownload}
                <form method='post' action='{jurl 'netads~dossiers:nad'}' target="_blank">
                    <input type='hidden' name='repository' value='{$repository}'/>
                    <input type='hidden' name='project' value='{$project}'/>
                    <input type='hidden' name='iddossier' value='{$doss->iddossier}'/>
                    <input type='submit' class='btn jforms-ctrl-submit jforms-submit' value='Consulter' />
                </form>
                {else}
                <form method='post' action='{$viewURL}' target="_blank">
                    <input type='hidden' name='idcommune' value='{$doss->idcommune}'/>
                    <input type='hidden' name='idmodule' value='{$doss->idmodule}'/>
                    <input type='hidden' name='iddossier' value='{$doss->iddossier}'/>
                    <input type='hidden' name='idclient' value='{$netADSClientId}'/>
                    <input type='submit' class='btn jforms-ctrl-submit jforms-submit' value='Consulter' />
                </form>
                {/if}
            </td></tr>
        </tbody>
    </table>
{/foreach}
</div>
{/if}
