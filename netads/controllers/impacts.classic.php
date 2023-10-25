<?php

class impactsCtrl extends jController {

    public function index() {
        $repo = $this->param('repository');
        $projectName = $this->param('project');
        $parcelleIDU = $this->param('IDU');

        $resp = $this->getResponse('text');
        if (is_null($repo) || is_null($projectName) || is_null($parcelleIDU)) {
            $resp->setHttpStatus('404', 'repository, project, IDU params are mandatory');
            return $resp;
        }
        $testNetADSProject = \netADS\Util::projectIsNetADS($repo, $projectName);
        if ($testNetADSProject != \netADS\Util::PROJECT_OK) {
            if ($testNetADSProject == \netADS\Util::ERR_CODE_PROJECT_VARIABLE) {
                $message = 'Missing project variable';
            } else {
                $message = 'Project name must be "netads"';
            }
            $resp->setHttpStatus('500', $message);
            return $resp;
        }

        if (strlen($parcelleIDU) < 12 ) {
            $resp->setHttpStatus('500', 'IDU must be 12 or 15 characters long');
            return $resp;
        }

        if (strlen($parcelleIDU) == 12 ) {
            $prefixParcelle = \netADS\Util::projectPrefixParcelleNetADS($repository, $project);
            $parcelleIDU = $prefixParcelle . $parcelleIDU;
        }

        if (strlen($parcelleIDU) != 15 ) {
            $resp->setHttpStatus('500', 'IDU must be 12 or 15 characters long');
            return $resp;
        }

        $sqlParcelle = 'SELECT ident, ndeb, sdeb, type, nom, ccocom
        FROM netads.parcelles
        WHERE ident = :ident;
         ';
        $cnx = \jDb::getConnection('netads');

        try {
            $resultset = $cnx->prepare($sqlParcelle);
            $resultset->bindValue('ident', $parcelleIDU);
            $resultset->execute();
            $data = $resultset->fetchAssociative();
        } catch (\Exception $e) {
            \jlog::log($e->getMessage(), 'error');
            $resp->setHttpStatus('500', 'Error while querying parcelle');
            return $resp;
        }

        if (is_null($data)) {
            $resp->setHttpStatus('404', 'Parcelle not found');
            return $resp;
        }

        $sqlImpact = 'SELECT DISTINCT i.id_impacts, i.type, i.code, i.sous_code, i.etiquette, i.libelle, i.description
            FROM netads.impacts i
            JOIN netads.geo_impacts gi ON i.id_impacts=gi.id_impacts
            JOIN netads.parcelles p ON (ST_INTERSECTS(p.geom, gi.geom) AND NOT ST_Touches(p.geom, gi.geom))
            JOIN netads.communes com ON (com.codeinsee=gi.codeinsee AND com.codcomm = p.ccocom)
            WHERE p.ident = :ident';

        try {
            $resultset = $cnx->prepare($sqlImpact);
            $resultset->bindValue('ident', $parcelleIDU);
            $resultset->execute();
        } catch (\Exception $e) {
            \jlog::log($e->getMessage(), 'error');
            $resp->setHttpStatus('500', 'Error while querying impact');
            return $resp;
        }
        $resp = $this->getResponse('xml');
        $tpl = new \jTpl();
        $tpl->assign('impacts', $resultset);
        $resp->content = $tpl->fetch('impact');
        return $resp;
    }
}
