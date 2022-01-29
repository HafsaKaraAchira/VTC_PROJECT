<?php 
    abstract class CommonVue
    {
        
        protected $my_secret_key = '3klmsd94mms.saeo44o!!3le';

        protected $username ; //='anonymous' ;

        //public function __construct(){
        //    var_dump($_SESSION['profile']) ;
        //}
        public function __get($property){return $this->property;}

        public function entete(){
            $this->username = !empty($_SESSION['profile'])?$_SESSION['profile']['prenom']:'anonymous' ;
            //var_dump($this->username) ;
            echo "<!DOCTYPE html>
                <html>
                    <head>
                        <meta charset='utf-8'>
                        <meta name='description' content='vtc transport site'>
                        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                        <title>VTC TRANSPORT</title>
                        <link rel='icon' href='./../Assets/car.png' type='image/x-icon'>
                        <script type ='text/javascript' src='./../Assets/jquery-3.6.0.js' ></script>
                        <!-- <script type ='text/javascript' src='./../Vue/VueFormatter.js' ></script> -->
                        <link rel='stylesheet' href='./../Vue/styles.css' type='text/css'>
                        <link rel='stylesheet' href='https://use.fontawesome.com/releases/v5.12.1/css/all.css' crossorigin='anonymous'>
                        <script>
                            let jq = jQuery.noConflict();
                            jq(document).ready(function() {
                                profile_state();
                            });//ready
                                
                        function profile_state(){    
                            var username = '".$this->username."' ;
                            //console.log(username);
                            if(username!=='anonymous'){
                                jq('header button#inscription').css('display','none');
                                jq('header button#connexion').css('display','none');
                                jq('header button#profile').css('display','block');
                                jq('header button#deconnecter').css('display','block');
                                jq('header').append('<h5>Bonjour ".$this->username." !</h5>') ;
                            }
                        }//profile_state()
                        </script>
                    
                    </head>
                    <body>
                ";
        }

        public function fermeture(){
            echo "</body>
                </html>";
        }

        public function head(){
            echo "<header>
                    <a href='/?view=PageAccueil'><img src='./../Assets/car.png' alt='logo'></a>
                    <h1>VTC Transport de matériels et colis </h1>
                    <div>
                        <button id='inscription' onclick='window.location=\"/?view=Inscription\"'' >Inscription</button>
                        <button id='connexion' onclick='window.location=\"/?view=Login\"' >Connexion</button>
                        <button id='profile' class='".$this->username."' onclick='window.location=\"/?view=Profile\"'' style='display:none;' >Profile</button>
                        <button id='deconnecter' onclick='window.location=\"/?action=Deconnecter&view=Login\"' style='display:none;' >Deconnecter</button>
                    </div>
                </header>";
        }
        
        public function menu(){
            echo "<nav>";
            echo "
                <a href='/?view=PageAccueil'> Accueil</a>
                <a href='/?view=Presentation'>Présentation</a>
                <a href='/?view=AllNews'>News</a>
                <a href='/?view=Inscription'>Inscription</a>
                <a href='/?view=Statistiques'>Statistiques</a>
                <a href='/?view=Contact'>Contact</a>
                ";
            echo "</nav>";
        }

        abstract public function contents();
        
        public function foot(){
            echo "<footer>";
            echo "
                <a href='/?view=PageAccueil'> Accueil</a>
                <a href='/?view=Presentation'>Présentation</a>
                <a href='/?view=AllNews'>News</a>
                <a href='/?view=Inscription'>Inscription</a>
                <a href='/?view=Statistiques'>Statistiques</a>
                <a href='/?view=Contact'>Contact</a>
                ";
            echo "</footer>";
        }

        public function view(){
            $this->entete();
            $this->head();
            $this->menu();
            echo '<main>';
            $this->contents();
            echo '</main>';
            $this->foot();
            $this->fermeture();
        }
    }
    
?>