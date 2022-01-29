<?php
    require_once('CommonVue.php');
    require($_SERVER['DOCUMENT_ROOT'].'/Vue/CadreVue.php');
    require($_SERVER['DOCUMENT_ROOT'].'/Vue/TableVue.php');
    class ProfileVue extends CommonVue
    {
        private $generalInfos;
        private $historique ;

        public function __construct($generalInfos,$historique,$transporteurInfos=null){
            $this->generalInfos=$generalInfos ;
            $this->historique=$historique ;
            $this->transporteurInfos=$transporteurInfos ;
        }

        public function viewProfileInfos(){
            //var_dump($this->generalInfos);
            echo "
                <form name='profile' action='/?view=Profile&action=Modif' method='POST'>
                        <h2>Profile</h2>
                        
                        <label for='nom'>Nom</label>
                        <input type='nom' id='nom' name='nom' value='".$this->generalInfos['nom']."' required readonly>

                        <label for='prenom'>Prénom</label>
                        <input type='prenom' id='prenom' name='prenom' value='".$this->generalInfos['prenom']."' required readonly>

                        <label for='email'>Email</label>
                        <input type='email' id='email' name='email' value='".$this->generalInfos['email']."' required readonly>

                        <label for='tel'>Téléphone</label>
                        <input type='number' id='tel' name='tel' value='".$this->generalInfos['tel']."' required readonly>

                        <label for='adresse'>Adresse</label>
                        <input type='text' id='adresse' name='adresse' value='".$this->generalInfos['adresse']."' required readonly>

                        <label for='pwdanc'>L'ancien mot de passe</label>
                        <input type='password' autocomplete id='pwdanc' name='pwdanc' value='........' required readonly>

                        <label for='pwd' style='display:none'>Le nouveau Mot de passe</label>
                        <input type='password' autocomplete id='pwd' name='pwd' style='display:none' required readonly>

                        <label for='confirmpwd' style='display:none'>Confirmer le nouveau Mot de passe</label>
                        <input type='password' autocomplete id='confirmpwd' name='confirmpwd' style='display:none' required readonly >
        
                        <input type='button' id='modifbtn' name='modifbtn' value='Modifier le Profile' required readonly >
                </form>";
                
            echo "
                <script type ='text/javascript'>
                    let jq1 = jQuery.noConflict();
                    jq1(document).ready(function() {
                        jq1('form[name=profile] input#modifbtn').bind('click',modif);
                        jq1('form[name=profile] input[name=pwd').bind('input',onChange);
                        jq1('form[name=profile] input[name=confirmpwd').bind('input',onChange);
                    });//ready
                    
                    function modif(event){
                        //console.log();
                        if(jq1(this).attr('type') == 'button'){
                            jq1(this).val('Confirmer') ;
                            jq1('input').attr('readonly',false);
                            jq1('input[type=password]').val('');
                            jq1('input , label').css('display','block');
                            jq1(this).attr('type','submit') ;
                        }
                    }//modif

                    

                    function onChange() {
                        console.log('change');
                        let password = jq1('input[name=pwd]');
                        let confirm = jq1('input[name=confirmpwd]');
                        console.log(confirm);
                        if (confirm.val() === password.val()) {
                            password.css('border-color','lightgreen');
                            confirm.css('border-color','lightgreen');
                            password.css('outline','lightgreen');
                            confirm.css('outline','lightgreen');
                            jq1('form[name=profile] input#modifbtn').attr('disabled',false);
                        } 
                        else {
                            password.css('border-color','indianred');
                            confirm.css('border-color','indianred');
                            password.css('outline','indianred');
                            confirm.css('outline','indianred');
                            jq1('form[name=profile] input#modifbtn').attr('disabled',true);
                        }
                    }

                </script>
            ";
        }

        public function viewProfileAnnonces(){
            echo "<section id='profileannonce'>
                    <h2>Vos Annonces</h2>
                " ;   
            foreach ($this->historique['annonce'] as $annonce) {
                //var_dump($annonce);
                $cadre = new CadreVue();
                echo $cadre->AnnonceCadre($annonce) ;
            }
            echo "</section>";
        }

        public function viewProfileTranscations(){
            echo "<section id='transaction'>
                    <h2>Vos Transactions</h2>
                " ;   
            $transactionTable = new TableVue() ;
            $transactionTable->TransactionTable($this->historique['transaction']) ;
            echo "
            <form name='signal' action='' method='POST' style='display:none;'>
            
                <label for='problem'>Le problème</label>
                <div>
                    <input type='text' id='problem' name='problem'>
                </div>
        
                <label for='explic'>Exliquer</label>
                <div>
                    <textarea id='explic' name='explic' rows='10' cols='65'>
                    </textarea>
                </div>

                <input name='signalbtn' type='submit' value='Signaler'>
            </form>
            ";
            echo "</section>";
            echo "
            <script>
                let jq3 = jQuery.noConflict();
                jq3(document).ready(function() {
                    jq3('a.signal').bind('click',signal_form);
                    jq3('form[name=signal]').bind('submit',signal_submit);

                    fix_notes() ;
                });//ready
                
                function signal_form(){
                    let form = jq3('form[name=signal]') ;
                    form.show();
                    form.attr('action','/?view=Profile&action=Signaler&id='+jq3(this).attr('id') ) ;
                    //form.append('<input type='hidden' id='annonce' name='annonce' value=''>');
                    return false ;
                }
                
                function signal_submit(e){
                    let form = jq3('form[name=signal]') ;
                    form.children('input:not(#signalbtn)').val('') ;
                    form.hide();
                    e.preventDefault();

                    let formdata = form.serialize();

                    // Make AJAX request
                    jq3.ajax({
                        type: 'POST',
                        url: form.attr('action'),
                        data: formdata,
                    })
                    .done(function() {
                        console.log('Success');
                    });
                }

                function fix_notes(){
                    
                    jq3.each(jq3('.transac_note[data-note!=0]'),function(i,t){
                        let note = jq3(t).attr('data-note') ;
                        jq3(t).children('a').css('color', 'black');
                        jq3(t).children('a').addClass('fixed_link');
                        jq3(t).children('a:not(:lt('+(5-note)+'))').css('color', 'green');
                        jq3(t).next('b').text(note);
                        jq3(t).children('a').off('hover');
                        jq3(t).children('a').off('click');
                    });
                    
                    //console.log(jq3('.transac_note').attr('data-note'));
                }

                function note(event){
                    let note = parseInt(jq3(event).attr('data-note')) ;
                    let id = parseInt(jq3(event).closest('.transac_note').attr('data-id')) ;

                    jq3(event).closest('.transac_note').attr('data-note',note) ;
                    fix_notes();

                    jq3.ajax({
                        type: 'GET',
                        url: '/?view=Profile&action=Note&id='+id+'&note='+note,
                        async:false,
                        data: note,
                        dataType: 'json' ,
                        cache: true
                    })
                    .done(function(data) {    
                        jq3(event).closest('div.note').prevAll('div.transporteur').children('div.Stars').attr('style','--rating:'+(data)+';');
                    })
                    .fail( function(err) {
                        console.log('error');
                        console.log(err.responseText);
                    })  ;

                    return false
                }

            </script>
            ";


        }

        public function viewTransporteurInfos(){
            echo "<section id='transporteurinfos'>
                    <h2>Vos Infos de transport</h2>
                " ; 
            $transporteurNote = '<div class="Stars" style="--rating:'.$this->transporteurInfos['transporteur']['TransporteurNote'].';" aria-label="Note du transporteur : '.$this->transporteurInfos['transporteur']['TransporteurNote'].'/5"></div>' ;
            echo "<h3>Note :</h3>".$transporteurNote ;
            echo "<h3>Le gain : <b>".$this->transporteurInfos['transporteur']['TransporteurProfit']."</b></h3>" ;
            echo "<h3>Le pourcentage du site : <b>".$this->transporteurInfos['transporteur']['TransporteurProfit']."</b></h3>" ;
            echo "</section>";

            echo "<section id='trajetsinfos'>
                    <h2>Vos trajets</h2>
                " ; 
            $trajetTable = new TableVue() ;
            $trajetTable->TrajetTable($this->transporteurInfos['trajet']) ;
                    

            echo "</section>";
            
            echo "
                <script type ='text/javascript'>
                    let jq5 = jQuery.noConflict();

                    let row = 0 ;
                    let new_trajet=[] ;
                    let transporteur = ".$_SESSION['profile']['TransporteurID'].";
                    jq5(document).ready(function(){
                        
                        jq5('button.trajet_removebtn').bind('click',remove_trajet);
                        
                        jq5('button#add_trajetbtn').bind('click',add_trajet);

                        jq5('select#new_depart,select#new_arriv').bind('change',function(){
                            let vid = jq5('select#new_depart').val()=='' || jq5('select#new_arriv').val()=='' ;
                            //console.log('value='+vid);
                            jq5('#add_trajetbtn').attr('disabled',vid);
                        });

                        init_trajets() ;

                    });//ready
                    
                    function init_trajets(){
                        
                        let departsel = jq5('select#new_depart') ;
                        let arrivsel = jq5('select#new_arriv') ;
                        
                        wilayas=['ADRAR','CHLEF','LAGHOUAT','OUM BOUAGHI','BATNA','BEJAIA','BISKRA','BECHAR','BLIDA','BOUIRA','TAMANRASSET','TEBESSA','TLEMCEN','TIARET','TIZI-OUZOU','ALGER','DJELFA','JIJEL','SETIF','SAIDA','SKIKDA','SIDIBELABBES','ANNABA','GUELMA','CONSTANTINE','MEDEA','MOSTAGANEM','MSILA','MASCARA','OUARGLA','ORAN','EL BAYDH','ILLIZI','BORDJ','BOUMERDES','EL TAREF','TINDOUF','TISSEMSILT','EL OUED','KHENCHLA','SOUK AHRASS','TIPAZA','MILA','AÏN DEFLA','NÂAMA','AÏN TEMOUCHENT','GHARDAÏA','RELIZANE'];

                        jq5.each(wilayas, function (i, w) {
                            departsel.append(jq5('<option/>', { 
                                value: w,
                                text : w 
                            }));
                            arrivsel.append(jq5('<option/>', { 
                                value: w,
                                text : w 
                            }));
                        });
                    }
                    
                    function add_trajet(event){
                        let departsel = jq5('select#new_depart') ;
                        let arrivsel = jq5('select#new_arriv') ;

                        let trajet = [{
                                            depart : departsel.val(),
                                            arriv : arrivsel.val()
                                        }] ;

                        console.log(JSON.stringify({trajet})) ;

                        // Make AJAX request
                        jq5.ajax({
                            type: 'POST',
                            url: '/?view=Profile&action=addTrajet&transporteur='+transporteur,
                            data: {trajet:JSON.stringify(trajet)} ,
                            datatype:'json',
                            async:false,
                        })
                        .done(function(data) {
                            console.log('Success'+data) ;
                        });
                        location.reload(true)
                        return false ;
                    }

                    function remove_trajet(event){
                        if(jq5('section#trajetsinfos div.line').length>2){
                            row_elements = jq5(this).closest('span').prevUntil('span') ;
                        row = row_elements.filter('div.rownum').attr('data-id') ;

                        //console.log(jq5(this).closest('span').prevUntil('span'));

                        jq5.ajax({
                            type: 'GET',
                            url: '/?view=Profile',
                            data: 'action=SupprimerTrajet&transporteur='+transporteur+'&trajet='+row,
                            async:false
                        })
                        .done(function() {
                            console.log('Success') ;
                            row = 0 ;
                            row_elements.remove();
                        });
                        jq5(this).remove();
                        }
                        return false ;
                    }

                    </script>
                    " ;
        }

        public function viewCertificatInfos(){
            echo "<section id='certificat'>
                    <h2>Votre Certificat</h2>
                " ; 
            $status = (empty($this->transporteurInfos['certificat'])?'non certifié':$this->transporteurInfos['certificat']['CertificatStatus']) ;
            echo "<h3>Status : <b>".$status."</b></h3>" ;
            //var_dump($this->transporteurInfos['certificat']) ;
            if( !empty($this->transporteurInfos['certificat']) ){
                if( strpos($status,'valid')!== false ){
                    echo "<h4>Documents : <b>".$this->transporteurInfos['certificat']['CertificatDocuments']."</b> </h3>" ;
                }
                elseif (strpos($status,'refus')!== false) {
                    echo "<h3>Justificatif : <b>".$this->transporteurInfos['certificat']['CertificatResponse']."</b> </h3>" ;
                }
            }
            echo "</section>";
        }


        public function contents(){
            //header('Content-type: text/html; charset=utf-8');
            $this->viewProfileInfos();
            $this->viewProfileAnnonces();
            $this->viewProfileTranscations();
            //var_dump($this->transporteurInfos) ;
            if($this->transporteurInfos){
                $this->viewTransporteurInfos();
                $this->viewCertificatInfos();
            }
        }
    }
    //<script type ='text/javascript' src='./../Assets/jquery-3.5.0.js' ></script>
    /*
    echo "
                <script>
                    let jq2 = jQuery.noConflict();
                    jq2(document).ready(function() {
                        trajets_lists();
                    });//ready
                    
                    function trajets_lists(){
                        trajets=".json_encode($this->transporteurInfos['trajet']).";
                        jq2.each(trajets, function (i, t) {

                            let newrow = jq2('<tr id=\"trajet'+t.TrajetID+'\"></tr>') ;
                            let rownum = jq2('<td><p>'+t.TrajetID+'</td>') ;
                            let depart = jq2('<td><input type=\"text\" name=trajet['+t.TrajetID+'][depart] value=\"'+t.PtDepart+'\" readonly></td>') ;
                            let arriv = jq2('<td><input type=\"text\"  name=trajet['+t.TrajetID+'][arriv] value=\"'+t.PtArrivee+'\"  readonly></td>') ;
                            let xbutton = jq2('<td><input type=\"button\" class=\"removetrajet\" name=\"remove'+t.TrajetID+'\" id=\"'+t.TrajetID+'\" value=\"X\" disabled></td>') ;
                            
                            newrow.append(rownum);
                            newrow.append(depart);
                            newrow.append(arriv);
                            newrow.append(xbutton);

                            jq2('table#trajets tbody').append(newrow) ;
                        });
                    }
                    </script>
                    ";



        function modif(event){
                        console.log('modif');
                        row = jq5(this).closest('span').prev('div.rownum').attr('data-id') ;
                        
                        jq5(this).empty() ;
                        jq5(this).append('<i class=\"fa fa-check fa-2x\" style=\"font-size: larger;\"></i>');
                        
                        jq5('select[name=depart_'+row+'],select[name=arriv_'+row+']').attr('disabled',false);
                        
                        jq5(this).unbind('click');
                        jq5(this).bind('click',valid_modif);
                        populate_trajet();
                        return false ;
                    }//modif

                    function valid_modif(event){
                        console.log('valid_modif');
                        let departsel = jq5('select[name=depart_'+row+']') ;
                        let arrivsel = jq5('select[name=arriv_'+row+']') ;

                        let trajetdata = {
                                            depart:departsel.val(),
                                            arriv:arrivsel.val()
                                        } ;

                        // Make AJAX request
                        jq3.ajax({
                            type: 'POST',
                            url: '/?view=Profile&action=ModifierTrajet&transporteur='+transporteur+'&trajet='+row,
                            data: trajetdata,
                            datatype:json,
                            async:false,
                            cache:true
                        })
                        .done(function(data) {
                            console.log('Success = '+data) ;
                            jq5(this).closest('span').prev('div.rownum').attr('data-id',data) ;
                            row = 0 ;
                        })
                        .fails(function(err) {
                            console.log('err');
                        });

                        jq5(this).empty() ;
                        jq5(this).append('<i class=\"fas fa-pen-square\" style=\"font-size: larger;\"></i>');

                        jq5(this).unbind('click');
                        jq5(this).bind('click',modif);
                        departsel.attr('disabled',true);
                        arrivsel.attr('disabled',true);

                    }//valid_modif

                    function populate_trajet(){
                        
                        let departsel = jq5('select[name=depart_'+row+']') ;
                        let arrivsel = jq5('select[name=arriv_'+row+']') ;

                        let initial_depart = departsel.val();
                        let initial_arriv = arrivsel.val();
                        
                        wilayas=['ADRAR','CHLEF','LAGHOUAT','OUM BOUAGHI','BATNA','BEJAIA','BISKRA','BECHAR','BLIDA','BOUIRA','TAMANRASSET','TEBESSA','TLEMCEN','TIARET','TIZI-OUZOU','ALGER','DJELFA','JIJEL','SETIF','SAIDA','SKIKDA','SIDIBELABBES','ANNABA','GUELMA','CONSTANTINE','MEDEA','MOSTAGANEM','MSILA','MASCARA','OUARGLA','ORAN','EL BAYDH','ILLIZI','BORDJ','BOUMERDES','EL TAREF','TINDOUF','TISSEMSILT','EL OUED','KHENCHLA','SOUK AHRASS','TIPAZA','MILA','AÏN DEFLA','NÂAMA','AÏN TEMOUCHENT','GHARDAÏA','RELIZANE'];
                        departsel.empty();
                        arrivsel.empty() ;

                        jq5.each(wilayas, function (i, w) {
                            departsel.append(jq5('<option/>', { 
                                value: w,
                                text : w 
                            }));
                            arrivsel.append(jq5('<option/>', { 
                                value: w,
                                text : w 
                            }));
                        });

                        departsel.find('option[value='+initial_depart+']').attr('selected',true) ;
                        arrivsel.find('option[value='+initial_arriv+']').attr('selected',true) ;





                        let newrow = '<div class=\"rownum\" data-id=\"'+data+'\" >'+(jq5('section#trajetsinfos div.line').length)+'</div>'
                            +'<div class=\"depart\" >'
                                +'<select name=\"depart_'+data+'\" disabled>'
                                    +'<option value=\"'+departsel.val()+'\" selected style=\"text-align: center;\">'+departsel.val()+'</option>'
                                +'</select>'
                            +'</div>'
                            +'<div class=\"arriv\" >'
                                +'<select name=\"arriv_'+data+'\" disabled>'
                                    +'<option value=\"'+arrivsel.val()+'\" selected style=\"text-align: center;\">'+arrivsel.val()+'</option>'
                                +'</select>'
                            +'</div>'
                            +'<span class=\"trajet_edit\" >'
                                +'<button title=\"supprimer le trajet\" class=\"trajet_removebtn\" >'
                                    +'<i class=\"fas fa-trash-alt\"></i>'
                                +'</button>'
                            +'</span>'
                            +'<div class=\"line\"></div>' ;
                            //console.log(jq5(this).closest('span').prev('div.rownum')) ;
                            jq5(newrow).insertBefore('div.add_trajet_row');
                            //jq5('button.trajet_removebtn').bind('click',remove_trajet);
                    }
    */
?>