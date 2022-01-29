<?php 
    require_once('CommonController.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/Model/News.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/Vue/AllNewsVue.php');
    class NewsController extends CommonController
    {
        //private $id ;
        public function __construct($page){
            parent::__construct($page);
            $this->m = new News() ;
        }

        public function __get($property){
            return $this->property;
        }

        public function __set($property, $value){
            $this->property = $value ;
        }

        public function getAllNews(){
            $allNews=$this->m->AllNewsQuery();
            return $allNews;
        }

        public function getNewsDetails($id){
            $news=$this->m->NewsQuery($id);
            return $news;
        }

        public function viewPage($id=-1){
            $vue_name = $this->page.'Vue' ;
            require_once($_SERVER['DOCUMENT_ROOT'].'/Vue'.'/'.$vue_name.'.php');
            $NewsInfos = ($this->page == 'AllNews' ? $this->getAllNews() : $this->getNewsDetails($id) ) ;
            $this->v = new $vue_name($NewsInfos);
            $this->v->view();
        }
    }
?>