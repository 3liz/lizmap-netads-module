<?php
namespace netADS;

class Util {

    const ERR_CODE_PROJECT_NAME = 1;
    const ERR_CODE_PROJECT_VARIABLE = 2;
    const PROJECT_OK = 0;

    public static function projectIsNetADS(string $repo, string $projectName) {
        $project = \lizmap::getProject($repo . '~' . $projectName);

        if (!$project || $projectName !== 'netads') {
            return self::ERR_CODE_PROJECT_NAME;
        }

        // le projet doit contenir une variable custom netads_idclient
        $customProjectVariables = $project->getCustomProjectVariables();
        if ($customProjectVariables && array_key_exists('netads_idclient', $customProjectVariables)) {
            return self::PROJECT_OK;
        }

        return self::ERR_CODE_PROJECT_VARIABLE;
    }

    public static function projectIdClientNetADS(string $repo, string $projectName) {
        if (self::projectIsNetADS($repo, $projectName) != self::PROJECT_OK) {
            return null;
        }

        $project = \lizmap::getProject($repo . '~' . $projectName);
        // le projet contient une variable custom netads_idclient
        $customProjectVariables = $project->getCustomProjectVariables();
        if ($customProjectVariables && array_key_exists('netads_idclient', $customProjectVariables)) {
            return $customProjectVariables['netads_idclient'];
        }

        return null;
    }

    public static function projectPrefixParcelleNetADS(string $repo, string $projectName) {
        if (self::projectIsNetADS($repo, $projectName) != self::PROJECT_OK) {
            return null;
        }

        $project = \lizmap::getProject($repo . '~' . $projectName);
        // le projet contient une variable custom netads_prefix_parcelle
        $customProjectVariables = $project->getCustomProjectVariables();
        if ($customProjectVariables && array_key_exists('netads_prefix_parcelle', $customProjectVariables)) {
            return $customProjectVariables['netads_prefix_parcelle'];
        }

        // get config from inifile
        $file = \jApp::varconfigPath('netads.ini.php');
        $iniFile = new \Jelix\IniFile\IniModifier($file);
        return $iniFile->getValue('prefix_parcelle');
    }
}
