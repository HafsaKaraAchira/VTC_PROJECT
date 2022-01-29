<?php 
    require_once('CommonController.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/Model/Diaporama.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/Model/Annonce.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/Vue/PageAccueilVue.php');
    class PageAccueilController extends CommonController
    {
        public function __construct($page){
            parent::__construct($page);
        }

        public function getDiaporama(){
            $this->m = new Diaporama() ;
            $slides = $this->m->DiaporamaQuery();
            return $slides ;
        }

        public function getAnnonceSelection(){
            //var_dump(Configuration::getConfiguration());
            Configuration::getConfiguration() ;
            $nbAnnonces = (int)$_SESSION['configuration']['general']['nbVueAnnonce'] ;
            //var_dump($nbAnnonces);
            $this->m = new Annonce() ;
            $annonces = $this->m->annoncesSelectionQuery($nbAnnonces);
            return $annonces ;
        }

        public function viewPage(){
            $slides = $this->getDiaporama() ;
            $annonces = $this->getAnnonceSelection() ;
            $v = new PageAccueilVue($slides,$annonces);
            $v->view();
        }
    }
?>