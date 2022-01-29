<?php
    require_once(__DIR__.'/../Model/Query.php');
    class Certificat{

        public function __get($property){return $this->property;}

        public function __set($property, $value){ /*empty*/}

        public static function addCertificatQuery($transporteur){
            $query = new Query("INSERT INTO 
                                    `certificat`(`Transporteur`)
                                    VALUES(:transporteur)
                                ",
                                array(
                                    array(':transporteur',$transporteur,PDO::PARAM_INT)
                                )
                            ) ;
            $certificat = $query->execute_query(PDO::FETCH_ASSOC) ;
            return $certificat  ;
        }

        public static function updateCertificatQuery(){

            return $certificat  ;
        }

        public static function certificatDocumentsQuery(){

            return $certificat  ;
        }

        public static function certificatJustificatifQuery(){

            return $certificat  ;
        }

        public static function validerQuery(){

            return $certificat  ;
        }

        public static function certificatQuery($transporteur){
            $query = new Query("SELECT * 
                                FROM `certificat`
                                WHERE Transporteur=:transporteur
                                ",
                                array(
                                    array(':transporteur',$transporteur,PDO::PARAM_INT)
                                )
                            ) ;
            
            $certificat = $query->execute_query(PDO::FETCH_ASSOC)[0]??null ;
            return $certificat  ;
        }
    }
?>