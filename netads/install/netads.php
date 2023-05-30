<?php
/**
 * Entry point for OpenADS API.
 *
 * @author    3liz
 * @copyright 2018-2020 3liz
 *
 * @see      https://3liz.com
 *
 * @license   GPL 3
 */
require ('../application.init.php');

require (JELIX_LIB_CORE_PATH . 'request/jClassicRequest.class.php');

checkAppOpened();

// mapping of url to basic url (/module/controller/method)

$mapping = array(
    '/services/:projectKey/parcelle/:ids_parcelles/dossier' => array(
        'GET' => '/netads/parcelle/dossiers',
    ),
    '/services/:projectKey/dossier/:id_dossier/desktop_launcher' => array(
        'GET' => '/netads/dossier/desktop_launcher',
    ),
    '/services/:projectKey/commune/:insee/dossier/:id_dossier/web_link' => array(
        'GET' => '/netads/dossier/web_link',
    ),
    '/hello' =>  '/netads/default/index'
);

jApp::loadConfig('netads/config.ini.php');

jApp::setCoord(new jCoordinator());
jApp::coord()->process(new \netADS\Request($mapping));