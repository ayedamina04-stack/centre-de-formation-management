<?php
// Connexion
$con = mysqli_connect('localhost', 'root', '', 'centredeformations');
if (!$con) {
    die("Échec de connexion : " . mysqli_connect_error());
}

// Récupération et nettoyage des données
$nom      = isset($_POST['nom'])      ? trim($_POST['nom'])      : '';
$prenom   = isset($_POST['prenom'])   ? trim($_POST['prenom'])   : '';
$mail     = isset($_POST['mail'])     ? trim($_POST['mail'])     : '';
$tel      = isset($_POST['tel'])      ? trim($_POST['tel'])      : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';
$role     = isset($_POST['select'])   ? trim($_POST['select'])   : '';

// Vérification des champs obligatoires
if (empty($nom) || empty($prenom) || empty($mail) || empty($tel) || empty($password) || empty($role)) {
    echo "<script>alert('Veuillez remplir tous les champs ⚠️'); window.history.back();</script>";
    exit;
}

// Vérification email valide
if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
    echo "<script>alert('Email invalide ⚠️'); window.history.back();</script>";
    exit;
}

// Vérification si l'email existe déjà
$stmtCheck = mysqli_prepare($con, "SELECT id_user FROM utilisateur WHERE email = ?");
mysqli_stmt_bind_param($stmtCheck, "s", $mail);
mysqli_stmt_execute($stmtCheck);
mysqli_stmt_store_result($stmtCheck);

if (mysqli_stmt_num_rows($stmtCheck) > 0) {
    echo "<script>alert('Cet email est déjà utilisé ❌'); window.history.back();</script>";
    mysqli_stmt_close($stmtCheck);
    exit;
}
mysqli_stmt_close($stmtCheck);

// Insertion dans la table utilisateur (requête préparée)
$stmt = mysqli_prepare($con, "INSERT INTO utilisateur (nom, prenom, tel, email, mot_de_passe, role)
                               VALUES (?, ?, ?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, "ssssss", $nom, $prenom, $tel, $mail, $password, $role);

if (mysqli_stmt_execute($stmt)) {

    // Récupérer l'id du dernier utilisateur inséré
    $id_user = mysqli_insert_id($con);
    mysqli_stmt_close($stmt);

    // Si le rôle est participant → insertion dans table compte
    if ($role === 'participant') {
        $stmtCompte = mysqli_prepare($con, "INSERT INTO compte (statut, id_user) VALUES ('actif', ?)");
        mysqli_stmt_bind_param($stmtCompte, "i", $id_user);

        if (!mysqli_stmt_execute($stmtCompte)) {
            echo "<script>alert('Erreur insertion compte participant ❌'); window.history.back();</script>";
            exit;
        }
        mysqli_stmt_close($stmtCompte);
    }

    // Si le rôle est formateur → insertion dans table formateur
    if ($role === 'formateur') {
        $specialite = isset($_POST['specialite']) ? trim($_POST['specialite']) : '';

        if (empty($specialite)) {
            echo "<script>alert('Veuillez choisir une spécialité ⚠️'); window.history.back();</script>";
            exit;
        }

        // ✅ Correction : mysqli_query() manquait + requête préparée
        $stmtFormateur = mysqli_prepare($con, "INSERT INTO formateur (id_formateur, specialite) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmtFormateur, "is", $id_user, $specialite);

        if (!mysqli_stmt_execute($stmtFormateur)) {
            echo "<script>alert('Erreur insertion formateur ❌'); window.history.back();</script>";
            exit;
        }
        mysqli_stmt_close($stmtFormateur);
    }

    // Redirection selon le rôle
    if ($role === 'admin') {
        header("Location: admin.html");
        exit;
    } elseif ($role === 'formateur') {
        header("Location: gestionFormateur.html");
        exit;
    } else {
        header("Location: service.html");
        exit;
    }

} else {
    echo "<script>alert('Erreur insertion utilisateur : " . mysqli_stmt_error($stmt) . " ❌'); window.history.back();</script>";
    mysqli_stmt_close($stmt);
}

mysqli_close($con);
?>