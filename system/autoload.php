<?php

// Database
require_once __DIR__ . "/db/db.php";

// Template engine
require_once __DIR__ . "/core/template/template.php";

// Routes
require_once __DIR__ . "/routes/create-project.php";
require_once __DIR__ . "/routes/login.php";
require_once __DIR__ . "/routes/logout.php";
require_once __DIR__ . "/routes/register.php";
//require_once __DIR__ . "/routes/reset-password.php";

// Security
require_once __DIR__ . "/security/authentication.php";

// WordPress Engine parts
//require_once __DIR__ . '/wordpress/db.php';
//require_once __DIR__ . '/wordpress/menu.php';
//require_once __DIR__ . '/wordpress/posts.php';