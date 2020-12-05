<?php

namespace Think1st\Security;

class Authentication
{
    public static function checkLoggedInBoolean()
    {
        if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
            return false;
        } else {
            return true;
        }
    }

    public static function checkLoggedIn()
    {
        if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
            header("location: /login");
            exit;
        }
    }
    
    public static function checkLoggedInAndGoToGo()
    {
        if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
            header("location: /go");
            exit;
        }
    }
}
