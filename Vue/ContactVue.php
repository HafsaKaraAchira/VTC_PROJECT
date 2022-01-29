<?php
    require_once('CommonVue.php');
    class ContactVue extends CommonVue
    {
        private $contacts ;

        public function __construct($contacts){
            $this->contacts = $contacts ;
        }

        public function contacts(){
            echo "<section id='selection'>
                    <h2>Contactez-nous</h2>
                " ;   
            foreach ($this->contacts as $contact) {
                echo "<div class='contact'>
                        <h5>".$contact['ContactName']."</h5>
                        <span>".$contact['ContactText']."</span>
                    </div>";
            }
            echo "</section>";
        }
        public function contents(){
            $this->contacts();
        }
    }
?>