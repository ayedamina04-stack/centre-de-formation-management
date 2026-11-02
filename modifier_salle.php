<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Salles</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f8fb;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        h2 {
            color: #0d6efd;
            margin-top: 20px;
        }

        h3 {
            color: green;
        }

        /* ── Messages ── */
        .message-succes {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            padding: 10px 15px;
            border-radius: 6px;
            margin-bottom: 15px;
            text-align: center;
            width: 70%;
        }

        .message-erreur {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 10px 15px;
            border-radius: 6px;
            margin-bottom: 15px;
            text-align: center;
            width: 70%;
        }

        /* ── Tableau ── */
        table {
            margin-top: 20px;
            border-collapse: collapse;
            width: 70%;
            background: white;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }

        th {
            background-color: #0d6efd;
            color: white;
        }

        tr:hover td {
            background-color: #f0f4ff;
        }

        /* Badge disponibilité */
        .badge-oui {
            background-color: #d4edda;
            color: #155724;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: bold;
        }

        .badge-non {
            background-color: #f8d7da;
            color: #721c24;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: bold;
        }

        /* Badge type salle */
        .badge-type {
            background-color: #e8f0fe;
            color: #1a56db;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 13px;
        }

        /* ── Boutons tableau ── */
        .btn-modifier {
            background-color: #0d6efd;
            color: white;
            border: none;
            padding: 6px 14px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 13px;
            font-weight: bold;
        }

        .btn-modifier:hover {
            background-color: #0b5ed7;
        }

        /* ── Formulaire de modification ── */
        form {
            background: white;
            padding: 15px;
            margin: 10px;
            width: 360px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0,0,0,0.1);
        }

        input, select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        input[readonly] {
            background-color: #e9ecef;
            color: #666;
            cursor: not-allowed;
        }

        .btn-valider {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            border: none;
            color: white;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-valider:hover {
            background-color: #218838;
        }

        .btn-annuler {
            width: 100%;
            padding: 10px;
            background-color: #6c757d;
            border: none;
            color: white;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 5px;
        }

        .btn-annuler:hover {
            background-color: #5a6268;
        }

        .aucun {
            text-align: center;
            color: #888;
            padding: 30px;
            font-size: 15px;
        }

        hr {
            width: 60%;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>

<?php
// ════════════════════════════
// 1. CONNEXION
// ════════════════════════════
$con = mysqli_connect("localhost", "root", "", "centredeformations");

if (!$con) {
    die("<div class='message-erreur'>Erreur de connexion : " . mysqli_connect_error() . "</div>");
}

$message       = "";
$salle_modif   = null;   // Salle à afficher dans le formulaire

// ════════════════════════════
// 2. MODIFICATION (soumission du formulaire modif)
// ════════════════════════════
if (isset($_POST['valider_modif'])) {

    $ref   = $_POST['reference'];
    $nom   = $_POST['nom'];
    $type  = $_POST['type_salle'];
    $capa  = $_POST['capacite'];
    $dispo = $_POST['disponibilite'];

    $sql = "UPDATE salle SET
                nom           = '$nom',
                type_salle    = '$type',
                capacite      = '$capa',
                disponibilite = '$dispo'
            WHERE reference = '$ref'";

    $result = mysqli_query($con, $sql);

    if ($result && mysqli_affected_rows($con) > 0) {
        $message = "<div class='message-succes'>✔ Salle <strong>" . htmlspecialchars($ref) . "</strong> modifiée avec succès.</div>";
    } elseif ($result) {
        $message = "<div class='message-erreur'>⚠ Aucune modification effectuée (données identiques).</div>";
    } else {
        $message = "<div class='message-erreur'>✖ Erreur lors de la modification.</div>";
    }
}

// ════════════════════════════
// 3. SÉLECTION D'UNE SALLE à modifier
// ════════════════════════════
if (isset($_POST['modifier']) && !empty($_POST['reference'])) {

    $ref = $_POST['reference'];

    $sql    = "SELECT * FROM salle WHERE reference = '$ref'";
    $result = mysqli_query($con, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $salle_modif = mysqli_fetch_assoc($result);
    } else {
        $message = "<div class='message-erreur'>✖ Salle introuvable.</div>";
    }
}

// ════════════════════════════
// 4. RÉCUPÉRATION de toutes les salles
// ════════════════════════════
$sql_liste = "SELECT * FROM salle ORDER BY nom ASC";
$liste     = mysqli_query($con, $sql_liste);
?>

<!-- ════ TITRE ════ -->
<h2>Modifier une salle</h2>

<!-- Message succès / erreur -->
<?= $message ?>

<!-- ════ LISTE DES SALLES ════ -->
<h2>Liste des salles</h2>

<?php if (!$liste || mysqli_num_rows($liste) == 0): ?>

    <p class="aucun">Aucune salle disponible dans la base de données.</p>

<?php else: ?>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Référence</th>
                <th>Nom</th>
                <th>Type</th>
                <th>Capacité</th>
                <th>Disponibilité</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = mysqli_fetch_assoc($liste)): ?>

            <?php
                $ref   = htmlspecialchars($row['reference']);
                $nom   = htmlspecialchars($row['nom']);
                $type  = htmlspecialchars($row['type_salle']);
                $capa  = htmlspecialchars($row['capacite']);
                $dispo = $row['disponibilite'];

                $badge_dispo = ($dispo == 'oui')
                    ? "<span class='badge-oui'>Disponible</span>"
                    : "<span class='badge-non'>Indisponible</span>";

                $badge_type = "<span class='badge-type'>" . ucfirst($type) . "</span>";
            ?>

            <tr>
                <td><?= htmlspecialchars($row['id_salle']) ?></td>
                <td><?= $ref ?></td>
                <td><?= $nom ?></td>
                <td><?= $badge_type ?></td>
                <td><?= $capa ?> places</td>
                <td><?= $badge_dispo ?></td>
                <td>
                    <form method="post">
                        <input type="hidden" name="reference" value="<?= $ref ?>">
                        <button type="submit" name="modifier" class="btn-modifier">
                            ✏ Modifier
                        </button>
                    </form>
                </td>
            </tr>

        <?php endwhile; ?>
        </tbody>
    </table>

<?php endif; ?>

<!-- ════ FORMULAIRE DE MODIFICATION ════ -->
<?php if ($salle_modif): ?>

    <hr>

    <h3>Modifier : <?= htmlspecialchars($salle_modif['nom']) ?></h3>

    <form method="post">

        Référence :
        <input type="text"
               name="reference"
               value="<?= htmlspecialchars($salle_modif['reference']) ?>"
               readonly>

        Nom :
        <input type="text"
               name="nom"
               value="<?= htmlspecialchars($salle_modif['nom']) ?>"
               required>

        Type de salle :
        <select name="type_salle">
            <option value="classique"
                <?= $salle_modif['type_salle'] == 'classique'    ? 'selected' : '' ?>>
                Classique
            </option>
            <option value="informatique"
                <?= $salle_modif['type_salle'] == 'informatique' ? 'selected' : '' ?>>
                Informatique
            </option>
            <option value="conference"
                <?= $salle_modif['type_salle'] == 'conference'   ? 'selected' : '' ?>>
                Conférence
            </option>
        </select>

        Capacité (places) :
        <input type="number"
               name="capacite"
               value="<?= htmlspecialchars($salle_modif['capacite']) ?>"
               min="1"
               required>

        Disponibilité :
        <select name="disponibilite">
            <option value="oui"
                <?= $salle_modif['disponibilite'] == 'oui' ? 'selected' : '' ?>>
                Disponible
            </option>
            <option value="non"
                <?= $salle_modif['disponibilite'] == 'non' ? 'selected' : '' ?>>
                Indisponible
            </option>
        </select>

        <button type="submit" name="valider_modif" class="btn-valider">
            ✔ Enregistrer les modifications
        </button>

    </form>

    <form method="get">
        <button type="submit" class="btn-annuler">✖ Annuler</button>
    </form>

<?php endif; ?>

<?php mysqli_close($con); ?>

</body>
</html>