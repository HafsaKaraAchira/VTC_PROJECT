<?php 
    require_once('CommonController.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/Model/Annonce.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/Model/Transaction.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/Vue/AnnonceVue.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/Vue/RechercheVue.php');
    class AnnonceController extends CommonController
    {
        //private $id ;
        public function __construct($page){
            parent::__construct($page);
            $this->m = new Annonce() ;
        }

        public function __get($property){
            return $this->property;
        }

        public function __set($property, $value){
            $this->property = $value ;
        }

        public function getGeneralAnnonceInfos($id){
            $annonce=$this->m->AnnonceGeneralInfosQuery($id);
            return $annonce;
        }

        public function getAnnonceDetails($id){
            $detail=$this->m->AnnonceDetailsQuery($id);
            //var_dump($detail);
            return $detail;
        }

        public function getPossibleTranport($annonce,$depart,$arriv){
            $transport=$this->m->AnounceTransportCorrespondanceQuery($annonce,$depart,$arriv);
            return $transport;
        }

        
        public function doSugguestTransaction($transaction){
            $this->m = new Transaction();
            $this->m->addTransactionsQuery($transaction);
            header('Location: /?view=Annonce&action=AnnonceDetails&id='.$transaction['annonce']);
        }

        public function getAnnonce($id){
            return $this->getGeneralAnnonceInfos($id) ;
        }

        public function getRechercheResult($depart,$arriv){
            $result=$this->m->rechercheQuery($depart,$arriv);
            return $result;
        }

        public function doAddAnnonce($annonce){
            $this->m->addAnnonceQuery($annonce) ;
            header('Location:/?') ;
        }

        public function doArchiveAnnonce($annonce){
            $this->m->archiveAnnonceQuery($annonce) ;
            header('Location: /?view=Profile');
        }

        public function doModifierAnnonce($annonce){
            $this->m->UpdateAnnonceQuery($annonce) ;
            header('Location: /?view=Annonce&action=AnnonceDetails&id='.$annonce['id']);
        }

        public function viewPage($id=-1){
            $vue_name = $this->page.'Vue' ;
            require_once($_SERVER['DOCUMENT_ROOT'].'/Vue'.'/'.$vue_name.'.php');
            if($vue_name == 'AnnonceVue'){
                $AnnonceInfos = $this->getAnnonce($id) ;
                if($AnnonceInfos!==null)
                {
                    if(!empty($_SESSION['profile']))
                    {
                        
                        $AnnonceDetails = $this->getAnnonceDetails($id);
                        if($_SESSION['profile']['ProfileID']==$AnnonceInfos['AnnonceUserID'])
                        {

                            $AnnonceTransports = $this->getPossibleTranport($AnnonceInfos['AnnonceID'],
                                                                        $AnnonceInfos['TransportGarantie'],
                                                                        $AnnonceInfos['AnnoncePtDepart'],
                                                                        $AnnonceInfos['AnnoncePtArrivee']
                                                                );
                        }else{
                            $AnnonceTransports = null;
                        }
                        //var_dump($AnnonceTransports) ;
                    }else{
                        $AnnonceDetails = null;
                    }
                    $this->v = new $vue_name($AnnonceInfos,$AnnonceDetails,$AnnonceTransports);
                    $this->v->view();
                }else{
                    echo '
                        <script type="text/javascript">
                            alert("La page que vous avez demandé n\'est pas trouvé !");
                            window.location.href="/?" ;
                        </script>
                    ';
                }
                
            }else{
                $result = $this->getRechercheResult($id['depart'],$id['arriv']) ;
                $this->v = new $vue_name($result);
                $this->v->view();
            }
            
            
        }
    }

?>