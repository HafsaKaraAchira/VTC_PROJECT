<?php 
    require_once(__DIR__.'/../Model/Query.php');
    class News
    {
        public function NewsQuery($id){
            $news = array() ;
            //var_dump($id) ;

            $sql1 = "SELECT * FROM news WHERE news.NewsID = :id";
            $params = array(array(':id',$id,PDO::PARAM_INT) ) ;
            $query = new Query($sql1,$params) ;

            $news['texts'] = $query->execute_query(PDO::FETCH_ASSOC)[0];
            
            $sql2 = "SELECT
                        ImageLink
                    FROM (SELECT
                            newsimage.ImageID,
                            image.ImageLink
                        FROM
                            image
                        JOIN newsimage USING(ImageID)
                        WHERE
                            newsimage.NewsID = :id
                        AND news.NewsShow=1
                            
                        ) v 
                    ORDER BY v.ImageID ASC
                    ";
            $query = new Query($sql2,$params) ;
             
            $news['images'] = $query->execute_query(PDO::FETCH_ASSOC) ;
            //var_dump($news) ;
            return $news ;
        }

        public function AllNewsQuery(){
            $sql = "SELECT news.NewsID,
                news.NewsDate,
                news.NewsTitle,
                SUBSTRING(news.NewsText, 1, 100) AS Newsdescription, 
                v.ImageLink
                FROM news
                JOIN(
                    SELECT
                        MIN(newsimage.ImageID) AS ImageID,
                        newsimage.NewsID,
                        image.ImageLink
                    FROM
                        image JOIN newsimage USING(ImageID) 
                    GROUP BY newsimage.NewsID 
                ) v 
                ON news.NewsID = v.NewsID 
                WHERE news.NewsShow=1
                ORDER BY news.NewsID ASC
            ";
            $query = new Query($sql) ;
            return $query->execute_query(PDO::FETCH_ASSOC) ;
        }

    }
?>