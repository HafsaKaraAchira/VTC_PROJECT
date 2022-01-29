<?php
    require_once('CommonVue.php');
    class PresentationVue extends CommonVue
    {
        private $presentation ;

        public function __construct($presentation){
            $this->presentation = $presentation ;
            //var_dump($this->presentation);
        }

        public function presentation(){
            echo "<section id='selection'>
                    <h2>Présentation</h2>
                " ;   
            echo "<div class='presentation'>
                        <section id='objectifs'>
                            <h3>Nos Objectifs</h3>
                            <p>".$this->presentation['ObjectifText']."</p>
                        </section>
                        <img  width='70%' src='".(substr_count($this->presentation['ImageLink'],$_SESSION['imageFolder'])?"/?view=PageAccueil&action=viewImage&link=".basename($this->presentation['ImageLink']):$this->presentation['ImageLink'])."'>
                        <video width='100%' controls autoplay muted >
                            <source src='".$this->presentation['PresentationVideoLink']."' type='video/mp4'> >
                        </video>
                        <section id='fonctionnement'>
                            <h3>Comment ça fonctionne ?</h3>
                            <p>".$this->presentation['FonctionnmentText']."</p>
                        </section>
                    </div>";
            echo "</section>";
        }

        public function contents(){
            $this->presentation();
        }
    }
?>