<?php

/*

  _   _     _       _   __     _   
 | | | |   (_)     | | /_ |   | |  
 | |_| |__  _ _ __ | | _| |___| |_ 
 | __| "_ \| | "_ \| |/ / / __| __|
 | |_| | | | | | | |   <| \__ \ |_ 
  \__|_| |_|_|_| |_|_|\_\_|___/\__|
                ultra mini runtime.   

*/

namespace Think1st\Db;

class Connection
{
    private $link = false;
    public function start()
    {
        
        if ($this->link === false) {
            /* Attempt to connect to MySQL database */
            $this->link = mysqli_connect(
                (isset($_ENV['DB_SERVER'])) ? $_ENV['DB_SERVER'] : 'localhost', 
                (isset($_ENV['DB_USERNAME'])) ? $_ENV['DB_USERNAME'] : 'root', 
                (isset($_ENV['DB_PASSWORD'])) ? $_ENV['DB_PASSWORD'] : '', 
                (isset($_ENV['DB_DATABASE'])) ? $_ENV['DB_DATABASE'] : 'feminenzanew'
            );

            // Create user table if not exists.
            $sql = "CREATE TABLE IF NOT EXISTS `users` (`id` int(11) NOT NULL AUTO_INCREMENT,`username` varchar(50) NOT NULL,`password` varchar(255) NOT NULL,`company` varchar(255) NOT NULL,`created_at` datetime DEFAULT CURRENT_TIMESTAMP,PRIMARY KEY (`id`),UNIQUE KEY `username` (`username`)) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
            if($stmt = mysqli_prepare($this->link, $sql)){
                if(mysqli_stmt_execute($stmt)){
                }
            }

            // Check connection
            if ($this->link === false) {
                die("ERROR: Could not connect. " . mysqli_connect_error());
            }
            return $this->link;
        } else {
            return $this->link;
        }
    }
    public function stop()
    {
        if ($this->link === false) {
            mysqli_close($this->link);
        }
    }
}
