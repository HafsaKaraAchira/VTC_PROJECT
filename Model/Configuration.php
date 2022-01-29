<?php
    require_once(__DIR__.'/../Model/Query.php');
    class Configuration{
        /*private static $nbVueAnnonce;
        private static $nbVueNews;
        private static $Selectioncriteria;
        private static $contentFont;
        private static $TitleFont;
        private static $SlideDuration;*/

        public function __get($property){return $this->property;}

        public function __set($name, $value){ /*empty*/}

        public static function getConfiguration(){
                $_SESSION['configuration'] = array() ;
                $query = new Query("SELECT * FROM `configuration` WHERE 1") ;
                $_SESSION['configuration']['general'] = $query->execute_query(PDO::FETCH_ASSOC)[0]  ;

                $query = new Query("SELECT * FROM `poidsintervalles` WHERE 1") ;
                $_SESSION['configuration']['poids'] = $query->execute_query(PDO::FETCH_ASSOC) ;

                $query = new Query("SELECT * FROM `volumeintervalles` WHERE 1") ;
                $_SESSION['configuration']['volume'] = $query->execute_query(PDO::FETCH_ASSOC) ;

                $query = new Query("SELECT * FROM `type_transport` WHERE 1") ;
                $_SESSION['configuration']['typeTransport'] = $query->execute_query(PDO::FETCH_ASSOC) ;

        }

        public function majConfiguration(){
            
        }
    }
?>