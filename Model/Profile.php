<?php
    require_once(__DIR__.'/../Model/Query.php');
    class Profile{
        
        private static $PROFILE ;

        public function __get($property){return $this->property;}

        public function __set($name, $value){ /*empty*/}

        public static function getConnection($email,$password){
            if(!isset($_SESSION['profile'])){
            
                $query1 = new Query("SELECT 
                	                    `profile`.`ProfileID`,
                                        `prenom`,
                                        `profileType`,
                                        transporteur.TransporteurID 
                                    FROM `profile` 
                                    LEFT JOIN `transporteur` 
                                    ON `profile`.ProfileID = transporteur.ProfileID
                                    WHERE `email` = :email 
                                    AND `mdp` = :password"
                                    ,array(
                                        array(':email',$email,PDO::PARAM_STR)
                                        ,array(':password',$password,PDO::PARAM_STR))
                                    ) ;
                                        
                $profile=$query1->execute_query(PDO::FETCH_ASSOC)[0]??null ;                                                        
                if($profile) $_SESSION['profile'] = $profile;
                //$_SESSION['profile'] = $query1->execute_query(PDO::FETCH_ASSOC)[0] ;
            }
        }

        public function ProfileInfosQuery(){
            if(!empty($_SESSION['profile']))
            {
                if($_SESSION['profile']['profileType'] == 'transporteur'){
                    $query2 = new Query("SELECT * 
                                    FROM `profile` 
                                    CROSS JOIN `transporteur` 
                                    USING(ProfileID) 
                                    WHERE profile.ProfileID = :id",
                                    array(
                                        array(':id',$_SESSION['profile']['ProfileID'],PDO::PARAM_INT)
                                    ) 
                                ) ;
                }else{
                    $query2 = new Query("SELECT * 
                                    FROM `profile` 
                                    WHERE profile.ProfileID = :id",
                                    array(
                                        array(':id',$_SESSION['profile']['ProfileID'],PDO::PARAM_INT)
                                    ) 
                                );
                }

                return $query2->execute_query(PDO::FETCH_ASSOC)[0] ;
            }
            
        }

        public static function uniqueProfileQuery($email){
            $query1 = new Query("SELECT COUNT(`email`) AS c
                                    FROM `profile` 
                                    WHERE `email` = :email
                                ",
                                array(
                                    array(':email',$email,PDO::PARAM_STR)
                                    )
                                ) ;
            $count =(int) $query1->execute_query(PDO::FETCH_ASSOC)[0]['c'] ;
            //var_dump($count) ;
            return ($count == 0?1:0) ; 
        }        

        public static function addProfileQuery($newProfile){

            $type = (empty($newProfile['type'])?'client':'transporteur') ;
            $query2 = new Query("INSERT INTO 
                                `profile`(
                                    `nom`,
                                    `prenom`,
                                    `email`,
                                    `tel`,
                                    `adresse`,
                                    `mdp`,
                                    `profileType`
                                )
                                VALUES(
                                    :nom,
                                    :prenom,
                                    :email,
                                    :tel,
                                    :addr,
                                    :pwd,
                                    :typ
                                )
                                ",
                                array(
                                    array(':nom',$newProfile['nom'],PDO::PARAM_STR),
                                    array(':prenom',$newProfile['prenom'],PDO::PARAM_STR),
                                    array(':email',$newProfile['email'],PDO::PARAM_STR),
                                    array(':tel',$newProfile['tel'],PDO::PARAM_INT),
                                    array(':addr',$newProfile['addr'],PDO::PARAM_STR),
                                    array(':pwd',$newProfile['pwd'],PDO::PARAM_STR),
                                    array(':typ',$type,PDO::PARAM_STR),
                                )
                            ) ;

            return $query2->execute_query(PDO::FETCH_ASSOC) ;
        }

        public static function UpdateProfileQuery($newProfile){
            $query2 = new Query("UPDATE
                                    `profile`
                                SET
                                    `nom` = :nom ,
                                    `prenom` = :prenom,
                                    `email` = :email,
                                    `tel` = :tel,
                                    `adresse` = :addr,
                                    `mdp` = :pwd
                                WHERE
                                    `ProfileID`=:id
                                AND `mdp`=:ancpwd ",
                                array(
                                    array(':id',$_SESSION['profile']['ProfileID'],PDO::PARAM_INT),
                                    array(':nom',$newProfile['nom'],PDO::PARAM_STR),
                                    array(':prenom',$newProfile['prenom'],PDO::PARAM_STR),
                                    array(':email',$newProfile['email'],PDO::PARAM_STR),
                                    array(':tel',$newProfile['tel'],PDO::PARAM_INT),
                                    array(':addr',$newProfile['adresse'],PDO::PARAM_STR),
                                    array(':pwd',$newProfile['pwd'],PDO::PARAM_STR),
                                    array(':ancpwd',$newProfile['pwdanc'],PDO::PARAM_STR),
                                )
                            ) ;
            
            $query2->execute_query(PDO::FETCH_ASSOC) ;
            return $query2->getAffectedRowsCount();
        }

        public static function DeconnectProfile(){
            unset($_SESSION['profile']) ;
        }

    }

    //return $_SESSION['profile']  ;
?>