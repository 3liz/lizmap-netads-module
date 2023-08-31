<IMPACTS>
{foreach $impacts as $impact}
<IMPACT>
        <TYPE>{$impact->type}</TYPE>
        <CODE>{$impact->code}</CODE>
        <SOUS_CODE>{$impact->sous_code}</SOUS_CODE>
        <ETIQUETTE>{$impact->etiquette}</ETIQUETTE>
        <LIBELLE>{$impact->libelle}</LIBELLE>
        <DESCRIPTION>{$impact->description}</DESCRIPTION>
</IMPACT>
{/foreach}
</IMPACTS>