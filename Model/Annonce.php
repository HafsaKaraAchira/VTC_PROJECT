<?php 
    require_once(__DIR__.'/../Model/Query.php');
    class Annonce
    {

        public function AnnonceGeneralInfosQuery($id){
            $sql = "SELECT
                        annonce.AnnonceID,
                        annonce.AnnoncePtDepart,
                        annonce.AnnoncePtArrivee,
                        annonce.AnnonceTypeTransport,
                        annonce.AnnoncePoids,
                        annonce.AnnonceVolume,
                        annonce.AnnonceDescription,
                        annonce.AnnonceUserID,
                        annonce.AnnonceArchive,
                        annonce.AnnonceValidee,
                        image.ImageLink
                    FROM
                        annonce JOIN image ON
                     annonce.AnnonceImage = image.ImageID
                    WHERE
                        annonce.AnnonceID = :id
                    AND annonce.AnnonceArchive = 0
                    ";

            $params = array(array(':id',$id,PDO::PARAM_INT) ) ;
            $query = new Query($sql,$params) ;
            //var_dump($query->execute_query(PDO::FETCH_ASSOC)) ;
            $annonce = $query->execute_query(PDO::FETCH_ASSOC) ;
            if($query->getAffectedRowsCount() > 0 ){
                if( $annonce[0]['AnnonceValidee'] || ($annonce[0]['AnnonceUserID']==$_SESSION['profile']['ProfileID']) )
                { return $annonce[0]; }
                else{return null ;}
            }
            else{return null ;}
            
        }
        
        public function AnnonceDetailsQuery($id){
            $sql = "SELECT
                        annonce.AnnonceUserID,
                        annonce.AnnonceMoyenTransport,
                        annonce.AnnonceTarif,
                        annonce.AnnonceTax,
                        annonce.AnnonceValidee,
                        profile.ProfileID,
                        profile.nom,
                        profile.prenom,
                        profile.tel,
                        `type_transport`.TransportGarantie,
                        MAX(
                            CASE WHEN EXISTS(
                                SELECT
                                    1 AS `one`
                                FROM
                                    `transaction`
                                WHERE
                                    `transaction`.ProposeurID = :proposeur
                                AND `transaction`.`AnnonceID` = :id
                            ) THEN 1 ELSE NULL
                            END
                        ) AS `contacteDeja`
                        ,
                        MAX(
                            CASE WHEN EXISTS(
                                SELECT 1
                                FROM `transaction`
                                WHERE
                                    `transaction`.`AnnonceID` = 6 
                                AND `transaction`.`TransactionStatus` != 'pas encore confirmée' 
                            ) THEN 1 ELSE NULL
                            END
                        ) AS `transactionValideDeja`
                    FROM profile
                    JOIN annonce 
                    ON 
                        annonce.AnnonceUserID = profile.ProfileID
                    JOIN type_transport 
                    ON annonce.AnnonceTypeTransport=type_transport.TypeTransport
                    WHERE
                        annonce.AnnonceID = :id
                    AND annonce.AnnonceArchive = 0
                    ";

            $params = array(array(':id',$id,PDO::PARAM_INT),
                            array(':proposeur',$_SESSION['profile']['ProfileID'],PDO::PARAM_INT) 
                        ) ;
            $query = new Query($sql,$params) ;
            //var_dump($query->execute_query(PDO::FETCH_ASSOC)) ;
            return $query->execute_query(PDO::FETCH_ASSOC)[0] ;
        }
        
        public function AnounceTransportCorrespondanceQuery($annonce,$garantie,$depart,$arriv){
                $sql = "SELECT 
                        q.*,    
                        s.`contacteDeja`
                    FROM
                    (
                    SELECT
                        p.*,
                        `profile`.nom,
                        `profile`.prenom
                    FROM
                    (
                        SELECT
                            t.*,
                            transporteur.certificat,
                            transporteur.TransporteurNote AS note,
                            transporteur.ProfileID
                        FROM
                        (
                            SELECT
                                transport.TransporteurID
                            FROM
                            (
                                SELECT
                                    trajet.*
                                FROM
                                    trajet
                                WHERE
                                    trajet.PtDepart = :depart 
                                AND trajet.PtArrivee = :arriv
                            ) j
                            INNER JOIN `transport` 
                            ON transport.TrajetID = j.TrajetID
                        ) t
                        INNER JOIN `transporteur` 
                        ON transporteur.TransporteurID = t.TransporteurID
                        ".($garantie?'WHERE transporteur.certificat IS NOT NULL ':'WHERE 1')."
                    ) p
                    INNER JOIN `profile` 
                    ON `profile`.ProfileID = p.ProfileID
                    )q
                    LEFT JOIN(
                        SELECT 
    	                    transaction.TransporteurID,
    	                    1 AS `contacteDeja`
                        FROM
                            `transaction`
                        WHERE
                            `transaction`.`AnnonceID` = :annonce
                    )s
                    ON s.TransporteurID = q.`TransporteurID`
                ";
            
            $params = array(
                array(':annonce',$annonce,PDO::PARAM_INT),
                array(':depart',$depart,PDO::PARAM_STR),
                array(':arriv',$arriv,PDO::PARAM_STR) ) ;
            $query = new Query($sql,$params) ;
            //var_dump($query->execute_query(PDO::FETCH_ASSOC)) ;
            return $query->execute_query(PDO::FETCH_ASSOC) ;
        }

        public function rechercheQuery($depart,$arriv){
            $sql = "SELECT
                        AnnonceID,
                        AnnoncePtDepart,
                        AnnoncePtArrivee,
                        AnnonceTypeTransport,
                        AnnonceDescription,
                        image.ImageLink
                    FROM annonce
                    JOIN image 
                    ON annonce.AnnonceImage = image.ImageID
                    WHERE annonce.AnnoncePtDepart = :depart
                    AND annonce.AnnoncePtArrivee = :arriv
                    AND annonce.AnnonceArchive = 0
                    AND annonce.AnnonceValidee = 1
                    ORDER BY annonce.AnnonceID
                    ";

            $params = array(array(':depart',$depart,PDO::PARAM_STR),array(':arriv',$arriv,PDO::PARAM_STR) ) ;
            $query = new Query($sql,$params) ;
            //var_dump($query->execute_query(PDO::FETCH_ASSOC)) ;
            return $query->execute_query(PDO::FETCH_ASSOC) ;
        }

        public function annoncesSelectionQuery($limit=8,$Selectioncriteria = 'RAND()'){
            $sql = "SELECT 
                        AnnonceID,
                        AnnoncePtDepart,
                        AnnoncePtArrivee,
                        AnnonceTypeTransport,
                        AnnonceDescription,
                        image.ImageLink 
                    FROM annonce 
                    JOIN image 
                    ON 
                        annonce.AnnonceImage=image.ImageID 
                    WHERE annonce.AnnonceArchive = 0
                    AND annonce.AnnonceValidee = 1
                    ORDER BY ".$Selectioncriteria." LIMIT :limit 
                    ";

            $params = array( array(':limit',$limit,PDO::PARAM_INT) )  ;
            $query = new Query($sql,$params) ;
            return $query->execute_query(PDO::FETCH_ASSOC) ;
        }

        public function ProfileAnnoncesQuery(){
            $annonces= array();
            if(!empty($_SESSION['profile']))
            {
                $query2 = new Query("SELECT
                                    annonce.AnnonceID,
                                    annonce.AnnoncePtDepart,
                                    annonce.AnnoncePtArrivee,
                                    annonce.AnnonceTypeTransport,
                                    annonce.AnnoncePoids,
                                    annonce.AnnonceVolume,
                                    annonce.AnnonceDescription,
                                    annonce.AnnonceUserID,
                                    annonce.AnnonceValidee,
                                    image.ImageLink
                                FROM
                                    annonce
                                JOIN image 
                                ON annonce.AnnonceImage = image.ImageID
                                WHERE
                                annonce.AnnonceUserID = :user
                                AND annonce.AnnonceArchive = 0
                                ",
                                array(array(':user',$_SESSION['profile']['ProfileID'],PDO::PARAM_INT)) ) ;
            
                $annonces = $query2->execute_query(PDO::FETCH_ASSOC) ;
            }
            return $annonces ;
        }

        public function addAnnonceQuery($annonce){
            $query2 = new Query("INSERT INTO 
                                `annonce`(
                                    `AnnoncePtDepart`,
                                    `AnnoncePtArrivee`,
                                    `AnnonceTypeTransport`,
                                    `AnnoncePoids`,
                                    `AnnonceVolume`,
                                    `AnnonceMoyenTransport`,
                                    `AnnonceUserID`
                                )
                                VALUES(
                                    :depart,
                                    :arriv,
                                    :typ,
                                    :poids,
                                    :volume,
                                    :moyen,
                                    :user
                                )
                                ",
                                array(
                                    array(':depart',$annonce['depart'],PDO::PARAM_STR),
                                    array(':arriv',$annonce['arriv'],PDO::PARAM_STR),
                                    array(':typ',$annonce['type'],PDO::PARAM_STR),
                                    array(':poids',$annonce['poids'],PDO::PARAM_INT),
                                    array(':volume',$annonce['volume'],PDO::PARAM_STR),
                                    array(':moyen',$annonce['moyen'],PDO::PARAM_STR),
                                    array(':user',$annonce['profile'],PDO::PARAM_INT),
                                )
                            ) ;

            $query2->execute_query(PDO::FETCH_ASSOC) ;

            $query2 = new Query("CALL tarif_maj(LAST_INSERT_ID());") ;            
            $query2->execute_query(PDO::FETCH_ASSOC) ;

        }//addAnnonceQuery

        public function updateAnnonceQuery($annonce){
            $query2 = new Query("UPDATE 
                                `annonce`
                                SET
                                    `AnnoncePtDepart`=:depart,
                                    `AnnoncePtArrivee`=:arriv,
                                    `AnnonceTypeTransport`=:typ,
                                    `AnnoncePoids`=:poids,
                                    `AnnonceVolume`=:volume,
                                    `AnnonceMoyenTransport`=:moyen,
                                    `AnnonceDescription`= :descr
                                WHERE 
                                    `AnnonceID`=:id
                                ",
                                array(
                                    array(':depart',$annonce['depart'],PDO::PARAM_STR),
                                    array(':arriv',$annonce['arriv'],PDO::PARAM_STR),
                                    array(':typ',$annonce['type'],PDO::PARAM_STR),
                                    array(':poids',$annonce['poids'],PDO::PARAM_INT),
                                    array(':volume',$annonce['volume'],PDO::PARAM_STR),
                                    array(':moyen',$annonce['moyen'],PDO::PARAM_STR),
                                    array(':descr',$annonce['description'],PDO::PARAM_STR),
                                    array(':id',$annonce['id'],PDO::PARAM_INT),
                                )
                            ) ;

            $query2->execute_query(PDO::FETCH_ASSOC) ;

            $query2 = new Query("CALL tarif_maj(:id);",array(array(':id',$annonce['id'],PDO::PARAM_INT))) ;            
            $query2->execute_query(PDO::FETCH_ASSOC) ;

        }//addAnnonceQuery

        public function archiveAnnonceQuery($annonce){
            $query2 = new Query("UPDATE `annonce` 
                                SET `AnnonceArchive` = '1' 
                                WHERE `annonce`.`AnnonceID` = :id
                                ",
                                array(
                                    array(':id',$annonce,PDO::PARAM_INT)
                                )
                            ) ;

            $query2->execute_query(PDO::FETCH_ASSOC) ;

        }//addAnnonceQuery

    }//Annonce
    
?>
        