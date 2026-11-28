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

    /**
     * Get the netADS dossiers of a parcel from the netADS search API.
     *
     * @param string $commune Code of the commune (département, direction and commune)
     * @param string $section Prefix and section of the parcel
     * @param string $numero  Number of the parcel
     *
     * @return array The dossier XML elements
     *
     * @throws DomainException When the API is not configured, cannot be queried or returns an error
     */
    public function getDossiers(string $commune, string $section, string $numero) {
        // The search URL is mandatory: the module may not be configured yet
        if (empty($this->searchURL)) {
            \jLog::log('netADS API search URL is not configured', 'lizmapadmin');

            throw new DomainException('netADS API not configured');
        }

        $requestParam = array(
            'idclient' => $this->netADSclientID,
            'user_login' => $this->login,
            'user_pass' => $this->encryptedPassword,
            'idrecherche' => '3',
            'idmodule' => 'ADS',
            'communes' => $commune,
            'section' => $section,
            'numero' => $numero

        );

        $options = array(
            'method' => 'post',
            'body' => http_build_query($requestParam),
            'headers' => array(
                'Content-type' => 'application/x-www-form-urlencoded',
            ),
        );

        // Network errors are raised as exceptions by the proxy
        try {
            list($data, $mime, $code) = \Lizmap\Request\Proxy::getRemoteData($this->searchURL, $options);
        } catch (\Exception $e) {
            \jLog::log('unable to query netADS API (' . $this->searchURL . ') '.$e->getMessage(), 'error');

            throw new DomainException('netADS API call error');
        }

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
