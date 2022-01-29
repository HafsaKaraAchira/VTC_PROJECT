<?php
    require_once('CommonVue.php');
    class NewsVue extends CommonVue
    {
        private $news ;

        public function __construct($news){
            $this->news = $news ;
        }

        public function News(){
            //var_dump($this->news['images']) ;
            //".$this->news['texts']['NewsID']."'>
            echo "<section id='news'>
                    <h2>".$this->news['texts']['NewsTitle']."</h2>
                    <h3>".$this->news['texts']['NewsDate']."</h3>
                    <p>".$this->news['texts']['NewsText']."</p>
                " ;   
            foreach ($this->news['images'] as $image) {
                echo "<img src=\"".(substr_count($image['ImageLink'],$_SESSION['imageFolder'])?"/?view=News&action=viewImage&link=".basename($image['ImageLink']):$image['ImageLink'])."\">" ;
            }

            echo "</section>";
        }

        public function contents(){
            $this->News();
        }
    }
?>