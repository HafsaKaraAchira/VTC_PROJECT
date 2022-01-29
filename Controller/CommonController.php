<?php 
    require_once(__DIR__.'/../Model/Configuration.php');
    abstract class CommonController
    {
        protected $v ;
        protected $m ;
        protected $page ;

        public function __construct($page){
            $this->page = $page ;
        }

        public function doUploadImages(){
            $data = array();
            if (!file_exists($_SESSION['imageFolder'])) {
                mkdir($_SESSION['imageFolder'], 0777, true);// Create a new direcotry
            }
            $fileCount = count($_FILES["images"]['name']);//files numbers
            for($i=0; $i < $fileCount; $i++)
            {
                $target_file = $_SESSION['imageFolder'] .'/'. basename($_FILES["images"]["name"][$i]);
                $uploadOk = 1;
                $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

                // Check if image file is a actual image or fake image
                $check = getimagesize($_FILES["images"]["tmp_name"][$i]);
                if($check !== false) {echo "File is an image - " . $check["mime"] . ".";      $uploadOk = 1;}
                else                 {echo "File is not an image.";    $uploadOk = 0;}

                // Check if file already exists
                if (file_exists($target_file)) {$uploadOk = 0;}

                if($uploadOk==1){
                    if (move_uploaded_file($_FILES["images"]["tmp_name"][$i], $target_file)) {var_export($uploadOk) ;}                         
                }
                array_push($data,$target_file);
            }
            return $data;
        }

        public function doOutputImage($imagename){
            $imagepath = $_SESSION['imageFolder'] .'/'. $imagename;
            ob_start();
            $mimetype=mime_content_type($imagepath);
            header('Content-Type: '.$mimetype);
            ob_end_clean();
            //var_dump(file_exists($imagepath));
            $fp = fopen($imagepath, 'rb');
            echo fpassthru($fp);
            //exit;
        }

        abstract public function viewPage() ;
    }
?>




