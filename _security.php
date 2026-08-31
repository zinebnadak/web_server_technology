<?php
// Möjliggör sessions
session_start();

// Skapa sessions-variabler
$_SESSION["loggedin"] = "ok";
$_SESSION["employeecode"] = $employeecode;

// Dödar sessions
unset($_SESSION["loggedin"]);
unset($_SESSION["employeecode"]);

// Dödar alla sessions-variabler
session_destroy();

?>