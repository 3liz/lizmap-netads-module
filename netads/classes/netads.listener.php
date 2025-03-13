<?php

/**
 * @author    3liz
 * @copyright 2021 3liz
 *
 * @see      https://3liz.com
 *
 * @license    Mozilla Public Licence
 */
class netadsListener extends jEventListener {
    public function ongetMapAdditions($event) {
        $repository = $event->repository;
        $project = $event->project;

        $layerParcelle = 'parcelles';

        $projectNetADSCheck = \netADS\Util::projectIsNetADS($repository, $project);
        switch ($projectNetADSCheck) {
            case \netADS\Util::PROJECT_OK:
                $prefixParcelle = \netADS\Util::projectPrefixParcelleNetADS($repository, $project);
                $jscode = array('const netAdsConfig  = {"layerParcelle" : "' . $layerParcelle . '" , ' .
                    ' "parcelleQueryUrl":"' . jUrl::get('netads~dossiers:index') . '" , ' .
                    ' "prefixParcelle":"' . $prefixParcelle . '"};');

                $js = array(jUrl::get('jelix~www:getfile', array('targetmodule' => 'netads', 'file' => 'netads.js')));

                $event->add(
                    array(
                        'js' => $js,
                        'jscode' => $jscode,
                    )
                );
                break;

            case \netADS\Util::ERR_CODE_PROJECT_VARIABLE:
                $jscode = array('
                    console.warn(`Ce projet "netads" n\'est pas configuré correctement.
                    La variable "netads_idclient" doit être définie dans votre projet QGIS.`);
                ');

                $event->add(
                    array(
                        'jscode' => $jscode,
                    )
                );
                break;
        }
    }

    public function ongetRedirectKeyParams($event) {
        $repository = $event->repository;
        $project = $event->project;

        $projectNetADSCheck = \netADS\Util::projectIsNetADS($repository, $project);
        switch ($projectNetADSCheck) {
            case \netADS\Util::PROJECT_OK:
                $event->add('IDU');
                break;
        }
    }
}
