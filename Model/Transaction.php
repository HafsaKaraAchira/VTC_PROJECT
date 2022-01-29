<?php 
    require_once(__DIR__.'/../Model/Query.php');
    class Transaction
    {

        public function addTransactionsQuery($transaction){
            if(!empty($_SESSION['profile']))
            {
                $query = new Query("INSERT INTO `transaction`
                                    (`AnnonceID`, `TransporteurID`,`ProposeurID`)
                                    VALUES(:annonce, :transporteur,:proposeur)
                                    ",
                                    array(
                                        array(':annonce',$transaction['annonce'],PDO::PARAM_INT),
                                        array(':transporteur',$transaction['transporteur'],PDO::PARAM_INT),
                                        array(':proposeur',$_SESSION['profile']['ProfileID'],PDO::PARAM_INT),
                                        )
                                    ) ;
            
                $query->execute_query(PDO::FETCH_ASSOC) ;
            }
        }

        public function confirmTransactionQuery($transaction){
            if(!empty($_SESSION['profile']))
            {
                $query = new Query("UPDATE `transaction` 
                                    SET `TransactionStatus`= :response 
                                    WHERE `TransactionID` = :id
                                    " ,
                                    array(
                                        array(':id',$transaction['id'],PDO::PARAM_INT),
                                        array(':response',$transaction['response'],PDO::PARAM_STR)
                                    )
                                    ) ;
            
                $query->execute_query(PDO::FETCH_ASSOC) ;

                $query = new Query("UPDATE
                                        transporteur
                                    SET
                                        transporteur.TransporteurProfit = transporteur.TransporteurProfit 
                                            +(
                                                SELECT
                                                    `annonce`.`AnnonceTarif` - `annonce`.`AnnonceTarif` * `annonce`.`AnnonceTax` / 100 AS profit
                                                FROM
                                                    `annonce`
                                                WHERE
                                                    `annonce`.`AnnonceID` = (
                                                                            SELECT `AnnonceID` 
                                                                            FROM `transaction` 
                                                                            WHERE `TransactionID` = :id 
                                                                            )
                                            )
                                    WHERE
                                        transporteur.TransporteurID =(
                                                                    SELECT `TransporteurID` 
                                                                    FROM `transaction` 
                                                                    WHERE `TransactionID` = :id 
                                                                    )
                                    " ,
                                    array(
                                        array(':id',$transaction['id'],PDO::PARAM_INT)
                                    )
                                    ) ;
            
                $query->execute_query(PDO::FETCH_ASSOC) ;
            }
        }

        public function noteTransactionQuery($transaction){
            if(!empty($_SESSION['profile']))
            {
                //var_dump($transaction['id']) ;
                //var_dump($transaction['note']) ;
                $query = new Query("UPDATE `transaction` 
                                    SET `TransactionNote`= :note 
                                    WHERE `TransactionID` = :id;

                                    SELECT `TransactionNote` FROM
                                        transporteur WHERE  transporteur.TransporteurID IN 
                                        SELECT `transporteurID` FROM `transaction`
                                        WHERE `TransactionID` = :id;
                                    " ,
                                    array(
                                        array(':id',$transaction['id'],PDO::PARAM_INT),
                                        array(':note',$transaction['note'],PDO::PARAM_STR)
                                    )
                                    ) ;
            
                $query->execute_query(PDO::FETCH_ASSOC) ;

                $query = new Query("SELECT
                                        transporteur.TransporteurNote
                                    FROM transporteur
                                    WHERE transporteur.TransporteurID 
                                    IN(
                                        SELECT
                                            `transporteurID`
                                        FROM `transaction`
                                        WHERE `TransactionID` = :id
                                    )
                                    " ,
                                    array(
                                        array(':id',$transaction['id'],PDO::PARAM_INT)
                                    )
                                    ) ;
                
                $note = $query->execute_query(PDO::FETCH_ASSOC)[0]['TransporteurNote'] ;
                //var_dump($note);

                return $note ;
            }
        }

        public function ProfileTransactionsQuery(){
            $transactions= array();
            if(!empty($_SESSION['profile']))
            {
                $query2 = new Query("SELECT *
                                    FROM
                                    (
                                        SELECT
                                            `profile`.nom AS ClientNom,
                                            `profile`.prenom AS ClientPrenom,
                                            annonce.AnnonceUserID AS ClientID,
                                            annonce.AnnonceID,
                                            annonce.AnnonceTarif,
                                            annonce.AnnonceTax
                                        FROM
                                            `profile`
                                        INNER JOIN annonce 
                                        ON annonce.AnnonceUserID = `profile`.ProfileID
                                    ) a
                                    JOIN(
                                        SELECT
                                            `transaction`.*,
                                            p.ProfileID AS TransporteurProfile,
                                            p.nom AS TransporteurNom,
                                            p.prenom AS TransporteurPrenom,
                                            p.certificat,
                                            p.TransporteurNote
                                        FROM
                                            `transaction`
                                        INNER JOIN(
                                            SELECT
                                                transporteur.TransporteurID,
                                                transporteur.certificat,
                                                transporteur.TransporteurNote,
                                                `profile`.nom,
                                                `profile`.prenom,
                                                `profile`.`ProfileID`
                                            FROM
                                                `profile`
                                            INNER JOIN transporteur 
                                            ON transporteur.ProfileID = `profile`.ProfileID
                                        ) p
                                    ON p.TransporteurID = `transaction`.TransporteurID
                                    WHERE
                                        `transaction`.TransporteurID IN(
                                        SELECT
                                            transporteur.TransporteurID
                                        FROM
                                            transporteur
                                        WHERE
                                            transporteur.ProfileID = :user)
                                    OR `transaction`.`AnnonceID` IN(
                                        SELECT
                                            annonce.AnnonceID
                                        FROM
                                            annonce
                                        WHERE
                                            annonce.AnnonceUserID = :user
                                        )
                                    ) t
                                    ON t.AnnonceID = a.AnnonceID
                                    ",
                                    array(
                                        array(':user',$_SESSION['profile']['ProfileID'],PDO::PARAM_INT)
                                        )
                                    ) ;
            
                $transactions = $query2->execute_query(PDO::FETCH_ASSOC) ;
            }
            return $transactions ;

        }//ProfileTransactionsQuery


    }//Transaction
?>
        