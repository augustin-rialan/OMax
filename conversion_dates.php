<?php
try
{
    // On se connecte à MySQL
    $bdd = new PDO('mysql:host=localhost;dbname=tgvmax;charset=utf8', 'root', 'donatien');
}
catch(Exception $e)
{
    die('Erreur : '.$e->getMessage());
}

$reponse = $bdd->query('SELECT DATE FROM tgv');

/** while ($donnees = $reponse->fetch()) {

    $originalDate = $donnees['DATE'];
    $newDate = date("d/m/Y", strtotime($originalDate));
    $req=$bdd->prepare('UPDATE tgv SET DATE= :newDate WHERE DATE= :originalDate');
    $req->execute(array(
        'newDate' => $newDate,
        'originalDate' => $originalDate
    ));

    ?> </br> <?php

} **/

if (strtotime('23:01:00')>strtotime('23:00:00')){
    echo 'true';
}
?>


