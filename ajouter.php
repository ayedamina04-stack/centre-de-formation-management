<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

    <?php
$con=mysqli_connect('localhost','root','') or die("echec de connexion serveur");
mysqli_select_db($con,'centredeformations');
$ref=$_POST['ref'];
$nom        = $_POST['nom'];
$capacite   = $_POST['capacite'];
$type_salle = $_POST['type'];
$dispo      = $_POST['dispo'];

$r = mysqli_query($con,"SELECT * FROM salle WHERE nom='$nom'");

if(mysqli_num_rows($r)!=0){
    echo "<script>alert('La salle $nom existe déjà !'); window.history.back();</script>";
}else{
    $sql=mysqli_query($con,"INSERT INTO salle(reference,nom,capacite,type_salle,disponibilite) 
    VALUES('$ref','$nom','$capacite','$type_salle','$dispo')");

    if($sql){
        echo "<script>alert('La salle $nom a été ajoutée avec succès !'); window.history.back();</script>";
    }else{
        echo "<script>alert('Erreur : ".mysqli_error($con)."'); window.history.back();</script>";
    }
}

mysqli_close($con);
?>
</body>
</html>