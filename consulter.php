<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Consultation comptes</title>

    <style>
        body {
            font-family: Arial;
            background: #f4f8fb;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        h2 {
            color: #0d6efd;
        }

        form {
            background: white;
            padding: 20px;
            margin-top: 20px;
            width: 350px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0,0,0,0.1);
        }

        input, select, button {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            border-radius: 5px;
        }

        input {
            border: 1px solid #ccc;
        }

        button {
            background: #0d6efd;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background: #0b5ed7;
        }

        .result {
            background: white;
            margin-top: 20px;
            padding: 20px;
            width: 350px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0,0,0,0.1);
        }
    </style>
</head>

<body>

<h2>Consulter les comptes</h2>

<form method="post">

    <select name="type_consultation" required>
        <option value="one">Un utilisateur</option>
        <option value="all">Tous les comptes</option>
    </select>

    <input type="number" name="id_user" placeholder="ID utilisateur (si besoin)">

    <button type="submit" name="rechercher">Consulter</button>
</form>

<?php
$con = mysqli_connect("localhost", "root", "", "centredeformations");
if (!$con) die("Erreur connexion");

// =======================
// 🔍 CONSULTATION
// =======================
if (isset($_POST['rechercher'])) {

    $type = $_POST['type_consultation'];

    // =======================
    // CAS 1 : UN UTILISATEUR
    // =======================
    if ($type == "one") {

        $id = $_POST['id_user'];

        $sql = "SELECT 
                    u.nom, u.prenom, u.tel, u.email,
                    c.statut, c.id_user
                FROM compte c
                INNER JOIN utilisateur u ON c.id_user = u.id_user
                WHERE c.id_user = '$id'";

        $result = mysqli_query($con, $sql);

        if (mysqli_num_rows($result) > 0) {

            $user = mysqli_fetch_assoc($result);

            echo "<div class='result'>
                    <h3>Utilisateur</h3>
                    <p><b>ID:</b> {$user['id_user']}</p>
                    <p><b>Nom:</b> {$user['nom']}</p>
                    <p><b>Prénom:</b> {$user['prenom']}</p>
                    <p><b>Tél:</b> {$user['tel']}</p>
                    <p><b>Email:</b> {$user['email']}</p>
                    <p><b>Statut:</b> {$user['statut']}</p>
                  </div>";

        } else {
            echo "<p>Utilisateur introuvable ❌</p>";
        }
    }

    // =======================
    // CAS 2 : TOUS LES COMPTES
    // =======================
    else if ($type == "all") {

        $sql = "SELECT 
                    u.nom, u.prenom, u.tel, u.email,
                    c.statut, c.id_user
                FROM compte c
                INNER JOIN utilisateur u ON c.id_user = u.id_user";

        $result = mysqli_query($con, $sql);

        echo "<div class='result'><h3>Liste des comptes</h3>";

        while ($row = mysqli_fetch_assoc($result)) {

            echo "<hr>
                  <p><b>ID:</b> {$row['id_user']}</p>
                  <p><b>Nom:</b> {$row['nom']}</p>
                  <p><b>Prénom:</b> {$row['prenom']}</p>
                  <p><b>Tél:</b> {$row['tel']}</p>
                  <p><b>Email:</b> {$row['email']}</p>
                  <p><b>Statut:</b> {$row['statut']}</p>";
        }

        echo "</div>";
    }
}
?>

</body>
</html>