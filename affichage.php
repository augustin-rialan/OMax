<?php
try {

    // Connection à MySQL
    $bdd = new PDO('mysql:host=localhost;dbname=tgvmax;charset=utf8', 'root', 'donatien');
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}


//Obtention du tableau des destinations

$reponse = $bdd->query('SELECT Origine FROM tgv');
$newtab = array();
while ($trainAller = $reponse->fetch()) {
    $newtab[] = $trainAller['Origine'];

}

$tab = array_unique($newtab);
foreach ($tab as $value) {
    $tableaudesdestinations[$value] = array(0, 0);
}


//recuperation de la date et conversion pour la BDD
$originalDate = $_POST['dateAller'];
$DateAller = date("Y-m-d", strtotime($originalDate));
$originalDate = $_POST['dateRetour'];
$DateRetour = date("Y-m-d", strtotime($originalDate));


//recuperation de tout les trajets ALLER partant de la ville spécifié
$aller = $bdd->prepare('SELECT * FROM tgv WHERE Origine=:origine AND DATE=:date');
$aller->execute(array(
    'origine' => $_POST['origine'],
    'date' => $DateAller
));


//Boucle numero un pour obtenir tout les trains aller vers les destinations potentielles
while ($trainAller = $aller->fetch()) {
// Mettre une condition on ne passe dessus que si l'heure du train est plus tôt que celle précédente ou si c'est la première entrée pour cette desti



    if ($tableaudesdestinations[$trainAller['Destination']][0] != 0) {
        //On retrouve les horaires du train précedemment enregistré dans la base
        $horaire = $bdd->prepare('SELECT Heure_depart FROM tgv WHERE id=:id');
        $horaire->execute(array(
            'id' => $tableaudesdestinations[$trainAller['Destination']][0],
        ));
        $heure_depart = $horaire->fetch();
        $heure_depart = $heure_depart['Heure_depart'];
        if (strtotime($trainAller['Heure_depart']) < strtotime($heure_depart)) {
            $tableaudesdestinations[$trainAller['Destination']][0] = $trainAller['id'];
        }

    } else {
        $tableaudesdestinations[$trainAller['Destination']][0] = $trainAller['id'];
    }


}

// Termine le traitement de la requête
$aller->closeCursor();

?>


<?php

//On a un train de A vers B, on cherche si il y a un train de B vers A deux jours plus tard
$retour = $bdd->prepare('SELECT * FROM tgv WHERE Destination=:destination AND DATE=:date');
$retour->execute(array(
    'destination' => $_POST['origine'],
    'date' => $DateRetour
));
?>

<?php
//Boucle numero deux pour obtenir tout les trains retour depuis les destinations potentielles
while ($trainRetour = $retour->fetch()) {



        //on compare les horaire de ce train retour avec celui précedemment enregistré dans le tableau, si il y en avait un
        if ($tableaudesdestinations[$trainRetour['Origine']][1] != 0) {
            //On retrouve les horaires du train précedemment enregistré dans la base
            $horaire = $bdd->prepare('SELECT Heure_arrivee FROM tgv WHERE id=:id');
            $horaire->execute(array(
                'id' => $tableaudesdestinations[$trainRetour['Origine']][1],
            ));
            $heure_arrivee = $horaire->fetch();
            $heure_arrivee = $heure_arrivee['Heure_arrivee'];
            if (strtotime($trainRetour['Heure_depart']) > strtotime($heure_arrivee)) {
                $tableaudesdestinations[$trainRetour['Origine']][1] = $trainRetour['id'];

            }
        } else {
            $tableaudesdestinations[$trainRetour['Origine']][1] = $trainRetour['id'];
        }

        ?>
<?php }
?>


<?php

// Termine le traitement de la requête
$retour->closeCursor();

$i=0;
foreach ($tableaudesdestinations as $values) {
    ?> </br> <?php

    if ($values[0] != 0 & $values[1] != 0) {
        $i++;
        echo $i;
        echo "Pour la destination: " . key($tableaudesdestinations) . "<br>";
        $requete = $bdd->prepare('SELECT * FROM tgv WHERE id = :id');
        $requete->execute(array(
            'id' => $values[0],
        ));
        $trainAller = $requete->fetch(); ?>
        <strong>Train</strong> :
        <?php echo $trainAller['TRAIN_NO']; ?>
        <br/>
        Le train vient de <?php echo $trainAller['Origine']; ?>, et est à destination
        de <?php echo $trainAller['Destination']; ?>
        <br/>
        <?php echo "A la date du: ";
        echo $DateAller;
        $requete->closeCursor();
        ?>
        <?php
        $requete = $bdd->prepare('SELECT * FROM tgv WHERE id = :id');
        $requete->execute(array(
            'id' => $values[1],
        ));
        $trainRetour = $requete->fetch();
        ?>
        <strong>Train</strong> : <?php echo $trainRetour['TRAIN_NO']; ?><br/>
        Le train retour vient de <?php echo $trainRetour['Origine']; ?>, et est à destination
        de <?php echo $trainRetour['Destination']; ?> <br/>
        <?php echo "A la date du: ";
        echo $DateRetour;
        $requete->closeCursor();
        ?> </br> <?php

    }
    next($tableaudesdestinations);

}

?>