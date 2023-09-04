<?php

class adminnetadsListener extends \jEventListener
{
    public function onmasteradminGetMenuContent($event)
    {
        if (jAcl2::check('netads.admin.access')) {
            // new section
            $sectionAuth = new masterAdminMenuItem('netads', "NetADS", '', 120);
            // add config page
            $sectionAuth->childItems[] = new masterAdminMenuItem('netads', "Configuration", jUrl::get('netads~admin:show'), 150, 'netads_conf');

            $event->add($sectionAuth);
        }
    }
}