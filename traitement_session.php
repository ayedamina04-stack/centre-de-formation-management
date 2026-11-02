<?php
$conn = new mysqli("localhost", "root", "", "centredeformations");

if ($conn->connect_error) {
    die("Erreur connexion");
}

$titre = $_POST['titre'];
$date  = $_POST['date_session'];
$duree = $_POST['duree'];
$id_formateur = $_POST['id_formateur'];

$sql = "INSERT INTO sessions (titre, date_session, duree, id_formateur)
        VALUES ('$titre', '$date', '$duree', '$id_formateur')";

if ($conn->query($sql) === TRUE) {
    echo "Session ajoutée avec succès !";
} else {
    echo "Erreur : " . $conn->error;
}

$conn->close();
?>