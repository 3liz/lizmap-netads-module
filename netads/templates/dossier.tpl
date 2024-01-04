{foreach $dossiers as $doss }
    <div>
        Dossier :
        <table>
            {foreach $fields as $fieldName }
                <tr>
                    <td>{$fieldName}</td>
                    <td>{$doss->$fieldName}</td>
                </tr>
            {/foreach}
        </table>
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
    </div>
{/foreach}
{if count($dossiers) == 0}
    <div>Dossier : aucun</div>
{/if}
