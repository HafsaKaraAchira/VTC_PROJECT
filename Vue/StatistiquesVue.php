<?php
    require_once('CommonVue.php');
    require($_SERVER['DOCUMENT_ROOT'].'/Vue/CadreVue.php');
    class StatistiquesVue extends CommonVue
    {
        private $stats ;
        private $nbUsers ;
        private $nbAnnonces ;
        private $topAnnonces ;

        public function __construct($userStats,$annonceStats,$topAnnonces){
            $this->userStats = $userStats ;
            $this->annonceStats = $annonceStats ;
            $this->topAnnonces = $topAnnonces ;
        }

        public function viewUserStats(){
            echo "<section id='userstats'>
                    <h2>Statistiques sur les utilisateurs</h2>";
            echo "<h3>Types d'utilisateurs</h3>";
            foreach($this->userStats['typeUsers'] as $type)
            {
                echo "<h4>".$type['profileType']."</h4>
                        <h5>".$type['UserCount']."</h5>
                " ;
            }   
            echo "</section>";
            //<h3>Nombre Total d'utilisateur:</h3>
            //<h4>Type : ".$this->userStats['typeUsers']['Total']."</h4>
        }

        public function viewAnnonceStats(){
            echo "<section id='annoncestats'>
                    <h2>Statistiques sur les annnonces</h2>
            " ;   
            echo "<h3>Types d'annonces</h3>";
            foreach($this->annonceStats['typeTransport'] as $type)
            {
                echo "<h4>".$type['AnnonceTypeTransport']."</h4>
                        <h5>".$type['AnnonceTypeCount']."</h5>
                " ;
            } 
            echo "<h3>Moyen de transport d'annonces</h3>";
            foreach($this->annonceStats['moyenTransport'] as $moyen)
            {
                echo "<h4>".$moyen['AnnonceMoyenTransport']."</h4>
                        <h5>".$moyen['AnnonceMoyenCount']."</h5>
                " ;
            } 
            echo "</section>";
        }

        public function viewTopAnnonce(){
            echo "<section id='topannonce'>
                    <h2>Top Annonces</h2>
                " ;   
            foreach ($this->topAnnonces as $annonce) {
                $cadre = new CadreVue();
                echo $cadre->AnnonceCadre($annonce) ;
            }
            echo "</section>";
        }

        public function contents(){
            $this->viewUserStats() ;
            $this->viewAnnonceStats() ;
            $this->viewTopAnnonce() ;
        }
    }
?>