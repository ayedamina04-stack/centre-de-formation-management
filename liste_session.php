<?php
// Connexion à la base de données
require_once __DIR__ . '/db.php';

// Récupérer les sessions
$sql = "SELECT 
            sessions.id,
            sessions.titre,
            sessions.date_session,
            sessions.duree,
            utilisateur.nom    AS nom_formateur,
            utilisateur.prenom AS prenom_formateur
        FROM sessions
        JOIN formateur   ON sessions.id_formateur  = formateur.id_formateur
        JOIN utilisateur ON formateur.id_formateur = utilisateur.id_user";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="fr">
<!-- ↑ attribut lang="fr" ajouté -->
<head>
    <meta charset="UTF-8">
    <title>Liste des Sessions</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .container {
            width: 80%;
            margin: auto;
        }
        .session {
            background: white;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .session h2 {
            margin: 0;
            color: #007BFF;
        }
        .session p {
            margin: 5px 0;
            color: #555;
        }
        .date {
            font-weight: bold;
            color: #28a745;
        }
        .btn {
            display: inline-block;
            padding: 8px 12px;
            background: #007BFF;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            margin-top: 10px;
        }
        .btn:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>

<h1>Liste des Sessions</h1>
<div style="text-align: center; margin-bottom: 20px;">
    <a href="ajouter_session.php" class="btn">+ Ajouter une session</a>
</div>
<div class="container">

<?php
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $titre  = htmlspecialchars($row["titre"]);
        $date   = htmlspecialchars($row["date_session"]);
        $nom    = htmlspecialchars($row["nom_formateur"]);
        $prenom = htmlspecialchars($row["prenom_formateur"]);
        $duree  = htmlspecialchars($row["duree"]);
        $id     = (int) $row["id"];

        echo '<div class="session">';
        echo '<h2>'                          . $titre  . '</h2>';
        echo '<p class="date">Date : '       . $date   . '</p>';
        echo '<p>Nom Formateur : '           . $nom    . '</p>';
        echo '<p>Prénom Formateur : '        . $prenom . '</p>';
        echo '<p>Durée : '                   . $duree  . '</p>';
        echo '<a href="liste.html"id=' . $id . '" class="btn">Voir détails</a>';
        echo '</div>';
    }
} else {
    echo "<p>Aucune session disponible</p>";
}

$conn->close();
?>

</div>
</body>
</html>