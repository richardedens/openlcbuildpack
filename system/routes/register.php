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

namespace Think1st\Routes;

/**
 * Think1st\Routes\Register
 * 
 * The Register route enables individuals to register to the Think1st Content Management System.
 * It is intended to be a straight forward process, where one should provide a companyname (not required), a username and password, and password confirmation.
 * 
 * @copyright       2020 by Think1st 
 * @version         1.0.0
 * @package         Think1st
 * @subpackage      Routes
 * @author          Gerhard Richard Edens <richard.edens@think1st.nl>
 */
class Register
{

    /**
     * @var \Think1st\TemplateEngine\TwigEngine
     */
    public $twig;

    /**
     * @var string
     */
    public $BASE_PATH;

    /**
     * The constructor will create a 
     */
    public function __construct($BASE_PATH)
    {
        // Set basepath
        $this->BASE_PATH = $BASE_PATH;
        // Initialize template engine
        $this->twig = new \Think1st\TemplateEngine\TwigEngine();
        $this->twig->BASE_PATH = $BASE_PATH;
    }

    /**
     * @return string
     */
    public function route()
    {

        // Setup database connection
        $db = new \Think1st\Db\Connection();
        $link = $db->start();

        // Define variables and initialize with empty values
        $username = $password = $confirm_password = $company = "";
        $username_err = $password_err = $confirm_password_err = $company_err = false;

        // Processing form data when form is submitted
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            $error = true;

            // Validate username
            if (empty(trim($_POST["username"]))) {
                $username_err = "Please enter a username.";
            } else {
                // Prepare a select statement
                $sql = "SELECT id FROM users WHERE username = ?";

                if ($stmt = mysqli_prepare($link, $sql)) {
                    // Bind variables to the prepared statement as parameters
                    mysqli_stmt_bind_param($stmt, "s", $param_username);

                    // Set parameters
                    $param_username = trim($_POST["username"]);

                    // Attempt to execute the prepared statement
                    if (mysqli_stmt_execute($stmt)) {
                        /* store result */
                        mysqli_stmt_store_result($stmt);

                        if (mysqli_stmt_num_rows($stmt) == 1) {
                            $username_err = "This username is already taken.";
                        } else {
                            $username = trim($_POST["username"]);
                        }
                    } else {
                        echo "Oops! Something went wrong. Please try again later.";
                    }

                    // Close statement
                    mysqli_stmt_close($stmt);
                }
            }

            // Validate password
            if (empty(trim($_POST["password"]))) {
                $password_err = "Please enter a password.";
            } elseif (strlen(trim($_POST["password"])) < 6) {
                $password_err = "Password must have atleast 6 characters.";
            } else {
                $password = trim($_POST["password"]);
            }

            // Validate company
            if (empty(trim($_POST["company"]))) {
                $company_err = "Please enter a company.";
            } elseif (strlen(trim($_POST["company"])) < 3) {
                $company_err = "Company name must have atleast 3 characters.";
            } else {
                $company = trim($_POST["company"]);
            }

            // Validate confirm password
            if (empty(trim($_POST["confirm_password"]))) {
                $confirm_password_err = "Please confirm password.";
            } else {
                $confirm_password = trim($_POST["confirm_password"]);
                if (empty($password_err) && ($password != $confirm_password)) {
                    $confirm_password_err = "Password did not match.";
                }
            }

            // Check input errors before inserting in database
            if (empty($company_err) && empty($username_err) && empty($password_err) && empty($confirm_password_err)) {

                // Prepare an insert statement
                $sql = "INSERT INTO users (username, password, company) VALUES (?, ?, ?)";

                if ($stmt = mysqli_prepare($link, $sql)) {
                    // Bind variables to the prepared statement as parameters
                    mysqli_stmt_bind_param($stmt, "sss", $param_username, $param_password, $param_company);

                    // Set parameters
                    $param_company = $company;
                    $param_username = $username;
                    $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash

                    // Attempt to execute the prepared statement
                    if (mysqli_stmt_execute($stmt)) {
                        // Redirect to login page
                        header("location: login.php");
                    } else {
                        echo "Something went wrong. Please try again later.";
                    }

                    // Close statement
                    mysqli_stmt_close($stmt);
                }
            }

            // Close connection
            mysqli_close($link);

            if ($error) {
                echo $this->render($username, $username_err, $password, $password_err, $company, $company_err, $confirm_password_err, $confirm_password);
            }
        } else {
            echo $this->render($username, $username_err, $password, $password_err, $company, $company_err, $confirm_password_err, $confirm_password);
        }
    }

    /**
     * @param string username
     * @param string username_err
     * @param string password
     * @param string password_err
     * @param string company
     * @param string company_err
     * @return string
     */
    private function render($username, $username_err, $password, $password_err, $company, $company_err, $confirm_password_err, $confirm_password)
    {
        return $this->twig->templateEngine($this->BASE_PATH . "/register.twig", [
            "username" => $username,
            "username_err" => $username_err,
            "password" => $password,
            "password_err" => $password_err,
            "company" => $company,
            "company_err" => $company_err,
            "confirm_password" => $confirm_password,
            "confirm_password_err" => $confirm_password_err,
            "title" => "Think1st - Content Management System"
        ]);
    }
}
