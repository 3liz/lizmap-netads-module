<?php
/**
 * @author    3liz
 * @copyright 2022 3liz
 *
 * @see      https://3liz.com
 *
 * @license    GPL 3
 */
class netadsModuleInstaller extends \Jelix\Installer\Module\Installer {
    public function install(Jelix\Installer\Module\API\InstallHelpers $helpers) {
        $groupName = 'netads.subject.group';
        // Add rights group
        jAcl2DbManager::createRightGroup($groupName, 'netads~default.rights.group.name');

        // Add right subject
        jAcl2DbManager::createRight('netads.admin.access', 'netads~default.rights.admin.access', $groupName);
        jAcl2DbManager::createRight('netads.nadfile.download.ok', 'netads~default.rights.nadfile.download.ok', $groupName);

        // Add rights on group admins
        jAcl2DbManager::addRight('admins', 'netads.admin.access');
        jAcl2DbManager::addRight('admins', 'netads.nadfile.download.ok');
    }
}
