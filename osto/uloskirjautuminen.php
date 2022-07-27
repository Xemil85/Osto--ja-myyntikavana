<?php
    //ini_set('display_errors','1');
    //ini_set('display_startup_errors','1');
    //error_reporting(E_ALL);

    session_start();

    include("kantayhteys.php");

    header("Content-Type:text/html;charset=utf-8");

    if ($_SESSION['LOGGEDIN'] == 1) {

        session_unset();

        session_destroy();

        echo "Uloskirjautuminen onnistui! <a href='kirjautuminen.html'>Kirjaudu</a> 
        uudelleen sisään tai <a href='index.php'>palaa etusivulle</a>."; 
    }
?>