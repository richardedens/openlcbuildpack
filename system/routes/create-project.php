<?php

namespace Think1st\Routes;

class CreateProject {

    public function route($BASE_PATH) {

        // Get the twig template engine.
        $twig = new \Think1st\TemplateEngine\TwigEngine();
        $twig->BASE_PATH = $BASE_PATH;

        // Set connection to false
        $connected = false;
        $pathCreated = "";

        // Create the project database.
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // collect value of input field
            $projectname = $_POST['projectname'];
            $projectname = strtolower(str_replace(" ","_",$projectname));
            $company = $_SESSION['company'];
            if (empty($company)) {
                $company = $_SESSION['id'];
            }
            if (!empty($projectname)) {
                // Create the PDO connection to the SQLite database
                $pathCreated = "sqlite:" . __DIR__ . "/projects/" . $company . "___" . $projectname . ".db";
                $pdo = new \PDO($pathCreated);
                header("location: /go");
                exit;
            }
        }

        // Detect file and fload it.
        echo $twig->templateEngine($BASE_PATH . "/" . "project-create.twig", [ 
            "loggedin" => false,
            "title" => "Digital Team - Do more in less time.",
            "dbconnection" => $connected,
            "path" => $pathCreated
        ]);
    }
}