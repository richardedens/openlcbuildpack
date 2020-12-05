<?php

/*

  _   _     _       _   __     _   
 | | | |   (_)     | | /_ |   | |  
 | |_| |__  _ _ __ | | _| |___| |_ 
 | __| '_ \| | '_ \| |/ / / __| __|
 | |_| | | | | | | |   <| \__ \ |_ 
  \__|_| |_|_|_| |_|_|\_\_|___/\__|
                ultra mini runtime.   

*/

namespace Think1st\Routes;

class Logout
{

    public function route()
    {

        // Unset all of the session variables
        $_SESSION = array();

        // Destroy the session.
        session_destroy();

        // Redirect to login page
        header("location: /");
        exit;
    }
}
