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
        $requestParam = array(
            'idclient' => $this->netADSclientID,
            'user_login' => $this->login,
            'user_pass' => $this->encryptedPassword,
            'idrecherche' => '3',
            'idmodule' => 'ADS',
            'communes' => $commune,
            'section' => substr($section, -2),
            'numero' => $numero

        );

        $options = array(
            'method' => 'post',
            'body' => http_build_query($requestParam),
            'headers' => array(
                'Content-type' => 'application/x-www-form-urlencoded',
            ),
        );

        list($data, $mime, $code) = \Lizmap\Request\Proxy::getRemoteData($this->searchURL, $options);

        if (floor($code / 100) >= 4) {
            \jLog::log('unable to query netADS API (' . $this->searchURL . ') HTTP code '.$code, 'error');

            return null;
        }

        if ($mime != 'application/xml') {
            \jLog::log('unable to query netADS API (' . $this->searchURL . ') mime-type '.$mime, 'error');

            return null;
        }

        $xmlObj = \Lizmap\App\XmlTools::xmlFromString($data);
        if (!is_object($xmlObj)) {
            \jLog::log('unable to query netADS API (' . $this->searchURL . ')', 'error');

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
