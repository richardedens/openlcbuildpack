<?php 

/**
 *
 *  _   _     _       _   __     _   
 * | | | |   (_)     | | /_ |   | |  
 * | |_| |__  _ _ __ | | _| |___| |_ 
 * | __| '_ \| | '_ \| |/ / / __| __|
 * | |_| | | | | | | |   <| \__ \ |_ 
 *  \__|_| |_|_|_| |_|_|\_\_|___/\__|
 *                ultra mini runtime.   
 * 
 * Author:      Gerhard Richard Edens
 * 
 * Copyright 2020 by Think1st All Rights Reserved.
 * 
 * MIT LICENSE: Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 * 
 */

// Load dependencies
require __DIR__ . '/system/autoload.php';

// Initialize the session
session_start();

// Get the request uri
$PATH = "";
if (isset($_SERVER['REQUEST_URI'])) {
    $PATH = $_SERVER['REQUEST_URI'];
}
if (isset($_GET['path'])) {
    $PATH = $_GET['path'];
}
$REQUEST_URI_PARTS = explode("/", $PATH );
$COMMAND_URI = "/" . $REQUEST_URI_PARTS[0];

// Micro Twig Engine
$BASE_PATH = getcwd() . "/views/";
$EXTENTION = ".twig";
$EXTENTION_TWIG = ".twig";
$EXTENTION_HTML = ".html";

// Yeah check out the pages and what is possible
switch ($COMMAND_URI) {

    // PHP driven functionality
    case "/login":

        // Login
        $login = new \Think1st\Routes\Login($BASE_PATH);
        $login->route();

        break;
    case "/logout":

        // Logout
        $logout = new \Think1st\Routes\Logout();
        $logout->route();

        break;
    case "/register":

        // Register
        $register = new \Think1st\Routes\Register($BASE_PATH);
        $register->route();

        break;
    case "/project-create":

        // Project Create
        $createProject = new \Think1st\Routes\CreateProject();
        $createProject->route($BASE_PATH);
        break;

    // Login Protected template rendering
    case "/go":
    case "/architect":
    case "/mindmap":
    case "/kanbanboard":
    case "/project-cloud-architecture":
    case "/project-database":
    case "/project-db":
    case "/project-design":
    case "/project-detail":
    case "/project-edit":
    case "/project-security":
    case "/project-settings":
    case "/wp-post-details":
    case "/wp-posts":

        // Check if we are logged in
        \Think1st\Security\Authentication::checkLoggedIn();

        // Get simplified TWIG engine
        $twig = new \Think1st\TemplateEngine\TwigEngine();
        $twig->BASE_PATH = $BASE_PATH;

        echo $twig->templateEngine($BASE_PATH . "/" . $COMMAND_URI . $EXTENTION, [ 
            "loggedin" => false,
            "title" => "Think1st - Content Management System",
            "path" => $_SERVER['REQUEST_URI'],
            "wpmenu" => $menu,
            "post_content" => $post_content,
            "project" => (is_array($REQUEST_URI_PARTS) && isset($REQUEST_URI_PARTS[1]) && $REQUEST_URI_PARTS[1] != null) ? $REQUEST_URI_PARTS[1] : ""
        ]);
        break;

    // Default behavior
    default:

        // Get page content from wordpress DB.
        $post_content = [];

        // Get simplified TWIG engine
        $twig = new \Think1st\TemplateEngine\TwigEngine();
        $twig->BASE_PATH = $BASE_PATH;
    
        // Check if we are logged in
        if (\Think1st\Security\Authentication::checkLoggedInBoolean()) {

            echo $twig->templateEngine($BASE_PATH . "/editor" . $EXTENTION, [ 
                "loggedin" => true,
                "title" => "Think1st - Content Management System.",
                "path" => $_SERVER['REQUEST_URI'],
                "posts" => []
            ]);

        } else {
            if(file_exists($BASE_PATH . $COMMAND_URI . $EXTENTION_TWIG)){
                echo $twig->templateEngine($BASE_PATH . $COMMAND_URI . $EXTENTION_TWIG, [ 
                    "loggedin" => false,
                    "title" => "Think1st - Content Management System",
                    "path" => $_SERVER['REQUEST_URI'],
                    "wpmenu" => $menu,
                    "post_content" => $post_content,
                    "project" => (is_array($REQUEST_URI_PARTS) && isset($REQUEST_URI_PARTS[1]) && $REQUEST_URI_PARTS[1] != null) ? $REQUEST_URI_PARTS[1] : ""
                ]);
            } else {
                if(file_exists($BASE_PATH . $COMMAND_URI . $EXTENTION_HTML)){
                    echo $twig->templateEngine($BASE_PATH . $COMMAND_URI . $EXTENTION_HTML, [ 
                        "loggedin" => false,
                        "title" => "Think1st - Content Management System",
                        "path" => $_SERVER['REQUEST_URI'],
                        "wpmenu" => $menu,
                        "post_content" => $post_content,
                        "project" => (is_array($REQUEST_URI_PARTS) && isset($REQUEST_URI_PARTS[1]) && $REQUEST_URI_PARTS[1] != null) ? $REQUEST_URI_PARTS[1] : ""
                    ]);
                } else {

                    if(file_exists($BASE_PATH . "index" . $EXTENTION_TWIG)){
                        echo $twig->templateEngine($BASE_PATH . "index" . $EXTENTION_TWIG, [ 
                            "loggedin" => false,
                            "title" => "Think1st - Content Management System",
                            "path" => $_SERVER['REQUEST_URI'],
                            "wpmenu" => $menu,
                            "post_content" => $post_content
                        ]);
                    } else {
                        echo $twig->templateEngine($BASE_PATH . "index" . $EXTENTION_HTML, [ 
                            "loggedin" => false,
                            "title" => "Think1st - Content Management System",
                            "path" => $_SERVER['REQUEST_URI'],
                            "wpmenu" => $menu,
                            "post_content" => $post_content
                        ]);
                    }

                }

            }
            
        }
        break;
}

?>