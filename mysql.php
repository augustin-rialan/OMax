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


?>



<?php

$reponse = $bdd->query('SELECT Origine FROM tgv');
$newtab=array();
while ($donnees = $reponse->fetch())
{

      // echo $donnees['Origine'];
       $newtab[]=$donnees['Origine'];

}
$tab=array_unique($newtab);

//foreach( $tab as $value )
 // echo $value . '<br />';


$reponse->closeCursor(); // Termine le traitement de la requête

?>

<form method="post" action="affichage.php">
    <p>
    <select name="origine">

       <?php
       foreach($tab as $values){
           ?>
           <option value="<?php echo $values;?>"><?php echo $values; }?></option>




    </select>
    </p>

    <input name="dateAller" type="date">
    <input name="dateRetour" type="date">
    </br>

    <input type="submit" value="Valider" />
</form>



