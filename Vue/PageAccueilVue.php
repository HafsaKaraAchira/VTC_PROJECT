<?php
    require('CommonVue.php');
    require($_SERVER['DOCUMENT_ROOT'].'/Vue/CadreVue.php');
    class PageAccueilVue extends CommonVue
    {
        private $slides ;
        private $annonces;

        public function __construct($slides,$annonces){
            $this->slides = $slides ;
            $this->annonces = $annonces ;
        }

        public function diaporama(){

            echo "<figure id='diaporama'>
                    <ul id='slide'>";
            foreach ($this->slides as $k => $slide) {
                echo '<li><a href='.$slide['SlideLink'].' target="_blank" rel="noopener noreferrer" ><img src='.(substr_count($slide['ImageLink'],$_SESSION['imageFolder'])?"/?view=PageAccueil&action=viewImage&link=".basename($slide['ImageLink']):$slide['ImageLink']).' alt="diapo_'.$k.'"></a></li>' ;
            }    
            echo "</ul>
                </figure>";
        }

        public function AnnounceSelection(){
            echo "<section id='selectionannonce'>
                    <h2>Selectionnés pour vous</h2>
                " ;   
            foreach ($this->annonces as $annonce) {
                $cadre = new CadreVue() ;
                echo $cadre->AnnonceCadre($annonce) ;
            }
            echo "</section>";
        }

        public function search(){
            echo "
                    <form name='searchbar' action='/?view=Recherche&action=Recherche' method='POST'>
                        <h2>Rechercher des annonces</h2>
                        
                        <label for='depart'>L'emplacement de départ</label>
                        <div>
                            <select name='depart' id='depart' style='width: 100%;font-weight: 700;'>
                                <option value=''>--Choisir un point de départ--</option>
                            </select>
                        </div>
                    
                        <label for='arriv'>L'emplacement de d'arrivée</label>
                        <div>
                            <select name='arriv' id='arriv' style='width: 100%;font-weight: 700;'>
                                <option value=''>--Choisir un point de d'arrivée--</option>
                            </select>
                        </div>
        
                        <input name='rechbtn' type='submit' value='Rechercher'>
                    </form>
                ";
        }

        public function AnnonceForm(){
            echo "
                <form  name='addannonce' action='/?view=Annonce&action=addAnnonce' method='POST'>
                    <h2>Ajouter vos annonces</h2>

                    <label for='depart'>L'emplacement de départ</label>
                    <div>
                        <select name='depart' id='depart' style='width: 100%;font-weight: 700;'>
                            <option value=''>--Choisir un point de départ--</option>
                        </select>
                    </div>
                    
                    <label for='arriv'>L'emplacement de d'arrivée</label>
                    <div>
                        <select name='arriv' id='arriv' style='width: 100%;font-weight: 700;'>
                            <option value=''>--Choisir un point de d'arrivée--</option>
                        </select>
                    </div>

                    <label for='type'>Le type de transport</label>
                    <div>
                        <select name='type' id='type' style='width: 100%;font-weight: 700;'>
                            <option value=''>--Choisir un type de transport--</option>
                            <option value='lettre'>Lettre</option>
                            <option value='colis'>Colis</option>
                            <option value='electromenager'>Electroménager</option>
                            <option value='meuble'>Meuble</option>
                            <option value='demenagement'>déménagement</option>
                        </select>
                    </div>
                    
                    <label for='poids'>Le Poids</label>
                    <div>
                        <select name='poids' id='poids' style='width: 100%;font-weight: 700;'>
                            <option value=''>--Choisir une intervalle de poids--</option>
                        </select>
                    </div>

                    <label for='volume'>Le Volume</label>
                    <div>
                        <select name='volume' id='volume' style='width: 100%;font-weight: 700;'>
                            <option value=''>--Choisir une intervalle de volume--</option>
                        </select>
                    </div>

                    <label for='type'>Le type de transport</label>
                    <div>
                        <select name='moyen' id='moyen' style='width: 100%;font-weight: 700;'>
                            <option value=''>--Choisir un moyen de transport--</option>
                            <option value='moto'>Moto</option>
                            <option value='mini-voiture'>Mini-voiture</option>
                            <option value='voiture'>Voiture</option>
                            <option value='camion'>Camion</option>
                        </select>
                    </div>

                    <input name='insbtn' type='submit' value='Ajouter'>
                </form>";           

        }

        public function contents(){
            $this->search();
            $this->AnnounceSelection();
            if(!empty($_SESSION['profile']) ) $this->AnnonceForm();
            echo "<section style='background-color:inherit;min-height:max-content;'>
                        <button><a style='color:aliceblue;' href='/?view=Presentation'>Comment ca fonctionne</a></button>
                </section>";

                echo "
                <script src='https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js'></script>
                <script type ='text/javascript'>
                    let jq2 = jQuery.noConflict(true);
                    jq2(document).ready(function() {
                        console.log('jquery');
                        populate_trajet();
                        populate_intervalles();
    
                        jq2('input[type=range]').bind('input',function(){
                            jq2(this).next('output').val( jq2(this).val() ) ;
                        });
    
                        ".(!empty($_SESSION['profile'])
                        ?"
                            jq2('form[name=addannonce]').bind('submit',function(){
                                jq2(this).append('<input type=\"hidden\" name=\"profile\" value=\"".$_SESSION['profile']['ProfileID']."\">');
                            });

                            jq2('form[name=addannonce]').validate({
                                rules: {
                                    'depart': {
                                        required:true
                                    },
                                    'arriv':{
                                        required:true
                                    },
                                    'type': {
                                        required: true
                                    },
                                    'poids': {
                                        required: true
                                    },
                                    'volume': {
                                        required: true
                                    },
                                    'moyen': {
                                        required: true
                                    },
                                },//rules
                                messages: {
                                    'depart': {
                                        required:'Ce champ est obligatoire'
                                    },
                                    'arriv':{
                                        required:'Ce champ est obligatoire'
                                    },
                                    'type': {
                                      required: 'Ce champ est obligatoire'
                                    },
                                    'poids':{
                                       required:'Ce champ est obligatoire'
                                    },
                                    'volume':{
                                        required:'Ce champ est obligatoire'
                                    },
                                    'moyen':{
                                        required:'Ce champ est obligatoire'
                                    } 
                                },//messages
                             });//validate
                        "
                        :""
                        )."
    
                        //jq2.validator.setDefaults({
                        //    debug: true,
                        //    success: 'valid'
                        //});
    
                        jq2('form[name=searchbar]').validate({
                            rules: {
                                'depart': {
                                    required:true
                                },
                                'arriv':{
                                    required:true
                                }
                            },//rules
                            messages: {
                                'depart': {
                                    required:'Ce champ est obligatoire'
                                },
                                'arriv':{
                                    required:'Ce champ est obligatoire'
                                }
                            },//messages
                         });//validate
    
                    });//ready
    
                    function populate_trajet(){
                        wilayas=['ADRAR','CHLEF','LAGHOUAT','OUM BOUAGHI','BATNA','BEJAIA','BISKRA','BECHAR','BLIDA','BOUIRA','TAMANRASSET','TEBESSA','TLEMCEN','TIARET','TIZI-OUZOU','ALGER','DJELFA','JIJEL','SETIF','SAIDA','SKIKDA','SIDIBELABBES','ANNABA','GUELMA','CONSTANTINE','MEDEA','MOSTAGANEM','MSILA','MASCARA','OUARGLA','ORAN','EL BAYDH','ILLIZI','BORDJ','BOUMERDES','EL TAREF','TINDOUF','TISSEMSILT','EL OUED','KHENCHLA','SOUK AHRASS','TIPAZA','MILA','AÏN DEFLA','NÂAMA','AÏN TEMOUCHENT','GHARDAÏA','RELIZANE'];
                        
                        jq2.each(wilayas, function (i, w) {
                            jq2('select[name=arriv],select[name=depart]').append(jq2('<option/>', { 
                                value: w,
                                text : w 
                            }));
                        });
                    }
    
                    function populate_intervalles(){
                        poids=".json_encode($_SESSION['configuration']['poids'])."
                        jq2.each(poids, function (i, p) {
                            jq2('select[name=poids]').append(jq2('<option/>', { 
                                value: p.PoidsIntervalleID,
                                text : (p.IntervalleStart>=1?parseInt(p.IntervalleStart)+' kg':p.IntervalleStart*1000 + ' g')+' -- '+(p.IntervalleEnd>1?parseInt(p.IntervalleEnd)+' kg':p.IntervalleEnd*1000+' g')
                            }));
                        });
    
                        volume=".json_encode($_SESSION['configuration']['volume'])."
                        jq2.each(volume, function (i, v) {
                            jq2('select[name=volume]').append(jq2('<option/>', { 
                                value: v.VolumeIntervalleID,
                                text : (v.IntervalleStart>=1?parseInt(v.IntervalleStart)+' m3':v.IntervalleStart*1000 + ' cm3')+' -- '+(v.IntervalleEnd>1?parseInt(v.IntervalleEnd)+' m3':v.IntervalleEnd*1000+' cm3')
                            }));
                        });
                    }
    
                    </script>
                " ;                                    
        }

        public function view(){
            $this->entete();
            $this->head();
            $this->diaporama();
            $this->menu();
            echo '<main>';
            $this->contents();
            echo '</main>';
            $this->foot();
            $this->fermeture();
        }
    }
?>

