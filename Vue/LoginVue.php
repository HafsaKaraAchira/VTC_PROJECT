<?php
    require_once('CommonVue.php');
    class LoginVue extends CommonVue
    {
        public function LoginForm(){
            echo "
                    <form name='login' action='/?view=Login&action=Login' method='POST'>
                        <h2>Connecter-vous</h2>
                        <label for='email'>Email</label>
                        <input type='email' name='email' required>
                    
                        <label for='pwd'>Mot de passe</label>
                        <input type='password' name='pwd' required>

                        <input type='hidden' name='previouspage' value='".$_SERVER['HTTP_REFERER']."'>
        
                        <input name='loginbtn' type='submit' value='Connnecter-Vous'>
                    </form> <script src='https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js'></script>
                        <script>
                        let jq2 = jQuery.noConflict();
    
                        jq2(document).ready(function(){
                            let incorrect = ".(!isset($_SESSION['warn'])?"true":"false").";
    
                            jq2.validator.addMethod('loginIncorrect', 
                                function(value, element) {
                                    return(incorrect);
                                },
                                'Les informations saisis sont incorrectes !'
                            );
    
                            jq2('form[name=login]').validate({
                                rules: {
                                    'email': {
                                        loginIncorrect:true,
                                    },
                                    'pwd':{
                                        loginIncorrect:true,
                                    }
                                },//rules
                                messages: {
                                    'email': {required:'Ce champ est obligatoire'},
                                    'pwd':{required:'Ce champ est obligatoire'}
                                },//messages
                             });//validate
    
                            jq2( '#email,#pwd' ).rules( 'add', {loginIncorrect:true});
                            
                            if(!incorrect){
                                jq2('form').validate().form();
                                console.log('validate');
                            }
    
                            jq2( '#email,#pwd' ).rules( 'add', {required:true});
    
                            incorrect= true;
                        })
    
                        </script>
                ";
                unset($_SESSION['warn']);
        }
        public function contents(){
            $this->LoginForm();
        }
    }
?>
