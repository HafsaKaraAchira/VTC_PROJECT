<?php 
    require_once('CommonController.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/Model/SiteInfos.php');
    class SiteInfosController extends CommonController
    {

        public function __construct($page){
            parent::__construct($page);
            //var_dump($page) ;
            $this->m = new SiteInfos() ;
        }

        public function getContacts(){
            $contacts = $this->m->ContactQuery();
            //var_dump($contacts) ;
            return $contacts ;
        }

        public function getPresentation(){
            $presentation = $this->m->PresentationQuery();
            return $presentation ;
        }

        public function viewPage(){
            $vue_name = $this->page.'Vue' ;
            require_once($_SERVER['DOCUMENT_ROOT'].'/Vue'.'/'.$vue_name.'.php');
            $siteInfos = ($this->page == 'Contact' ? $this->getContacts() : $this->getPresentation()[0] ) ;
            $this->v = new $vue_name($siteInfos);
            $this->v->view();
        }
    }
?>