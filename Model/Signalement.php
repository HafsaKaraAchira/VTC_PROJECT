<?php 
    require_once(__DIR__.'/../Model/Query.php');
    class Signalement
    {

        public function addSignalQuery($signal){
            if(!empty($_SESSION['profile']))
            {
                $query = new Query("INSERT INTO 
                                    `signalement`(
                                        `SenderID`,
                                        `AnnonceID`,
                                        `ReceiverID`,
                                        `SignalementProblem`,
                                        `SignalementText`
                                    )
                                    VALUES(:sender, :annonce, :receiver,:problem ,:explic)
                                    ",
                                    array(
                                        array(':annonce',$signal['id'],PDO::PARAM_INT),
                                        array(':sender',$_SESSION['profile']['ProfileID'],PDO::PARAM_INT),
                                        array(':receiver',$signal['user'],PDO::PARAM_INT),
                                        array(':problem',$signal['problem'],PDO::PARAM_STR),
                                        array(':explic',$signal['explic'],PDO::PARAM_STR)
                                        )
                                    ) ;
            
                $query->execute_query(PDO::FETCH_ASSOC) ;
            }
        }

    }//Signalement
?>