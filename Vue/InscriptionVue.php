<?php
    require_once('CommonVue.php');
    class InscriptionVue extends CommonVue
    {
        public function InscriptionForm(){
            //var_dump(1) ;
            echo "
                <form name='inscription' action='/?view=Profile&action=Inscription' method='POST'>
                        <h2>Inscription</h2>
                        
                        <label for='nom'>Nom</label>
                        <div>
                            <input type='nom' id='nom' name='profile[nom]'>
                        </div>

                        <label for='prenom'>Prénom</label>
                        <div>
                            <input type='prenom' id='prenom' name='profile[prenom]'>
                        </div>

                        <label for='email'>Email</label>
                        <div>
                            <input type='email' id='email' name='profile[email]'>
                        </div>

                        <label for='tel'>Téléphone</label>
                        <div>
                            <input type='number' id='tel' name='profile[tel]'>
                        </div>

                        <label for='addr'>Adresse</label>
                        <div>
                            <input type='text' id='addr' name='profile[addr]'>
                        </div>

                        <label for='pwd'>Mot de passe</label>
                        <div>
                            <input type='password' id='pwd' name='profile[pwd]'>
                        </div>

                        <label for='confirmpwd' >Confirmer le mot de passe</label>
                        <div>
                            <input type='password' id='confirmpwd' name='profile[confirmpwd]'>
                        </div>

                        <label for='type' >Vous etes un transporteur ?</label>
                        <div>
                            <input type='checkbox' id='type' name='profile[type]' value='Oui'>
                        </div>

                        <label class='trajet' for='trajet' style='display:none'>Vos Trajet</label>                        
                        <div id='trajet' class='trajet' style='display:none'>
                            
                            <label for='depart'>De</label>
                            <select name='depart' id='depart'>
                                <option value=\"\">--Choisir un point de départ--</option>
                            </select>

                            <label for='arriv'>Vers</label>
                            <select name='arriv' id='arriv'>
                                <option value=\"\">--Choisir un point de d'arrivée--</option>
                            </select>

                            <input type='button' id='trajetbtn' name='trajetbtn' value='+' disabled>

                            <table>
                            </table>
                        
                        </div>

                        <label for='certif' class='certificat' style='display:none'>Vouliez-vous devenir un transporteur certifié ?</label>
                        <div id='certificat' class='certificat' style='display:none'>
                            <input type='checkbox' id='certif' name='certif' value='Oui'>
                            <button style='display:none' name='demandbtn'>Demander un certificat
                            </button>  
                        </div>

                        <input type='submit' id='inscrirbtn' name='inscrirbtn' value='Inscrire'>
                </form>";
                
            echo "
                <script src='https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js'></script>
                <script type ='text/javascript'>
                    let jq1 = jQuery.noConflict(true);
                    
                    let i=1;
                    jq1(document).ready(function() {
                        populate_trajet();
                        
                        jq1('input[type=checkbox]').bind('change',inscrirTransporteur);
                        jq1('div#trajet input#trajetbtn').bind('click',add_trajet);
                        //jq1('input#pwd').bind('input',onChange);
                        //jq1('input#confirmpwd').bind('input',onChange);

                        jq1('button[name=demandbtn]').click(function(){
                            jq1('input#inscrirbtn').attr('disabled',false);
                            jq1(this).text(' Demande de certificat attaché ');
                            jq1(this).append('<i class=\"fa fa-check\"></i>');
                            jq1('#certif').valid();
                            return false ;
                        });//certificatbutton

                        jq1('form select').bind('change',function(){
                            let vid = jq1('form select#depart').val()=='' || jq1('form select#arriv').val()=='' ;
                            console.log('value='+vid);
                            jq1('#trajetbtn').attr('disabled',vid);
                        });

                        jq1('form[name=inscription] input').bind('change',function(){
                            jq1(this).valid();
                        });

                        /*jq1.validator.setDefaults({
                            debug: true,
                            success: 'valid'
                        });*/

                        jq1.validator.addMethod('trajetCount', function(value, element) {
                                console.log( jq1('#type').is(':unchecked') || jq1('div#trajet table tr').length > 0 ) ;
                                return( jq1('#type').is(':unchecked') || jq1('div#trajet table tr').length > 0 );
                            },'ajouter au moins un trajet!' );

                        jq1.validator.addMethod('demandCertif', function(value, element) {
                            return( jq1('#certif').is(':unchecked') || jq1('button[name=demandbtn]').text().includes('Demande de certificat attaché') );
                        },'confirmer votre demande de certificat !' );

                        jq1.validator.addMethod('unique',
                                            function(value, element, params) {
                                                let isUnique = false;
                                                //console.log('value : '+value);
                                                jq1.ajax({
                                                    url: '/?view=Profile' ,
                                                    type : 'GET' ,
                                                    async: false ,
                                                    data: 'action=' + params[0] + '&email=' + value ,
                                                    dataType: 'json' ,
                                                    cache: true
                                                }).done(function (data) {
                                                        isUnique = Boolean(data) ;
                                                })
                                                .fail( function(err) {
                                                    console.log('error');
                                                    console.log(err);
                                                })  ;
                                                return isUnique;
                                            }
                                        );

                        jq1('form[name=inscription]').validate({
                            rules: {
                                'profile[nom]': {
                                    required:true,
                                },
                                'profile[prenom]':{
                                    required:true,
                                },
                                'profile[email]': {
                                    required: true,
                                    email: true,
                                    maxlength: 127,
                                    unique:['validerEmail', jq1('#email').val()],
                                },
                                'profile[tel]':{
                                   required:true,
                                   digits: true,
                                   maxlength:10 ,
                                },
                                'profile[addr]':{
                                    required:true,
                                 },
                                'profile[pwd]' : {
                                    required:true,
                                    minlength : 5
                                },
                                'profile[confirmpwd]' : {
                                    required:true,
                                    equalTo : '#pwd',
                                },
                                'profile[type]': {
                                    trajetCount:function(){
                                        return jq1('#type').is(':checked') ;
                                    },
                                },
                                'certif': {
                                    demandCertif:function(){
                                        return jq1('#certif').is(':checked') ;
                                    },
                                }
                            },
                            messages: {
                                'profile[nom]': {
                                    required:'Nom est obligatoire'
                                },
                                'profile[prenom]':{
                                    required:'prénom est obligatoire'
                                },
                                'profile[email]': {
                                  required: 'Email est obligatoire',
                                  email: '@ email invalide',
                                  maxlength: 'email trop long',
                                  unique:'Cet email est déja utilisé !'
                                },
                                'profile[tel]':{
                                   required:'tel est obligatoire',
                                   digits:'numéro de téléphone invalid',
                                   maxlength:'numero trop long. max:10 chiffres'
                                },
                                'profile[addr]':{
                                    required:'addresse est obligatoire',
                                 },
                                'profile[pwd]' : {
                                    required:'mot de passe est obligatoire',
                                    minlength : 'mot de passe très courte'
                                },
                                'profile[confirmpwd]' : {
                                    required:'mot de passe est obligatoire',
                                    equalTo : 'les mots de passe ne se correspondent pas'
                                }
                            }
                         });//validate
                         


                    });//ready

                    function inscrirTransporteur(event){
                            if(jq1(this).attr('id')=='type'){
                                if(jq1('input#type').is(':checked')) {
                                    jq1('.trajet,.certificat').show();
                                }else{
                                    jq1('.trajet,.certificat').hide();
                                }
                                jq1('input#certif').prop('checked',false);
                                jq1('#certif').valid();
                            }

                            if(jq1('input#certif').is(':checked')) {
                                jq1('button[name=demandbtn]').show();
                            }else{
                                jq1('button[name=demandbtn]').hide();
                            }
                            jq1('button[name=demandbtn]').text('Demander un certificat');
                    }

                    function populate_trajet(){
                        wilayas=['ADRAR','CHLEF','LAGHOUAT','OUM BOUAGHI','BATNA','BEJAIA','BISKRA','BECHAR','BLIDA','BOUIRA','TAMANRASSET','TEBESSA','TLEMCEN','TIARET','TIZI-OUZOU','ALGER','DJELFA','JIJEL','SETIF','SAIDA','SKIKDA','SIDIBELABBES','ANNABA','GUELMA','CONSTANTINE','MEDEA','MOSTAGANEM','MSILA','MASCARA','OUARGLA','ORAN','EL BAYDH','ILLIZI','BORDJ','BOUMERDES','EL TAREF','TINDOUF','TISSEMSILT','EL OUED','KHENCHLA','SOUK AHRASS','TIPAZA','MILA','AÏN DEFLA','NÂAMA','AÏN TEMOUCHENT','GHARDAÏA','RELIZANE'];
                        
                        jq1.each(wilayas, function (i, w) {
                            jq1('form[name=inscription] select').append(jq1('<option/>', { 
                                value: w,
                                text : w 
                            }));
                        });
                    }

                    function add_trajet(){
                        let departsel = jq1('select[name=depart]') ;
                        let arrivsel = jq1('select[name=arriv]') ;
        
                        if(jq1('div#trajet table tr:has(input[value='+departsel.val()+']+input[value='+arrivsel.val()+'])').length == 0){
                            
                            let newrow = jq1('<tr id=\"trajet'+i+'\"><td></td></tr>') ;
                            let depart = jq1('<input type=\"text\" name=trajet['+i+'][depart] value=\"'+departsel.val()+'\" readonly>') ;
                            let arriv = jq1('<input type=\"text\"  name=trajet['+i+'][arriv] value=\"'+arrivsel.val()+'\"  readonly>') ;
                            let xbutton = jq1('<input type=\"button\" class=\"removetrajet\" name=\"remove'+i+'\" id=\"'+i+'\" value=\"X\" readonly=\"false\">') ;
                        
                            newrow.append(depart);
                            newrow.append(arriv);
                            newrow.append(xbutton);

                            jq1('div#trajet table').append(newrow); 
                            i++;
                        }
                        jq1('table .removetrajet').bind('click',remove_trajet);
                        jq1('#type').valid();
                    }

                    function remove_trajet(event){
                        let button_id = jq1(this).attr('id') ;
                        jq1('div#trajet table tr#trajet'+button_id).remove() ;
                        jq1('#type').valid();
                    }

                </script>
            ";
        }
        public function contents(){
            $this->InscriptionForm();
        }
    }
?>