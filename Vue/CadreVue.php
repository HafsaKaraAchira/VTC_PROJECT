<?php

class CadreVue{

    /*private $cadreImage ;
    private $cadreTitle ;
    private $cadreInfo ;
    private $cadreLien ;
    private $id;*/
    
    private function view(){
        echo'
        <detail id="'.$this->id.'" class="cadre">
            <img src="'.$this->cadreImage.'" alt="CadreImage" style="width:100%">
            <div class="info">
                <h5>'.$this->cadreTitle.'</h5>
                <p>'.$this->cadreInfo.'</p>
                <a href="'.$this->cadreLien.'">Lire La suite =></a> 
            </div>
        </detail>
        ';
    }

    public function AnnonceCadre($annonce){
        $this->id = $annonce['AnnonceID'] ;
        $this->cadreImage = (substr_count($annonce['ImageLink'],$_SESSION['imageFolder'])?"/?view=Annonce&action=viewImage&link=".basename($annonce['ImageLink']):$annonce['ImageLink']) ;
        $this->cadreTitle = $annonce['AnnonceTypeTransport'].'<br>'.$annonce['AnnoncePtDepart'].'-'.$annonce['AnnoncePtArrivee'] ;

        $this->cadreInfo = $annonce['AnnonceDescription'] ;
        $this->cadreLien = '/?view=Annonce&action=AnnonceDetails&id='.$this->id;//.urlencode(base64_encode($this->$id)) ;

        return $this->view() ;
    }

    public function NewsCadre($news){
        $this->id = $news['NewsID'] ;
        $this->cadreImage = (substr_count($news['ImageLink'],$_SESSION['imageFolder'])?"/?view=News&action=viewImage&link=".basename($news['ImageLink']):$news['ImageLink']) ;
        $this->cadreTitle = $news['NewsTitle'] ;

        $this->cadreInfo = $news['Newsdescription'] ;
        $this->cadreLien = '/?view=News&action=NewsDetails&id='.$this->id;//.urlencode(base64_encode($this->$id)) ;

        return $this->view() ;
    }
}
    
    /*public function __construct($id,$cadreImage,$cadreTitle,$cadreInfo,$cadreLien){
        $this->id = $id;
        $this->cadreImage = $cadreImage;
        $this->cadreTitle = $cadreTitle;
        $this->cadreInfo = $cadreInfo;
        $this->cadreLien = $cadreLien;
    }

    public function __construct()
    {
        
    }
*/


?>

