<?php
$conn = new mysqli("localhost", "root", "", "centredeformations");

if ($conn->connect_error) {
    die("Erreur connexion");
}

// Récupérer les formateurs disponibles
$sql = "SELECT utilisateur.id_user, utilisateur.nom, utilisateur.prenom
        FROM utilisateur
        JOIN formateur ON utilisateur.id_user = formateur.id_formateur";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter Session</title>
<style>
    body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, #eaf3ff, #f7fbff);
    margin: 0;
    padding: 0;
}

/* Header */
.header {
    background: linear-gradient(90deg, #1e88e5, #1565c0);
    color: white;
    text-align: center;
    padding: 18px;
    font-size: 20px;
    font-weight: bold;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

/* Container */
.container {
    width: 420px;
    max-width: 90%;
    margin: 60px auto;
    background: #ffffff;
    padding: 30px;
    border-radius: 16px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    transition: 0.3s ease-in-out;
}

.container:hover {
    transform: translateY(-3px);
}

/* Title */
h2 {
    text-align: center;
    color: #1e88e5;
    margin-bottom: 25px;
    font-size: 22px;
}

/* Labels */
label {
    font-weight: 600;
    color: #333;
    display: block;
    margin-bottom: 5px;
}

/* Inputs & Select */
input, select {
    width: 100%;
    padding: 12px;
    margin-bottom: 18px;
    border: 1px solid #dbe6f3;
    border-radius: 10px;
    font-size: 14px;
    transition: 0.3s;
    background: #fafcff;
}

input:focus, select:focus {
    border-color: #1e88e5;
    box-shadow: 0 0 8px rgba(30,136,229,0.25);
    outline: none;
    background: #fff;
}

/* Button */
button {
    width: 100%;
    padding: 12px;
    background: linear-gradient(90deg, #1e88e5, #1565c0);
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    transition: 0.3s ease;
}

button:hover {
    background: linear-gradient(90deg, #1565c0, #0d47a1);
    transform: scale(1.02);
}

/* Animation entrée */
.container {
    animation: fadeIn 0.6s ease-in-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
    </style>
    
</head>
<body>

<h2>Ajouter une nouvelle session</h2>

<form action="traitement_session.php" method="POST">

    <label>Titre :</label><br>
    <input type="text" name="titre" required><br><br>

    <label>Date :</label><br>
    <input type="date" name="date_session" required><br><br>

    <label>Durée :</label><br>
    <input type="text" name="duree" required><br><br>

    <label>Formateur :</label><br>
    <select name="id_formateur" required>
        <option value="">-- Choisir un formateur --</option>

        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<option value="' . $row["id_user"] . '">'
                     . $row["nom"] . ' ' . $row["prenom"] .
                     '</option>';
            }
        }
        ?>

    </select><br><br>

    <button type="submit">Ajouter</button>
</form>

</body>
</html>

