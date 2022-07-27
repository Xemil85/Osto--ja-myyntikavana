<?php
// Virheentarkistus rivit 3-5 on kommentoitava toimivassa osto ja myynti kanavassa
//ini_set('display_errors','1');
//ini_set('display_startup_errors','1');
//error_reporting(E_ALL);
?>
<?php
// uusi sessio joka tallentaa käytetyt muuttujat itseensä
session_start();

// Yhteys tietokantaan 
include("kantayhteys.php");

// tarkistetaan mistä lomakkeesta tieto tulee
$sivu = $_POST['lomaketunnistin'];

$kayttaja_tunnus = mysqli_real_escape_string($dbconnect, $_POST['kayttaja_tunnus']);
$kayttaja_salasana = mysqli_real_escape_string($dbconnect, $_POST['kayttaja_salasana']);
//$kayttaja_salasana = md5($kayttaja_salasana);

// Rekisteröinti
if ($sivu == 0) {
    $varmistus = $_POST['varmistus'];
    $kayttaja_sahkoposti = mysqli_real_escape_string($dbconnect, $_POST['kayttaja_sahkoposti']);

    if (empty($kayttaja_tunnus) || empty($kayttaja_salasana) || empty($kayttaja_sahkoposti) || $varmistus !== 'kuusi') {
        die("Jätit tietoja täyttämättä. Ole hyvä ja <a href='rekisterointi.html'>täytä lomake uudestaan</a>.");
    } else {
       echo "Rekisteröinti onnistui! <a href='kirjautuminen.html'>Kirjaudu sisälle</a> palveluun."; 
    }

    $query = mysqli_query($dbconnect, "SELECT * FROM kayttajat WHERE kayttaja_tunnus = '$kayttaja_tunnus'"); 
    $kayttaja_salasana = password_hash($kayttaja_salasana, PASSWORD_BCRYPT);

    if(mysqli_num_rows($query) !== 0) { 
        echo "Tunnus on jo käytössä! <a href='rekisterointi.html'>Kokeile uudestaan</a>."; 
    } else {
        $query = mysqli_query($dbconnect, "INSERT INTO kayttajat (`kayttaja_tunnus`, `kayttaja_salasana`, `kayttaja_sahkoposti`) VALUES('$kayttaja_tunnus', '$kayttaja_salasana', '$kayttaja_sahkoposti')");  
    } 
}

// Kirjautuminen
if ($sivu == 1) {
    $query = mysqli_query($dbconnect, "SELECT * FROM kayttajat WHERE kayttaja_tunnus = '$kayttaja_tunnus'");

    $tiedot = mysqli_fetch_array($query) or die(mysqli_error($dbconnect));

    if (mysqli_num_rows($query) == 0) {
        echo "Kirjautuminen ei onnistunut. Joko kirjoitit tiedot väärin tai et ole <a href='rekisterointi.html'>rekisteröitynyt</a> palvelun käyttäjäksi. Kokeile <a href='kirjautuminen.html'>uudestaan</a>.";
    }

    if (mysqli_num_rows($query) !== 0 && password_verify($kayttaja_salasana, $tiedot['kayttaja_salasana'])) {
        echo "Kirjautuminen onnistui! <br> <a href='index.php'>Siirry palveluun</a>."; 

        $_SESSION["kayttaja_id"] = $tiedot['kayttaja_id']; 
        $_SESSION["kayttaja_taso"] = $tiedot['kayttaja_taso']; 
        $_SESSION["kayttaja_tunnus"] = $tiedot['kayttaja_tunnus']; 
        $_SESSION["kayttaja_salasana"] = $tiedot['kayttaja_salasana']; 
        $_SESSION["kayttaja_sahkoposti"] = $tiedot['kayttaja_sahkoposti']; 
        $_SESSION['LOGGEDIN'] = 1; 
    }
}

// Käyttäjätietojen muuttaminen
if ($sivu == 2) {
    $kayttaja_uusisalasana = $_POST['kayttaja_uusisalasana'];
    //$kayttaja_uusisalasana = md5($kayttaja_uusisalasana);
    
    function vaihdaSahkoposti() {
        $kayttaja_uusisahkoposti = $_POST['kayttaja_uusisahkoposti'];
        global $kayttaja_tunnus;
        global $dbconnect; 

        if (!empty($kayttaja_uusisahkoposti)) {
            $query = mysqli_query($dbconnect, "UPDATE kayttajat SET kayttaja_sahkoposti='$kayttaja_uusisahkoposti' WHERE kayttaja_tunnus = '$kayttaja_tunnus'");
            $_SESSION["kayttaja_sahkoposti"] = $kayttaja_uusisahkoposti;
        } else {
            echo "Jätit kentän tyhjäksi. Kokeile <a href='tiedot.php'>uudestaan</a>.";
        }
    }
    $query = mysqli_query($dbconnect, "SELECT * FROM kayttajat WHERE kayttaja_tunnus = '$kayttaja_tunnus'"); 

    $tiedot = mysqli_fetch_array($query) or die(mysqli_error($dbconnect)); 

    if (empty($kayttaja_salasana)) {
        vaihdaSahkoposti();

        echo "Tietojen muutos onnistui. <br> <a href='index.php'>Palaa etusivulle</a>."; 
    } else {
        if (!password_verify($kayttaja_salasana, $tiedot['kayttaja_salasana']) || empty($kayttaja_uusisalasana)) {
        echo "Syötit väärän salasanan tai jätit tietoja täyttämättä. Kokeile <a href='tiedot.php'>uudestaan</a>."; 
    } else {
        //$kayttaja_uusisalasana = md5($kayttaja_uusisalasana);
        $kayttaja_uusisalasana = password_hash($kayttaja_uusisalasana, PASSWORD_BCRYPT);
        $query= mysqli_query($dbconnect, "UPDATE kayttajat SET kayttaja_salasana = '$kayttaja_uusisalasana' WHERE kayttaja_tunnus = '$kayttaja_tunnus'"); 
        vaihdaSahkoposti();

        echo "Tietojen muutos onnistui. <br> <a href='index.php'>Palaa etusivulle</a>."; 
    }
}
}

mysqli_close($dbconnect);
?>
