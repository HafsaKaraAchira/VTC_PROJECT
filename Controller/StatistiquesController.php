<?php 
    require_once('CommonController.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/Model/Statistiques.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/Vue/StatistiquesVue.php');
    class StatistiquesController extends CommonController
    {
        public function __construct($page){
            parent::__construct($page);
            $this->m = new Statistiques() ;
        }

        public function getNbUser(){
            $userStats = $this->m->UsersStatsQuery();
            return $userStats ;
        }

        public function getNbAnnonce(){
            $annonceStats = $this->m->AnnoncesStatsQuery();
            return $annonceStats ;
        }

        public function getTopAnnonce(){
            $topAnnonce = $this->m->topAnnoncesQuery();
            return $topAnnonce ;
        }

        public function viewPage(){
            $userStats = $this->getNbUser() ;
            $annonceStats = $this->getNbAnnonce() ;
            $topAnnonce = $this->getTopAnnonce() ;
            //var_dump($topAnnonce);
            $v = new StatistiquesVue($userStats,$annonceStats,$topAnnonce);
            $v->view();
        }
    }
?>