<?php

namespace Think1st\WordPress;

class Posts {

    public static function get($COMMAND_URI) {

        $welcome = 'welcome';
        $lang = (isset($_GET['l'])) ? $_GET['l'] : "en";
        switch($lang) {
            case "nl":
                $welcome = "welkom"; //Dutch
                break;
            case "en":
                $welcome = "welcome"; //English
                break;
            case "da":
                $welcome = "welcome"; //Danish
                break;
            case "fr":
                $welcome = "welcome"; //French
                break;
            case "de":
                $welcome = "welcome"; //German
                break;
            case "he":
                $welcome = "welcome"; //Hebrew
                break;
            case "pr":
                $welcome = "welcome"; //Persian
                break;
            case "po":
                $welcome = "welcome"; //Portogees
                break;
            case "es":
                $welcome = "welcome"; //Spanish
                break;
        }
        $page_name = ($COMMAND_URI === "/") ? $welcome : $COMMAND_URI;
        $page_name = str_replace("/", "", $page_name);
        $post_content = '';

        // Get the wordpress database connection
        $wp = \Think1st\WordPress\Db::connection();
        
         // Prepare a select statement
         $sql = "SELECT `id`, `post_title`, `post_content` FROM `wp_68u2vb_posts` WHERE `post_name` = '" . $page_name . "'";
            
         if($stmt = mysqli_prepare($wp, $sql)){
            if(mysqli_stmt_execute($stmt)){

                $posts = [];

                //Binding values in result to variables
                mysqli_stmt_bind_result($stmt, $id, $post_title, $post_content);

                while (mysqli_stmt_fetch($stmt)) {
                    $posts[] = ["id" => $id,"post_title" => $post_title, "post_content" => $post_content];
                }

                if (is_array($posts) && isset($posts[0]['post_content']) && $posts[0]['post_content'] != "") {
                    $post_content = $posts[0]['post_content'];
                }

                return $post_content;

            }
        }

    }

    public static function route($basepath, $path) {

        // Get the wordpress database connection
        $wp = \Think1st\WordPress\Db::connection();

        // Get the twig template engine.
        $twig = new \Think1st\TemplateEngine\TwigEngine();
        $twig->BASE_PATH = $basepath;

        if (isset($_GET['p'])) {

            // Prepare a select statement
            $sql = "SELECT `id`, `post_title`, `post_content` FROM `wp_68u2vb_posts` WHERE `ID` = '" . $_GET["p"] . "'";
                
        } else {

            // Prepare a select statement
            $sql = "SELECT `id`, `post_title`, `post_content` FROM `wp_68u2vb_posts` WHERE `post_type` = 'page' LIMIT 0,1000";
                        
        }
        
        if($stmt = mysqli_prepare($wp, $sql)){
            if(mysqli_stmt_execute($stmt)){

                $posts = [];

                //Binding values in result to variables
                mysqli_stmt_bind_result($stmt, $id, $post_title, $post_content);

                while (mysqli_stmt_fetch($stmt)) {
                    $posts[] = ["id" => $id,"post_title" => $post_title, "post_content" => $post_content];
                }

                // Detect file and fload it.
                if(file_exists($path)){
                    echo $twig->templateEngine($path, [ 
                        "loggedin" => true,
                        "title" => "Think1st - Content Management System.",
                        "path" => $_SERVER['REQUEST_URI'],
                        "posts" => $posts
                    ]);
                } else {
                    echo $twig->templateEngine($basepath . "index.twig", [ 
                        "loggedin" => true,
                        "title" => "Think1st - Content Management System.",
                        "path" => $_SERVER['REQUEST_URI']
                    ]);
                }

            }
        }

    }

}