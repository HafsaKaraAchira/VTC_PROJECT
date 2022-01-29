<?php 
    require_once(__DIR__.'/../Model/Query.php');
    class Statistiques
    {
        public function UsersStatsQuery(){
            $userstats = array() ;
            $query = new Query("SELECT DISTINCT
                                    `profile`.profileType,
                                    COUNT(`profile`.ProfileID) AS UserCount
                                FROM `profile`
                                GROUP BY `profile`.profileType
                                UNION
                                SELECT DISTINCT
                                    'Total',
                                    COUNT(`profile`.profileType) AS UserCount
                                FROM `profile`
                                ") ;
            $userstats['typeUsers'] = $query->execute_query(PDO::FETCH_ASSOC) ;

            return $userstats ;
        }   

        public function AnnoncesStatsQuery(){
            $annoncestats = array() ;
            $query = new Query("SELECT DISTINCT
                                    AnnonceTypeTransport,
                                    COUNT(AnnonceID) AS AnnonceTypeCount
                                FROM annonce
                                WHERE annonce.AnnonceArchive = 0
                                GROUP BY annonce.AnnonceTypeTransport
                                UNION
                                SELECT DISTINCT
                                    'Total',
                                    COUNT(AnnonceID) AS nnonceTypeCount
                                FROM annonce
                                WHERE annonce.AnnonceArchive = 0
                                ") ;
            $annoncestats['typeTransport'] = $query->execute_query(PDO::FETCH_ASSOC) ;

            $query = new Query("SELECT DISTINCT
                                    AnnonceMoyenTransport,
                                    COUNT(AnnonceID) AS AnnonceMoyenCount
                                FROM annonce
                                WHERE annonce.AnnonceArchive = 0
                                GROUP BY
                                    annonce.AnnonceMoyenTransport
                                ") ;
            $annoncestats['moyenTransport'] = $query->execute_query(PDO::FETCH_ASSOC) ;
            
            return $annoncestats ;
        }

        public function TopAnnoncesQuery(){
            $sql = "SELECT
                        annonce.AnnonceID,
                        s.DemandCount,
                        annonce.AnnoncePtDepart,
                        annonce.AnnoncePtArrivee,
                        annonce.AnnonceTypeTransport,
                        annonce.AnnoncePoids,
                        annonce.AnnonceVolume,
                        annonce.AnnonceDescription,
                        image.ImageLink
                    FROM
                        annonce
                    JOIN image 
                    ON annonce.AnnonceImage = image.ImageID
                    INNER JOIN(
                        SELECT DISTINCT
                            `transaction`.AnnonceID,
                            COUNT(`transaction`.ProposeurID) AS DemandCount
                        FROM
                            `transaction`
                        GROUP BY
                            `transaction`.AnnonceID
                    ) s 
                    USING(AnnonceID)
                    WHERE annonce.AnnonceArchive = 0
                    AND annonce.AnnonceValidee=1
                    ORDER BY
                        s.DemandCount
                    DESC
                ";
            $query = new Query($sql) ;
            return $query->execute_query(PDO::FETCH_ASSOC) ;
        }
        
    }
?>