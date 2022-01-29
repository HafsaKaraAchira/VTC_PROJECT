<?php 
    require_once('CommonController.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/Vue/CommonVue.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/Model/Profile.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/Model/Annonce.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/Model/Transaction.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/Model/Transporteur.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/Model/Certificat.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/Model/Signalement.php');
    class ProfileController extends CommonController
    {
        private $previousPage ;
        private $profile ;
        public function __construct($page){
            parent::__construct($page);
        }

        /********************* profile actions *********************/

        public function doLogin($login){
            Profile::getConnection($login['email'],$login['pwd']);
            if(isset($_SESSION['profile'])){
                header('Location:/?');
            }else{
                $_SESSION['warn']='InfoIncorrectes';
                header('Location:/?view=Login');
            }
            //var_dump($_SESSION['profile']) ;
        }

        public function doInscription($inscription){
            //var_dump($inscription);
            $this->m = new Profile();
            $id = -1 ;
            $id=$this->m->addProfileQuery($inscription['profile']) ;
            //var_dump($id) ;
            if($id > 0)
            {
                if(!empty($inscription['trajet']))
                {
                    $this->m = new Transporteur();
                    $transporteur = $this->m->addTransporteurQuery($id,$inscription['trajet']) ;
                
                    if(!empty($inscription['certif'])){
                        $this->m = new Certificat();
                        $id=$this->m->addCertificatQuery($transporteur) ;
                    }
                }
                header('Location:/?') ;
            }
        }

        public function validerInfos($email){
            $this->m = new Profile();
            $unique = $this->m->uniqueProfileQuery($email) ;
            //var_dump($unique) ;
            echo $unique ;
        }

        public function doUpdateProfile($newinfos){
            if($this->m==null)
            {$this->m = new Profile();}
            $row=$this->m->UpdateProfileQuery($newinfos) ;
            //var_dump($row);
            if($row>0)
            {
                header('Location:/?view=Profile');
            }else{
                echo '
                    <script type="text/javascript">
                        alert("Ancien mot de passe invalide");
                    </script>
                ';
            }
            header('Location:/?view=Profile');
        }

        public function doAddTrajet($newtrajet){
            $this->m = new Transporteur();
            //var_dump($newtrajet) ;
            //var_export( json_decode($newtrajet['trajet'],true) );
            $newtrajet = $this->m->addTrajetSQuery($newtrajet['transporteur'],json_decode($newtrajet['trajet'],true)) ;
            echo $newtrajet ;
            //header('Location:/?view=Profile');
        }

        public function doRemoveTrajet($trajet){
            $this->m = new Transporteur();
            $this->m->removeTrajetsQuery($trajet) ;
            //header('Location:/?view=Profile');
        }

        public function doDeconnecter(){
            Profile::DeconnectProfile();
            header('Location:/?');
        }

        /********************* transcations actions *********************/

        public function doConfirmTransaction($transaction){
            $this->m = new Transaction();
            if($transaction['response'] == 'non'){
                $transaction['response'] = 'refusée' ;
            }else{
                $transaction['response'] = 'confirmée' ;
            }
            //var_dump($transaction['response']) ;
            $this->m->confirmTransactionQuery($transaction);
            header('Location:/?view=Profile');
        }

        public function doNoteTransaction($transaction){
            $this->m = new Transaction();
            $TransporteurNote = $this->m->noteTransactionQuery($transaction) ;
            echo $TransporteurNote ;
        }

        public function doSignalProfile($signal){
            $this->m = new Signalement();
            $this->m->addSignalQuery($signal);
        }

        /********************** get profile infos **********************/

        public function getGeneralInfos(){ //nom,prenom....
            $this->m = new Profile();
            $this->profile = $this->m->ProfileInfosQuery();
            return $this->profile ;
        }


        public function getHistorique(){ // annonces + transcations
            $historique = array() ;
            $this->m = new Annonce();
            $historique['annonce'] = $this->m->ProfileAnnoncesQuery();
            $this->m = new Transaction();
            $historique['transaction'] = $this->m->ProfileTransactionsQuery();
            return $historique ;
        }

        /******************** get transporteur infos *****************/

        public function getTransporteurInfos($id){ //gains,note...
            $this->m = new Transporteur();
            return $this->m->transporteurQuery($id);
        }

        public function getTransporteurTrajets($transporteur){ //transporteur trajets
            $this->m = new Transporteur();
            return $this->m->trajetsQuery($transporteur);
        }
        
        public function getCertificatInfos($transporteur){ // certificat
            $this->m = new Certificat();
            return $this->m->certificatQuery($transporteur);
        }

        /*************************************************************/
        /*************************************************************/
        
        public function viewPage() //profile,inscription,login
        {
            //var_dump($_SESSION['profile']);
            $vue_name = $this->page.'Vue' ;
            require_once($_SERVER['DOCUMENT_ROOT'].'/Vue'.'/'.$vue_name.'.php');
            if(!empty($_SESSION['profile'])){
                $generalInfos=$this->getGeneralInfos() ;
                $historique=$this->getHistorique() ;
                $transporteurInfos = null ;
                //var_dump($_SESSION['profile']['profileType']) ;
                if($_SESSION['profile']['profileType'] == 'transporteur'){
                    $transporteurInfos = array() ;
                    $transporteurInfos['transporteur'] = $this->getTransporteurInfos($_SESSION['profile']['ProfileID']) ;
                    $transporteurInfos['trajet'] = $this->getTransporteurTrajets($transporteurInfos['transporteur']['TransporteurID']) ;
                    if($transporteurInfos['transporteur']['certificat'] != null){
                        $transporteurInfos['certificat'] = $this->getCertificatInfos($transporteurInfos['transporteur']['TransporteurID']) ;
                    }
                }
                $this->v = new $vue_name($generalInfos,$historique,$transporteurInfos);
            }else{
                $this->v = new $vue_name();
            }
            $this->v->view();
        }
    }

?>