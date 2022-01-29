<?php
    require_once('CommonVue.php');
    require($_SERVER['DOCUMENT_ROOT'].'/Vue/TableVue.php');
    class AnnonceVue extends CommonVue
    {
        private $annonce ;

        public function __construct($annonce,$AnnonceDetails,$AnnonceTransports){
            $this->annonce = $annonce ;
            $this->AnnonceDetails =$AnnonceDetails;
            $this->AnnonceTransports =$AnnonceTransports; 
        }

        public function AnnonceGenerales(){

            //".$this->annonce['texts']['annonceID']."'>
            //$annonce['AnnonceID'] ;

            $p =$_SESSION['configuration']['poids'][(int)$this->annonce['AnnoncePoids']-1] ;
            $poids = ((int)$p['IntervalleStart']>=1?(int)$p['IntervalleStart'].' kg':((float)$p['IntervalleStart']*1000).' g')
                    .' -- '
                    .((int)$p['IntervalleEnd']>1?(int)$p['IntervalleEnd'].' kg':((int)$p['IntervalleEnd']*1000).' g') 
                ;

            $v =$_SESSION['configuration']['volume'][(int)$this->annonce['AnnonceVolume']-1] ;
            $volume = ((int)$v['IntervalleStart']>=1?((int)$v['IntervalleStart']).' m3':((float)$v['IntervalleStart']*1000 ). ' cm3')
                        .' -- '
                        .((int)$v['IntervalleEnd']>=1?((int)$v['IntervalleEnd']).' m3':((float)$v['IntervalleEnd']*1000).' cm3') ;
            //var_dump($v) ;
            echo "<form  name='annonceinfos' id='modifierannonce' action='/?view=Annonce&action=ModifierAnnonce&id=".$this->annonce['AnnonceID']."' method='POST'>
                    <h2>Annonce Infos</h2>

                    <label for='depart'>L'emplacement de départ</label>
                    <div>
                        <select name='depart' id='depart' style='width: 100%;font-weight: 700;'>
                            <option value='".$this->annonce['AnnoncePtDepart']."'>".$this->annonce['AnnoncePtDepart']."</option>
                        </select>
                    </div>
                    
                    <label for='arriv'>L'emplacement de d'arrivée</label>
                    <div>
                        <select name='arriv' id='arriv' style='width: 100%;font-weight: 700;'>
                            <option value='".$this->annonce['AnnoncePtArrivee']."'>".$this->annonce['AnnoncePtArrivee']."</option>
                        </select>
                    </div>

                    <label for='type'>Le type de transport</label>
                    <div>
                        <select name='type' id='type' style='width: 100%;font-weight: 700;'>
                            <option value='".$this->annonce['AnnonceTypeTransport']."'>".$this->annonce['AnnonceTypeTransport']."</option>
                        </select>
                    </div>
                    
                    <label for='poids'>Le Poids</label>
                    <div>
                        <select name='poids' id='poids' style='width: 100%;font-weight: 700;'>
                            <option value='".$this->annonce['AnnoncePoids']."' selected>".$poids."</option>
                        </select>
                    </div>

                    <label for='volume'>Le Volume</label>
                    <div>
                        <select name='volume' id='volume' style='width: 100%;font-weight: 700;'>
                            <option value='".$this->annonce['AnnonceVolume']."'>".$volume."</option>
                        </select>
                    </div>

                    <label for='description'>Description</label>
                    <div>
                    <textarea name='description' id='description' style='width: 100%;font-weight: 700;resize:none;' cols='60' rows='5'>".trim($this->annonce['AnnonceDescription'])."</textarea>
                    </div>";

                if($this->AnnonceDetails!==null ){
                    echo "<label for='moyen'>Le Moyen de transport</label>
                            <div>
                                <select name='moyen' id='moyen' style='width: 100%;font-weight: 700;'>
                                    <option value='".$this->AnnonceDetails['AnnonceMoyenTransport']."'>".$this->AnnonceDetails['AnnonceMoyenTransport']."</option>                                
                                </select>
                            </div>
                    ";
                }

                echo "<label for='image'>image</label>
                    <div>
                    <img style=\"width:75%;\" src=\"".(substr_count($this->annonce['ImageLink'],$_SESSION['imageFolder'])?"/?view=Annonce&action=viewImage&link=".basename($this->annonce['ImageLink']):$this->annonce['ImageLink'])."\" alt=\"annonce image\">
                    </div>
                ";
                echo "</form>";
        }
        
        public function AnnonceDetails(){

            echo "<section class='infos'>
                    <h2>Annonce Details</h2>
                    " ;
            echo "
                    <h5> Tarif : ".$this->AnnonceDetails['AnnonceTarif']." DZ</h5>
                    <h5>Pourcentage : ".$this->AnnonceDetails['AnnonceTax']."%</h5>
                    <h5>Profile : ".$this->AnnonceDetails['nom'].' '.$this->AnnonceDetails['prenom']."</h5>
                    <h5>Tel : ".$this->AnnonceDetails['tel']."</h5>
                    ";
            //var_dump($this->AnnonceDetails);
            if(!empty($_SESSION['profile'])){   
                if($_SESSION['profile']['ProfileID']==$this->annonce['AnnonceUserID'])
                {   
                    echo "<h5> État: : ".($this->AnnonceDetails['AnnonceValidee']?"Pas encore validée":"validée")."</h5>";
                    if ($this->AnnonceDetails['transactionValideDeja']){
                        echo "
                        <button  id='modifannonce'>
                            Modifier L'annonce
                            <i class='fas fa-edit fa-2x'></i>
                        </button>";

                        echo "<button 
                                onclick=\"window.location.href=
                                    '/?view=Annonce&action=SupprimerAnnonce&id=".$this->annonce['AnnonceID']."';\" >
                                Supprimer L'annonce
                                <i class='fas fa-trash-alt fa-2x'></i>
                        </button>";
                    }

                    echo "
                <script type ='text/javascript'>
                    let jq1 = jQuery.noConflict();
                    jq1(document).ready(function() {
                        jq1('button#modifannonce').bind('click',modif);
                        jq1('form').find('input,textarea').attr('readonly',true);
                        jq1('form').find('select').attr('disabled',true);
                    });//ready
                    
                    function modif(event){
                        console.log('modif');
                        jq1(this).text(' Confirmer La modification ') ;
                        jq1(this).append('<i class=\"fa fa-check fa-2x\"></i>');
                        jq1('form').find('input,textarea').attr('readonly',false);
                        jq1('form').find('select').attr('disabled',false);
                        //jq1('button#modifannonce').attr('type','submit') ;
                        //jq1('button#modifannonce').attr('form','annonceinfos') ;
                        jq1('button#modifannonce').unbind('click');
                        jq1('button#modifannonce').bind('click',valid_modif);
                        populate_intervalles();
                        populate_trajet();
                        location.href = '#modifierannonce';
                        return false ;
                    }//modif

                    function valid_modif(event){
                        console.log('valid_modif');
                        jq1('form').submit();
                        //location.href = '';
                    }//valid_modif

                    function populate_trajet(){
                        wilayas=['ADRAR','CHLEF','LAGHOUAT','OUM BOUAGHI','BATNA','BEJAIA','BISKRA','BECHAR','BLIDA','BOUIRA','TAMANRASSET','TEBESSA','TLEMCEN','TIARET','TIZI-OUZOU','ALGER','DJELFA','JIJEL','SETIF','SAIDA','SKIKDA','SIDIBELABBES','ANNABA','GUELMA','CONSTANTINE','MEDEA','MOSTAGANEM','MSILA','MASCARA','OUARGLA','ORAN','EL BAYDH','ILLIZI','BORDJ','BOUMERDES','EL TAREF','TINDOUF','TISSEMSILT','EL OUED','KHENCHLA','SOUK AHRASS','TIPAZA','MILA','AÏN DEFLA','NÂAMA','AÏN TEMOUCHENT','GHARDAÏA','RELIZANE'];
                        jq1('select[name=arriv],select[name=depart]').empty();
                        jq1.each(wilayas, function (i, w) {
                            jq1('select[name=arriv],select[name=depart]').append(jq1('<option/>', { 
                                value: w,
                                text : w 
                            }));
                        });

                        jq1('select[name=depart] option[value=".$this->annonce['AnnoncePtDepart']."]').attr('selected',true) ;
                        jq1('select[name=arriv] option[value=".$this->annonce['AnnoncePtArrivee']."]').attr('selected',true) ;
                    }

                    function populate_intervalles(){
                        poids=".json_encode($_SESSION['configuration']['poids'])."
                        jq1('select[name=poids]').empty() ;
                        jq1.each(poids, function (i, p) {
                            jq1('select[name=poids]').append(jq1('<option/>', { 
                                value: p.PoidsIntervalleID,
                                text : (p.IntervalleStart>=1?parseInt(p.IntervalleStart)+' kg':p.IntervalleStart*1000 + ' g')+' -- '+(p.IntervalleEnd>1?parseInt(p.IntervalleEnd)+' kg':p.IntervalleEnd*1000+' g')
                            }));
                        });
                        jq1('select[name=poids] option[value=".$this->annonce['AnnoncePoids']."]').attr('selected',true) ;
    
                        volume=".json_encode($_SESSION['configuration']['volume'])."
                        jq1('select[name=volume]').empty();
                        jq1.each(volume, function (i, v) {
                            jq1('select[name=volume]').append(jq1('<option/>', { 
                                value: v.VolumeIntervalleID,
                                text : (v.IntervalleStart>=1?parseInt(v.IntervalleStart)+' m3':v.IntervalleStart*1000 + ' cm3')+' -- '+(v.IntervalleEnd>1?parseInt(v.IntervalleEnd)+' m3':v.IntervalleEnd*1000+' cm3')
                            }));
                        });
                        jq1('select[name=volume] option[value=".$this->annonce['AnnonceVolume']."]').attr('selected',true) ;

                        let types = '<option value=\"lettre\">Lettre</option>'
                                    +'<option value=\"colis\">Colis</option>'
                                    +'<option value=\"electromenager\">Electroménager</option>'
                                    +'<option value=\"meuble\">Meuble</option>'
                                    +'<option value=\"demenagement\">déménagement</option>' ;

                        jq1('select[name=type]').empty();            
                        jq1('select[name=type]').append(types);
                        jq1('select[name=type] option[value=".$this->annonce['AnnonceTypeTransport']."]').attr('selected',true) ;                                            
                        
                        jq1('select[name=moyen]').empty();  
                        jq1('select[name=moyen]').append('<option value=\"moto\">Moto</option>'
                                                        +'<option value=\"mini-voiture\">Mini-voiture</option>'
                                                        +'<option value=\"voiture\">Voiture</option>'
                                                        +'<option value=\"camion\">Camion</option>');
                        jq1('select[name=moyen] option[value=".$this->AnnonceDetails['AnnonceMoyenTransport']."]').attr('selected',true) ;                                                
                    }

                    </script>
                    " ;
                }
                
                if($_SESSION['profile']['profileType']=='transporteur' && $this->annonce['AnnonceUserID']!= $_SESSION['profile']['ProfileID'] ){
                    if($this->AnnonceDetails['contacteDeja']!=null){
                        echo "<button 
                                onclick='return false ;' class='fixed_link' disabled>
                                <i class='fas fa-clock fa-2x'></i>
                                En attente d'une réponse
                            </button>";
                    }else{
                        echo "<button 
                                onclick=\"window.location.href=
                                    '/?view=Annonce&action=ProposerTransaction&transporteur=".$_SESSION['profile']['TransporteurID']."&annonce=".$this->annonce['AnnonceID']."';\" >
                                Postuler    
                            </button>";
                    }
                    
                }
            }

            echo "</section>";
        }
        
        public function AnnonceTransports(){
            echo "<section class='infos'>
                    <h2>Annonce Transporteurs Possibles</h2>";
            
            $transportTable = new TableVue() ;
            $transportTable->TransporteurTable($this->AnnonceTransports) ;
            echo "</section>";

            echo "
                <script type ='text/javascript'>
                    let jq2 = jQuery.noConflict();
                    jq2(document).ready(function() {
                        link_updates();
                    });//ready
                
                    function link_updates() {
                        //console.log(jq2('div.do_transaction a'));
                       jq2.each(jq2('div.do_transaction a'),function(i,link){
                            let v = jq2(link).attr('href') ;
                            jq2(link).attr('href',v+'&annonce='+".$this->annonce['AnnonceID'].") ;
                       });
                    }
                </script>
            ";
        }

        public function contents(){
            $this->AnnonceGenerales();
            //var_dump($this->AnnonceDetails) ;
            if($this->AnnonceDetails!==null ){
                $this->AnnonceDetails();
            }
            //var_dump($this->AnnonceTransports) ;
            if($this->AnnonceTransports!==null){
                $this->AnnonceTransports();
            }

            echo "
            
            ";
        }
    }
?>