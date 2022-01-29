<?php
    session_start();

    require_once($_SERVER['DOCUMENT_ROOT'].'/Model/Configuration.php');
    Configuration::getConfiguration();

    $_SESSION['imageFolder']=dirname($_SERVER["DOCUMENT_ROOT"]).'/UploadImages';

    $Controllers = array(
        'PageAccueil' => 'PageAccueil' ,
        'Login' => 'Profile' ,
        'Inscription' => 'Profile' ,
        'Deconnecter' => 'Profile' ,
        'Presentation' => 'SiteInfos' ,
        'Contact' => 'SiteInfos' ,
        'Statistiques' => 'Statistiques' ,
        'Annonce' => 'Annonce' ,
        'Recherche' => 'Annonce' ,
        'AllNews' => 'News' ,
        'News' => 'News',
        'Profile' => 'Profile'
    );

    $Default_controller = 'PageAccueil' ;


    #### routing to appropriateController

    $Vue_name = ($_GET['view']??$Default_controller) ;
    $controller_name = $Controllers[$Vue_name].'Controller';
    require_once($_SERVER['DOCUMENT_ROOT'].'/Controller'.'/'.$controller_name.'.php');
    
    $c = new $controller_name($Vue_name);
    //var_dump($controller_name);
    ## actions

    if(isset($_GET['action'])){
        //var_dump(explode('&view=',$_SERVER['HTTP_REFERER'])) ;

        $Actions = array(
            'viewImage' => [
                'doOutputImage' => $_GET['link']??null
            ],
            
            'Login' => [
                'doLogin' => $_POST 
            ] ,
                                    
            'Inscription' => [
                'doInscription' => $_POST 
            ],

            'validerEmail' => [
                'validerInfos' => $_GET['email']??null
            ],
    
            'Deconnecter' => [
                'doDeconnecter' => null 
            ] ,

            'NewsDetails' => [
                'viewPage' => $_GET['id']??null 
            ],

            'AnnonceDetails' => [
                'viewPage' => $_GET['id']??null
            ],

            'Recherche' => [
                'viewPage' => $_POST??null
            ],

            'Modif' => [
                'doUpdateProfile' => $_POST??null
            ],

            'addAnnonce' => [
                'doAddAnnonce' => $_POST??null
            ],

            'Signaler' => [
                'doSignalProfile' => $_REQUEST??null
            ],
            'Confirm' => [
                'doConfirmTransaction' => $_GET??null
            ],
            'Note' => [
                'doNoteTransaction' => $_REQUEST??null
            ],
            'ProposerTransaction' => [
                'doSugguestTransaction' => $_REQUEST??null
            ],
            'SupprimerAnnonce' => [
                'doArchiveAnnonce' => $_GET['id']??null
            ],
            'ModifierAnnonce' => [
                'doModifierAnnonce' => $_REQUEST??null
            ],
            'ModifierTrajet' => [
                'doUpdateTrajet' => $_REQUEST??null
            ],
            'SupprimerTrajet' => [
                'doRemoveTrajet' => $_REQUEST??null
            ],
            'addTrajet' => [
                'doAddTrajet' => $_REQUEST??null
            ]

        );
        ## call methods
        foreach ($Actions[$_GET['action']] as $method => $params) {
            if($params !== null ){
                //var_dump($Actions[$_GET['action']]) ;
                //var_dump($method) ;
                //var_dump($params) ;
                $c->$method($params) ;
            }else{
                $c->$method() ;
            }
        }
    }
    else{ ## go to controller view
        $c->viewPage();
    }

?>