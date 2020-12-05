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

class Login
{

    /**
     * @var \Think1st\TemplateEngine\TwigEngine
     */
    public $twig;

    /**
     * @var string
     */
    public $BASE_PATH;

    public function __construct($BASE_PATH) {
        // Set basepath
        $this->BASE_PATH = $BASE_PATH;
        // Initialize template engine
        $this->twig = new \Think1st\TemplateEngine\TwigEngine();
        $this->twig->BASE_PATH = $BASE_PATH;
    }

    public function route()
    {
        // Setup database connection
        $db = new \Think1st\Db\Connection();
        $link = $db->start();

        // Define variables and initialize with empty values
        $username = $password = "";
        $username_err = $password_err = false;

        // Processing form data when form is submitted
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            $error = true; 

            // Check if username is empty
            if (empty(trim($_POST["username"]))) {
                $username_err = "Please enter username.";
            } else {
                $username = trim($_POST["username"]);
            }

            // Check if password is empty
            if (empty(trim($_POST["password"]))) {
                $password_err = "Please enter your password.";
            } else {
                $password = trim($_POST["password"]);
            }

            // Validate credentials
            if (empty($username_err) && empty($password_err)) {
                // Prepare a select statement
                $sql = "SELECT id, username, password, company FROM users WHERE username = ?";

                if ($stmt = mysqli_prepare($link, $sql)) {
                    // Bind variables to the prepared statement as parameters
                    mysqli_stmt_bind_param($stmt, "s", $param_username);

                    // Set parameters
                    $param_username = $username;

                    // Attempt to execute the prepared statement
                    if (mysqli_stmt_execute($stmt)) {
                        // Store result
                        mysqli_stmt_store_result($stmt);

                        // Check if username exists, if yes then verify password
                        if (mysqli_stmt_num_rows($stmt) == 1) {
                            // Bind result variables
                            mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password, $company);
                            if (mysqli_stmt_fetch($stmt)) {
                                if (password_verify($password, $hashed_password)) {
                                    // Password is correct, so start a new session
                                    session_start();

                                    // Store data in session variables
                                    $_SESSION["loggedin"] = true;
                                    $_SESSION["id"] = $id;
                                    $_SESSION["username"] = $username;
                                    $_SESSION["company"] = $company;

                                    // Error is false we redirect
                                    $error = false;

                                    // Redirect user to welcome page
                                    header("location: /go");
                                } else {
                                    // Display an error message if password is not valid
                                    $password_err = "The password you entered was not valid.";
                                }
                            }
                        } else {
                            // Display an error message if username doesn't exist
                            $username_err = "No account found with that username.";
                        }
                    } else {
                        echo "Oops! Something went wrong. Please try again later.";
                    }

                    // Close statement
                    mysqli_stmt_close($stmt);
                }
            }

            // Close connection
            mysqli_close($link);

            if ($error) {
                echo $this->render($username, $username_err, $password_err);
            }
        } else {
            echo $this->render($username, $username_err, $password_err);
        }
    
    }

    /**
     * @param string username
     * @param string username_err
     * @param string password_err
     */
    private function render($username, $username_err, $password_err) {
        return $this->twig->templateEngine($this->BASE_PATH . "/login.twig", [ 
            "username" => $username,
            "username_err" => $username_err,
            "password_err" => $password_err,
            "title" => "Think1st - Content Management System"
        ]);
    }
}