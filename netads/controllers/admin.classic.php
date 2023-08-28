<?php

class adminCtrl extends jController {
    private $iniFile;

    public function __construct($request) {
        parent::__construct($request);
        $file = jApp::varconfigPath('netads.ini.php');
        $this->iniFile = new \Jelix\IniFile\IniModifier($file);
    }

    public function show() {
        /**
         * @var $resp jResponseHTML;
         */
        $resp = $this->getResponse('html');

        $form = jForms::create('netads~netadsadmin');
        $tpl = new jTpl();
        $this->initFormWithIni($form);
        $tpl->assign('form', $form);

        $resp->body->assign('MAIN', $tpl->fetch('show_config'));
        $resp->body->assign('selectedMenuItem', 'netads');

        return $resp;
    }

    public function prepare() {
        $form = jForms::create('netads~netadsadmin');
        $this->initFormWithIni($form);
        return $this->redirect('netads~admin:edit');
    }

    public function edit() {
        /**
         * @var $resp jResponseHTML;
         */
        $resp = $this->getResponse('html');

        $form = jForms::get('netads~netadsadmin');
        $tpl = new jTpl();
        $tpl->assign('form', $form);

        $resp->body->assign('MAIN', $tpl->fetch('config'));
        $resp->body->assign('selectedMenuItem', 'netads');

        return $resp;
    }

    public function save() {
        $form = jForms::fill('netads~netadsadmin');
        if (!$form->check()) {
            return $this->redirect('netads~admin:edit');
        }
        // Save the data
        foreach ($form->getControls() as $ctrl) {
            if ($ctrl->type != 'submit') {
                $this->iniFile->setValue($ctrl->ref, $form->getData($ctrl->ref));
            }
        }
        $this->iniFile->save();
        jForms::destroy('netads~netadsadmin');
        return $this->redirect('netads~admin:show');
    }

    private function initFormWithIni($form) {
        // init form
        foreach ($form->getControls() as $ctrl) {
            if ($ctrl->type != 'submit') {
                $form->setData($ctrl->ref, $this->iniFile->getValue($ctrl->ref));
            }
        }
    }
}
