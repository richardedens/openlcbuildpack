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

namespace Think1st\WordPress;

// Globally define wordpress database connection settings
define('WP_DB_SERVER', (isset($_ENV['WORDPRESS_DB_SERVER'])) ? $_ENV['WORDPRESS_DB_SERVER'] : 'localhost');
define('WP_DB_USERNAME', (isset($_ENV['WORDPRESS_DB_USERNAME'])) ? $_ENV['WORDPRESS_DB_USERNAME'] : 'root');
define('WP_DB_PASSWORD', (isset($_ENV['WORDPRESS_DB_PASSWORD'])) ? $_ENV['WORDPRESS_DB_PASSWORD'] : '');
define('WP_DB_NAME', (isset($_ENV['WORDPRESS_DB_NAME'])) ? $_ENV['WORDPRESS_DB_NAME'] : 'feminenzawp');

// Create database connection;
class Db {

  public static function connection() {

    /* Attempt to connect to MySQL database */
    $wp = mysqli_connect(WP_DB_SERVER, WP_DB_USERNAME, WP_DB_PASSWORD, WP_DB_NAME);
    
    // Check connection
    if($wp === false){
        die("ERROR: Could not connect. " . mysqli_connect_error());
    }
    return $wp;
  }

}
