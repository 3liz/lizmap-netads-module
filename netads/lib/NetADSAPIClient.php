<?php
namespace netADS;

use DomainException;

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

            throw new DomainException('netADS API call error');
        }

        if ($mime != 'application/xml') {
            \jLog::log('unable to query netADS API (' . $this->searchURL . ') mime-type '.$mime, 'error');

            throw new DomainException('netADS API invalid mime tye');
        }

        $xmlObj = \Lizmap\App\XmlTools::xmlFromString($data);
        if (!is_object($xmlObj)) {
            \jLog::log('unable to query netADS API (' . $this->searchURL . ')', 'error');

            throw new DomainException('netADS API invalid XML');
        }

        $returnTag = $xmlObj->xpath('//retour')[0];
        if ((string)$returnTag->children()->succes != 'O') {
            // error : retrieving message
            $message = (string)$returnTag->children()->message;
            $description = (string)$returnTag->children()->description;
            \jLog::log('netADS API return error : '.$description, 'lizmapadmin');

            throw new DomainException($message);
        }

        $data = $xmlObj->xpath('//dossier');
        return $data;
    }

    public function getViewURL()
    {
        return $this->viewURL;
    }
}
