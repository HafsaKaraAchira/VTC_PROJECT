<?php

class TableVue{
    
    private function view(){
        echo '
            <div id="'.$this->tableID.'" class="tableVue">';
        foreach ($this->tableHeader as $key => $header) {
            echo '<div><b>'.$header.'</b></div>';
        }
        //echo '<div class="line"></div>';
        //rows
        echo $this->rows ;
        //fermeture du tableau
        echo '</div>' ;
        
    }

    public function TransactionTable($transactions){
        $this->tableID = 'transaction_table';
        $this->tableHeader = ['N','Client','Annonce','Transporteur','certifié','Tarif','Tax','Status','Note'] ;
        $this->rowid = 'TransactionID' ;
        $this->rows='';

        foreach ($transactions as $num => $transaction) {
            $transactionClient = ($_SESSION['profile']['ProfileID'] == $transaction['ClientID']?true:false);
            $clientcol = ($transactionClient?'<b>'.'Vous'.'</b>':'<b>'.$transaction['ClientNom'].' '.$transaction['ClientPrenom'].'</b>'.'<a class="signal" href="" id="'.$transaction['AnnonceID'].'&user='.$transaction['ClientID'].'">Signaler</a>') ;
            
            $transporteurNote = '<div class="Stars" style="--rating:'.$transaction['TransporteurNote'].';" aria-label="Note du transporteur : '.$transaction['TransporteurNote'].'/5"></div>' ;
            $transpcol = ($transactionClient
                            ?'<b>'.$transaction['TransporteurNom'].' '.$transaction['TransporteurPrenom'].'</b>'.' '.'<a class="signal" href="" id="'.$transaction['AnnonceID'].'&user='.$transaction['ClientID'].'">Signaler</a>'.$transporteurNote
                            :'<b>Vous</b>'
                        ) ;
            $note_possible = $transactionClient && $transaction['TransactionStatus'] == 'effectuée' && $transaction['TransactionNote'] == 0 ;
            $TransactionNote = '
                                <span class="transac_note" data-id="'.$transaction['TransactionID'].'" data-note="'.$transaction['TransactionNote'].'">
                                    <a class="star" href="javascript:void(0);" '.($note_possible?'onclick="note(this);"':'').' data-note="5"><span>★</span></a>
                                    <a class="star" href="javascript:void(0);" '.($note_possible?'onclick="note(this);"':'').' data-note="4"><span>★</span></a>
                                    <a class="star" href="javascript:void(0);" '.($note_possible?'onclick="note(this);"':'').' data-note="3"><span>★</span></a>
                                    <a class="star" href="javascript:void(0);" '.($note_possible?'onclick="note(this);"':'').' data-note="2"><span>★</span></a>
                                    <a class="star" href="javascript:void(0);" '.($note_possible?'onclick="note(this);"':'').' data-note="1"><span>★</span></a>
                                </span>';
            
            $this->rows .= '<div class="line"></div>

                        <div class="rownum" scope="row">
                            <b>'.($num+1).'</b>
                        </div>

                        <div class="client">
                            '.$clientcol.'
                        </div>
                        
                        <div class="annonce">
                            <a href="/?view=Annonce&action=AnnonceDetails&id='.$transaction['AnnonceID'].'">
                                <i class="fas fa-info-circle fa-2x"></i>
                            Détails</a>
                        </div>
                        
                        <div class="transporteur">
                            '.$transpcol.'
                        </div>
                        
                        <div class="certificat">
                            '.($transaction['certificat']!=null?'<b>Oui</b>':'Non').'
                        </div>
                        
                        <div class="tarif">
                            <b>'.$transaction['AnnonceTarif'].'DZ</b>
                        </div>
                        
                        <div class="tax">
                            <b>'.$transaction['AnnonceTax'].'%</b>
                        </div>
                        
                        <div class="status">
                            <b>'.$transaction['TransactionStatus'].'</b>'.($transaction['TransactionStatus']=='pas encore confirmée'?'
                            <span>
                                <a style="width:fit-content;" href="/?view=Profile&action=Confirm&id='.$transaction[$this->rowid].'&response=oui">
                                    <i class="fas fa-check-circle fa-2x"></i>
                                </a>
                                <a style="width:fit-content;" href="/?view=Profile&action=Confirm&id='.$transaction[$this->rowid].'&response=non">
                                    <i class="fas fa-times-circle fa-2x"></i>
                                </a>':'').'
                            </span>
                        </div>
                        
                        <div class="note">
                            '.$TransactionNote.'
                            <b>'.$transaction['TransactionNote'].'</b>
                        </div>
                        
                        ';
                //.($transaction['TransactionNote']==0 && $transactionClient?'':'').'</div>
                
        }

        return $this->view() ;
    }//

