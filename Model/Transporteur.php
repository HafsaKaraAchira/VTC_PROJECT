<?php
    require_once(__DIR__.'/../Model/Query.php');
    class Transporteur{

        public function __get($property){return $this->property;}

        public function __set($property, $value){ /*empty*/}

        public function __construct(){
            
        }

        public function addTransporteurQuery($id,$trajets){

            $query = new Query("INSERT INTO 
                                `transporteur`(`ProfileID`)
                                VALUES(:id)
                                ",
                                array(
                                    array(':id',$id,PDO::PARAM_INT)
                                )
                            ) ;
            
            $transporteur = $query->execute_query(PDO::FETCH_ASSOC) ;
            //var_dump($transporteur);
            $this->addTrajetSQuery($transporteur,$trajets) ;

            return $transporteur ;
        }
        
        public function addTrajetSQuery($transporteur,$trajets){
            
            $proc="DROP PROCEDURE IF EXISTS `add_trajet` ; 
                CREATE PROCEDURE `add_trajet`
                (IN `id` INT, IN `depart` TEXT, IN `arriv` TEXT) NOT DETERMINISTIC MODIFIES SQL DATA SQL SECURITY DEFINER               BEGIN
                START TRANSACTION;
                INSERT IGNORE `trajet` (`PtDepart`,`PtArrivee`) 
                    VALUES(depart,arriv) 
                ;
                
                SELECT @trajet:=`TrajetID` 
                    FROM `trajet`
                    WHERE `PtDepart`= depart
                    AND `PtArrivee`= arriv
                ;
                
                INSERT INTO `transport` (`TransporteurID`, `TrajetID`) 
                    VALUES (id,@trajet) 
                ;
                SELECT @trajet as newid ;
            COMMIT;
            END
              " ;
            $query = new Query('',array());

            $query->addProc($proc) ;
            
            $query->setStatement('CALL add_trajet(:id,:depart,:arriv)');

            foreach ($trajets as $trajet) 
            {
               $query->setParameters(
                                array(
                                    array(':id',$transporteur,PDO::PARAM_INT),
                                    array(':depart',$trajet['depart'],PDO::PARAM_STR),
                                    array(':arriv',$trajet['arriv'],PDO::PARAM_STR)
                                )
                        ) ;

                $newid=reset($query->execute_query(PDO::FETCH_ASSOC)[0]) ;                  
            }
            return $newid ;
        }

        public function transporteurQuery($id){
            $query = new Query("SELECT *
                                FROM `transporteur`
                                WHERE `ProfileID`=:id
                                ",
                                array(
                                    array(':id',$id,PDO::PARAM_INT)
                                )
                            ) ;
            
            $transporteur = $query->execute_query(PDO::FETCH_ASSOC)[0] ;
            return $transporteur  ;
        }

        public function trajetsQuery($transporteur){
            $query = new Query("SELECT
                                    trajet.*
                                FROM `transport`
                                JOIN `trajet` 
                                ON 
                                    transport.TrajetID = trajet.TrajetID
                                WHERE
                                    transport.TransporteurID = :transporteur
                                ",
                                array(
                                    array(':transporteur',$transporteur,PDO::PARAM_INT)
                                )
                            ) ;
            
            $trajets = $query->execute_query(PDO::FETCH_ASSOC) ;
            return $trajets  ;
        }

        public function updateTrajetsQuery($newTrajet){
            
            $proc="DROP PROCEDURE `update_trajet`; 
                CREATE PROCEDURE `update_trajet`(
                IN `transporteur` INT,
                IN `trajet` INT,
                IN `depart` TEXT,
                IN `arriv` INT
            ) NOT DETERMINISTIC MODIFIES SQL DATA SQL SECURITY DEFINER
            BEGIN
                START TRANSACTION
                    ;
                INSERT IGNORE
                    `trajet`(`PtDepart`, `PtArrivee`)
                VALUES(depart, arriv);

                SELECT
                    @trajet := `TrajetID`
                FROM
                    `trajet`
                WHERE
                    `PtDepart` = depart 
                AND `PtArrivee` = arriv;
            
                UPDATE
                    `transport`
                SET
                    `TrajetID` = @trajet
                WHERE
                    `TransporteurID` = transporteur 
                AND `TrajetID` = trajet;

                DELETE
                FROM `trajet`
                WHERE `TrajetID` NOT 
                IN(
                    SELECT
                        `TrajetID`
                    FROM
                        `transport`
                    WHERE 1
                );

                SELECT
                @trajet AS newid;
                COMMIT
                ;
                END
            " ;
        
            $query = new Query('CALL update_trajet(:transporteur,:trajet,:depart,:arriv) ;',
                                array(
                                    array(':transporteur',$newTrajet['transporteur'],PDO::PARAM_INT),
                                    array(':trajet',$newTrajet['trajet'],PDO::PARAM_INT),
                                    array(':depart',$newTrajet['depart'],PDO::PARAM_STR),
                                    array(':arriv',$newTrajet['arriv'],PDO::PARAM_STR)
                                )
                            );

            $query->addProc($proc) ;     
            $query->execute_query(PDO::FETCH_ASSOC) ;                  
            $newtrajet = $query->execute_query(PDO::FETCH_ASSOC)[0]['newid'] ;
            return $newtrajet  ;
        }

        public function removeTrajetsQuery($trajet){
            
            $proc="DROP PROCEDURE IF EXISTS `remove_trajet`; 
                CREATE PROCEDURE `remove_trajet`(
                IN `transporteur` INT,
                IN `trajet` INT
            ) NOT DETERMINISTIC MODIFIES SQL DATA SQL SECURITY DEFINER
            BEGIN
                START TRANSACTION;
            
                DELETE FROM
                    `transport`
                WHERE
                    `TransporteurID` = transporteur 
                AND `TrajetID` = trajet;

                DELETE
                FROM `trajet`
                WHERE `TrajetID` NOT 
                IN(
                    SELECT
                        `TrajetID`
                    FROM
                        `transport`
                    WHERE 1
                );

                SELECT 1 ;

                COMMIT;
            END
            " ;
        
            $query = new Query('CALL remove_trajet(:transporteur,:trajet) ;',
                                array(
                                    array(':transporteur',$trajet['transporteur'],PDO::PARAM_INT),
                                    array(':trajet',$trajet['trajet'],PDO::PARAM_INT)
                                )
                            );

            $query->addProc($proc) ;     
            $query->execute_query(PDO::FETCH_ASSOC) ;
        }

    }
?>