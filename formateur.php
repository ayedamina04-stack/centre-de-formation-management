<?php
$con = mysqli_connect('localhost', 'root', '', 'centredeformations');
if (!$con) {
    die("Erreur connexion : " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $mail = trim($_POST['email']);
    $tel = trim($_POST['tel']);
    $mot = trim($_POST['mot']);
    $specialite = trim($_POST['specialite']);

    // Vérification obligatoire
   /* if (empty($nom) || empty($prenom) || empty($mail) || empty($tel) || empty($mot) || empty($specialite)) {
        die("Tous les champs sont obligatoires !");
    }*/

    // 🔍 DEBUG (à supprimer après test)
    // var_dump($mail);

    // 🔍 Vérifier email existant
    $check = "SELECT * FROM utilisateur WHERE email='$mail'";
    $result = mysqli_query($con, $check);

    if (mysqli_num_rows($result) > 0) {
        echo "<script>alert('Ce formateur existe déjà'); window.history.back();</script>";
        exit(); 
    }

    

    //  insertion utilisateur
    $sql = "INSERT INTO utilisateur (nom, prenom, tel, email, mot_de_passe, role)
            VALUES ('$nom', '$prenom', '$tel', '$mail', '$mot', 'formateur')";

    if (mysqli_query($con, $sql)) {

        $id = mysqli_insert_id($con);

        //insertion formateur
        $res = "INSERT INTO formateur (id_formateur, specialite)
                VALUES ('$id', '$specialite')";

        if (mysqli_query($con, $res)) {
            echo "<script>alert('Formateur ajouté avec succès'); window.location.href='gestionFormateur.html';</script>";
        } else {
            echo "Erreur formateur : " . mysqli_error($con);
        }

    } else {
        echo "Erreur utilisateur : " . mysqli_error($con);
    }
}
?>