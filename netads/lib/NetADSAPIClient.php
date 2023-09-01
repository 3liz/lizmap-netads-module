<?php
namespace netADS;

class NetADSAPIClient {
    private $netADSclientID;
    private $login;
    private $encryptedPassword;
    private $searchURL;
    private $viewURL ;

    public function __construct($clientID) {
        // get config from inifile
        $file = \jApp::varconfigPath('netads.ini.php');
        $iniFile = new \Jelix\IniFile\IniModifier($file);

        $this->netADSclientID = $clientID;
        $this->login = $iniFile->getValue('login');
        $this->encryptedPassword = $iniFile->getValue('password');
        $this->searchURL = $iniFile->getValue('search_url');
        $this->viewURL = $iniFile->getValue('view_url');
    }

    public function getDossiers(string $commune, string $section, string $numero) {
        $requestParam = array('id_client' => $this->netADSclientID,
            'user_login' => $this->login,
            'user_pass' => $this->encryptedPassword,
            'idrecherche' => '3',
            'idmodule' => 'ADS',
            'communes' => $commune,
            'section' => $section,
            'numero' => $numero

        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_URL, $this->searchURL);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $requestParam);

        $data = curl_exec($ch);
        curl_close($ch);

        $xmlObj = \Lizmap\App\XmlTools::xmlFromString($data);
        if (!is_object($xmlObj)) {
            \jLog::log('unable to query netADS API (.' . $this->searchURL . ')', 'error');

            return null;
        }

        $data = $xmlObj->xpath('//dossier');
        return $data;
    }

    public function getViewURL()
    {
        return $this->viewURL;
    }
}
