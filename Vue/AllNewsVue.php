<?php
    require_once('CommonVue.php');
    require($_SERVER['DOCUMENT_ROOT'].'/Vue/CadreVue.php');
    class AllNewsVue extends CommonVue
    {
        private $allNews ;

        public function __construct($allNews){
            $this->allNews = $allNews ;
        }

        public function AllNews(){

            echo "<section id='allnews'>
                    <h2>Tous les News</h2>
                " ;   
            foreach ($this->allNews as $news) {
                $cadre = new CadreVue() ;
                echo $cadre->NewsCadre($news) ;
            }
            echo "</section>";
        }

        public function contents(){
            $this->AllNews();
        }
    }
?>