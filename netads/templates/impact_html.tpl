
{if $impacts->rowCount() == 0}
<div class="netads-impacts">Impact&nbsp;: aucun.</div>
{else}
<div class="netads-impacts">
    <div>Impacts&nbsp;:</div>
{foreach $impacts as $impact}
    <table class="table table-condensed table-striped table-bordered lizmapPopupTable">
        <thead><tr><th>Champ</th><th>Valeur</th></tr></thead>
        <tbody>
            <tr><td>Type</td><td>{$impact->type}</td></tr>
            <tr><td>Code</td><td>{$impact->code}</td></tr>
            <tr><td>Sous-code</td><td>{$impact->sous_code}</td></tr>
            <tr><td>Étiquette</td><td>{$impact->etiquette}</td></tr>
            <tr><td>Libellé</td><td>{$impact->libelle}</td></tr>
            <tr><td>Description</td><td>{$impact->description}</td></tr>
        </tbody>
    </table>
{/foreach}
</div>
{/if}
