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
            <input type='hidden' name='repository' value='{$repository}'>
            <input type='hidden' name='project' value='{$project}'>
            <input type='hidden' name='iddossier' value='{$doss->iddossier}'>
            <submit>OK</submit>
        </form>
        {else}
        <form method='post' action='{$viewURL}' target="_blank">
            <input type='hidden' name='idcommune' value='{$doss->idcommune}'>
            <input type='hidden' name='idmodule' value='{$doss->idmodule}'>
            <input type='hidden' name='iddossier' value='{$doss->iddossier}'>
            <input type='hidden' name='idclient' value='{$netADSClientId}'>
            <submit>OK</submit>
        </form>

        {/if}
    </div>
{/foreach}
