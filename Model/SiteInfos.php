<?php 
    require_once(__DIR__.'/../Model/Query.php');
    class SiteInfos
    {
        public function ContactQuery(){
            $query = new Query("SELECT * FROM `contact` WHERE 1") ;
            return $query->execute_query(PDO::FETCH_ASSOC) ;
        }

        public function PresentationQuery(){
            $query = new Query("SELECT ObjectifText,PresentationVideoLink,FonctionnmentText,image.ImageLink FROM `presentation` join `image` On presentation.PresentationImage = image.ImageID") ;
            return $query->execute_query(PDO::FETCH_ASSOC) ;
        }
    }
?>