    public function TrajetTable($trajets){
        $this->tableID = 'trajet_table';
        $this->tableHeader = ['N','Point de Départ','Point d\'arrivée','<i class="fas fa-cog" style="font-size: larger;"></i>'] ;
        $this->rowid = 'TrajetID' ;
        $this->rows='';

        foreach ($trajets as $num => $trajet) {
            $this->rows .= //'<div id="'.$transaction[$this->rowid].'">
                        '
                        <div class="line"></div>
                        <div class="rownum" data-id="'.$trajet[$this->rowid].'" >'.($num+1).'</div>
                        <div class="depart" >
                            <select name="depart_'.$trajet[$this->rowid].'" disabled>
                                <option value="'.$trajet['PtDepart'].'" selected style="text-align: center;">'.$trajet['PtDepart'].'</option>
                            </select>
                        </div>
                        <div class="arriv" >
                            <select name="arriv_'.$trajet[$this->rowid].'" disabled>
                                <option value="'.$trajet['PtArrivee'].'" selected style="text-align: center;">'.$trajet['PtArrivee'].'</option>
                            </select>
                        </div>
                        <span class="trajet_edit" >
                            <!--<button title="modifier le trajet" class="trajet_modifbtn" ><i class="fas fa-pen-square"></i></button>-->
                            <button title="supprimer le trajet" class="trajet_removebtn" ><i class="fas fa-trash-alt"></i></button>
                        </span>
                        ';
        
        }
        $this->rows .= '
            <div class="line"></div>
            <div class="add_trajet_row"></div>
            <div>
            <select id="new_depart" style="width: 100%;font-weight: 700;">
                <option value="">--point de départ--</option>
            </select>
            </div>

            <div>
            <select id="new_arriv" style="width: 100%;font-weight: 700;">
                <option value="">--point de d\'arrivée--</option>
            </select>
            </div>
    
            <span><button name="add_trajetbtn" id="add_trajetbtn" disabled><i class="fas fa-plus"></i></button></span>
        ' ;

        return $this->view() ;
    }//TrajetTable

    public function TransporteurTable($transporteurs){
        $this->tableID = 'transporteur_table';
        $this->tableHeader = ['N','Nom','Prénom','Certifié','Note','Contacter'] ;
        $this->rowid = 'TransporteurID' ;
        $this->rows='';

        foreach ($transporteurs as $num => $transporteur) {
            
            $transporteurNote = '<div class="Stars" style="--rating:'.$transporteur['note'].';" aria-label="Note du transporteur : '.$transporteur['note'].'/5"></div>' ;
            $contact_possible = $transporteur['contacteDeja']==null ;
            //var_dump($transporteur) ;
            //var_dump($contact_possible) ;
            $this->rows .= 
                        '
                        <div class="line"></div>
                        <div class="rownum" id="transporteur['.$transporteur[$this->rowid].']">'.($num+1).'</div>
                        <div class="nom">'.$transporteur['nom'].'</div>
                        <div class="prenom">'.$transporteur['prenom'].'</div>
                        <div class="certificat">
                            '.($transporteur['certificat']!=null?'<b>Oui</b>':'Non').'
                        </div>
                        <div class="note">
                            '.$transporteurNote.'
                            <b>'.$transporteur['note'].'</b>
                        </div>
                        <div class="do_transaction">
                                '.($contact_possible
                                ?'<a href="/?view=Annonce&action=PostulerTransaction&transporteur='.$transporteur['TransporteurID'].'">
                                    <i class="fas fa-phone fa-2x"></i>
                                    Contacter
                                </a>'
                                :'<a href="javascript:void(0);" class="fixed_link" disabled>
                                    <i class="fas fa-clock fa-2x"></i>
                                    En attente
                                </a>').'
                            
                        </div>
                        ';
                        //&annonce=$annonce['AnnonceID'] &user='.$_SESSION['profile']['ProfileID'].
        
        }

        return $this->view() ;
    }//TrajetTable


}//TableVue

?>