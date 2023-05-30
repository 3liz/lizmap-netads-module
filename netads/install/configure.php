<?php
/**
 * @author    3liz
 * @copyright 2022 3liz
 *
 * @see      https://3liz.com
 *
 * @license    GPL 3
 */

use Jelix\Routing\UrlMapping\EntryPointUrlModifier;
use \Jelix\Routing\UrlMapping\MapEntry\MapInclude;

/**
 * Configurator for Lizmap 3.6+/Jelix 1.8+
 */
class netadsModuleConfigurator extends \Jelix\Installer\Module\Configurator {

    public function getDefaultParameters()
    {
        return array();
    }


    public function declareUrls(EntryPointUrlModifier $registerOnEntryPoint)
    {

        $registerOnEntryPoint->havingName(
            'netads',
            array(
                new MapInclude('urls.xml')
            )
        )
        ;
    }

    public function getEntryPointsToCreate()
    {
        return array(
            new \Jelix\Installer\Module\EntryPointToInstall(
                'netads.php',
                'netads/config.ini.php',
                'netads.php',
                'config/config.ini.php'
            )
        );
    }


    function configure(\Jelix\Installer\Module\API\ConfigurationHelpers $helpers)
    {
    }
}