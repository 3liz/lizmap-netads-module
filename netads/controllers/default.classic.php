<?php

class defaultCtrl extends jController
{
    public function index()
    {
        /**
         * @var $r jResponseText;
         */
         $r = $this->getResponse('text');
         $r->content  = 'hello';
         return $r;
    }
}

