<?php

class dossiersCtrl extends jController {

    public $pluginParams = array(
        'nad' => array('jacl2.right' => 'netads.nadfile.download.ok'),
    );

    public function index() {
        $repo = $this->param('repository');
        $projectName = $this->param('project');
        $parcelleFID = $this->param('parcelle_fid');

        $resp = $this->getResponse('htmlfragment');
        $missingParams = $this->checkMissingParams(array('parcelle_fid'));
        if (!is_null($missingParams)) {
            $resp->setHttpStatus('400', 'missing ' . implode(',', $missingParams) . ' param in request');
            return $resp;
        }
        $testNetADSProject = \netADS\Util::projectIsNetADS($repo, $projectName);
        if ($testNetADSProject != \netADS\Util::PROJECT_OK) {
            if ($testNetADSProject == \netADS\Util::ERR_CODE_PROJECT_VARIABLE) {
                $message = 'Missing projet variable';
            } else {
                $message = 'Project name must be "netads"';
            }
            $resp->setHttpStatus('500', $message);
            return $resp;
        }

        $sqlParcelle = 'SELECT id_parcelles, ccodep, ccodir, ccocom, ccopre, ccosec, dnupla, ident
        , ccodep||ccodir||ccocom as code_commune, ccopre||ccosec as code_section
        FROM netads.parcelles
        WHERE id_parcelles = :idparcelle ';

        $cnx = \netADS\Util::getConnection($repo, $projectName);

        try {
            $resultset = $cnx->prepare($sqlParcelle);
            $resultset->bindValue('idparcelle', intval($parcelleFID));
            $resultset->execute();
            $data = $resultset->fetchAssociative();
        } catch (\Exception $e) {
            jlog::log($e->getMessage(), 'error');
            $resp->setHttpStatus('500', 'Error while query');
            return $resp;
        }

        // curl sur le service externe
        $netADSClientId = $this->getNetADSClientID();
        $apiClient = new \netADS\NetADSAPIClient($netADSClientId);
        $dossiers = $apiClient->getDossiers($data['code_commune'], $data['code_section'], $data['dnupla']);

        $modeDownload = \jAcl2::check('netads.nadfile.download.ok');

        $dossierFields = array('idmodule',
            'idcommune',
            'iddossier',
            'nature',
            'naturecomplement',
            'modtft_lettre',
            'libellemodtft',
            'reforme',
            'datedepot',
            'datedecision',
            'naturedecision',
            'libelledecision',
            'instructeur',
            'instructeur_initiales',
            'tiers_nom',
            'terrain_numvoie',
            'terrain_lettrevoie',
            'terrain_adresse1',
            'terrain_adresse2',
            'terrain_adresse3',
            'terrain_adresse4',
            'terrain_boitepostale',
            'terrain_cedex',
            'terrain_codepostal',
            'terrain_ville',
            'datedoc',
            'datedaact',
            'mode',
            'modedepot',
            'mode_long',
            'projet',
            'lastEventDesc',
        );
        $resp->tpl->assign('fields', $dossierFields);
        $resp->tpl->assign('dossiers', $dossiers);
        $resp->tpl->assign('modeDownload',$modeDownload);
        $resp->tpl->assign('repository', $repo);
        $resp->tpl->assign('project',$projectName);
        $resp->tpl->assign('netADSClientId',$netADSClientId);
        $resp->tpl->assign('viewURL', $apiClient->getViewURL());
        $resp->tplname = 'netads~dossier';

        return $resp;
    }

    public function nad() {
        $repo = $this->param('repository');
        $projectName = $this->param('project');
        $idDossier = $this->param('iddossier');
        $missingParams = $this->checkMissingParams(array('iddossier'));
        if (!is_null($missingParams)) {
            $resp = $this->getResponse('text');
            $resp->setHttpStatus('400', 'missing ' . implode(',', $missingParams) . ' param in request');
            return $resp;
        }

        $testNetADSProject = netADS\Util::projectIsNetADS($repo, $projectName);
        if ($testNetADSProject != \netADS\Util::PROJECT_OK) {
            if ($testNetADSProject == \netADS\Util::ERR_CODE_PROJECT_VARIABLE) {
                $message = 'Missing projet variable';
            } else {
                $message = 'Project name must be "netads"';
            }
            $resp = $this->getResponse('text');
            $resp->setHttpStatus('500', $message);
            return $resp;
        }
        $resp = $this->getResponse('binary');
        $content = "D\nADS\n" . $idDossier;

        $resp->outputFileName = 'dossier.nad';
        $resp->mimeType = 'text/simple';
        $resp->content = $content;
        $resp->doDownload = true;
        return $resp;
    }

    protected function getNetADSClientID() {
        $repo = $this->param('repository');
        $projectName = $this->param('project');

        return netADS\Util::projectIdClientNetADS($repo, $projectName);
    }

    protected function checkMissingParams($additionnalParams = array()) {
        $defaultParams = array('repository', 'project');
        $allParams = array_merge($defaultParams, $additionnalParams);
        $missingParams = null;
        foreach ($allParams as $paramName) {
            if (is_null($this->param($paramName))) {
                $missingParams[] = $paramName;
            }
        }
        return $missingParams;
    }
}
