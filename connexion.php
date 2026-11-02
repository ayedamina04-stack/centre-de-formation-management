<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
</head>
<body>
<?php
    // Connexion à la base de données
    require_once __DIR__ . '/db.php';

    // Récupération des données POST
    $email = isset($_POST['email'])     ? trim($_POST['email'])     : '';
    $mot   = isset($_POST['password2']) ? trim($_POST['password2']) : '';

    // Vérification que les champs ne sont pas vides
    if (empty($email) || empty($mot)) {
        echo "<script>alert('Veuillez remplir tous les champs ⚠️'); window.history.back();</script>";
        exit;
    }

    // Étape 1 : Vérifier l'utilisateur dans la table utilisateur
    $stmt = mysqli_prepare($con, "SELECT * FROM utilisateur WHERE email = ? AND mot_de_passe = ?");
    mysqli_stmt_bind_param($stmt, "ss", $email, $mot);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($res) == 0) {
        echo "<script>alert('Email ou mot de passe incorrect ❌'); window.history.back();</script>";
        exit;
    }

    $user    = mysqli_fetch_assoc($res);
    $id_user = $user['id_user'];
    $role    = $user['role'];

    mysqli_stmt_close($stmt);

    // Étape 2 : Vérification selon le rôle
    if ($role === 'admin') {
        header("Location: admin.html");
        exit;

    } elseif ($role === 'formateur') {

        $stmt2 = mysqli_prepare($con, "SELECT etat FROM formateur WHERE id_formateur = ?");
        mysqli_stmt_bind_param($stmt2, "i", $id_user);
        mysqli_stmt_execute($stmt2);
        $res2 = mysqli_stmt_get_result($stmt2);

        // ✅ Bug 1 corrigé : == 0 au lieu de == 1
        if (mysqli_num_rows($res2) == 0) {
            echo "<script>alert('Compte formateur introuvable ❌'); window.history.back();</script>";
            exit;
        }

        $formateur = mysqli_fetch_assoc($res2);
        $etat      = $formateur['etat'];
        mysqli_stmt_close($stmt2);

        // ✅ Bug 2 corrigé : etat == 1 signifie bloqué (pas 0)
        if ($etat == 1) {
            echo "<script>alert('Votre compte est bloqué 🚫. Veuillez contacter l\\'administrateur.'); window.history.back();</script>";
            exit;
        }

        header("Location: gestionFormateur.html");
        exit;

    } else {
        // Participant
        $stmt3 = mysqli_prepare($con, "SELECT statut FROM compte WHERE id_user = ?");
        mysqli_stmt_bind_param($stmt3, "i", $id_user);
        mysqli_stmt_execute($stmt3);
        $res3 = mysqli_stmt_get_result($stmt3);

        if (mysqli_num_rows($res3) == 0) {
            echo "<script>alert('Compte participant introuvable ❌'); window.history.back();</script>";
            exit;
        }

        $compte = mysqli_fetch_assoc($res3);
        $statut = $compte['statut'];
        mysqli_stmt_close($stmt3);

        if ($statut === 'desactive') {
            echo "<script>alert('Votre compte est désactivé 🚫. Veuillez contacter l\\'administrateur.'); window.history.back();</script>";
            exit;
        }

        header("Location: service.html");
        exit;
    }

    mysqli_close($con);
?>
</body>
</html>