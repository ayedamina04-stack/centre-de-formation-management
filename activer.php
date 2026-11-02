<?php
// Configuration de la base de données
require_once __DIR__ . '/db_pdo.php';


// Traitement des actions AJAX / POST

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');

    $action  = $_POST['action'];
    $id_user = intval($_POST['id_user'] ?? 0);
    $type    = $_POST['type'] ?? ''; // 'compte' ou 'formateur'

    if (!$id_user) {
        echo json_encode(['success' => false, 'message' => 'ID utilisateur invalide.']);
        exit;
    }

    try {
        if ($action === 'toggle_compte') {
            // Lire le statut actuel du compte
            $stmt = $pdo->prepare("SELECT statut FROM compte WHERE id_user = ?");
            $stmt->execute([$id_user]);
            $compte = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$compte) {
                echo json_encode(['success' => false, 'message' => 'Compte introuvable.']);
                exit;
            }

            $nouveau_statut = ($compte['statut'] === 'actif') ? 'desactive' : 'actif';
            $update = $pdo->prepare("UPDATE compte SET statut = ? WHERE id_user = ?");
            $update->execute([$nouveau_statut, $id_user]);

            echo json_encode([
                'success'        => true,
                'nouveau_statut' => $nouveau_statut,
                'message'        => "Compte " . ($nouveau_statut === 'actif' ? 'activé' : 'désactivé') . " avec succès."
            ]);
            exit;
        }

        if ($action === 'toggle_formateur') {
            // Lire l'état actuel du formateur
            $stmt = $pdo->prepare("SELECT etat FROM formateur WHERE id_formateur = ?");
            $stmt->execute([$id_user]);
            $formateur = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$formateur) {
                echo json_encode(['success' => false, 'message' => 'Formateur introuvable.']);
                exit;
            }

            $nouvel_etat = ($formateur['etat'] == 1) ? 0 : 1;
            $update = $pdo->prepare("UPDATE formateur SET etat = ? WHERE id_formateur = ?");
            $update->execute([$nouvel_etat, $id_user]);

            echo json_encode([
                'success'      => true,
                'nouvel_etat'  => $nouvel_etat,
                'message'      => "Formateur " . ($nouvel_etat == 1 ? 'activé' : 'désactivé') . " avec succès."
            ]);
            exit;
        }

        echo json_encode(['success' => false, 'message' => 'Action inconnue.']);

    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Erreur base de données : ' . $e->getMessage()]);
    }
    exit;
}

// =====================================================
// Récupération de la liste des utilisateurs
// =====================================================

// Filtre de recherche
$search = trim($_GET['search'] ?? '');
$filtre_type = $_GET['filtre_type'] ?? 'tous'; // tous | compte | formateur

$whereClause = '';
$params = [];

if ($search !== '') {
    $whereClause = "WHERE (u.nom LIKE ? OR u.prenom LIKE ? OR u.email LIKE ?)";
    $params = ["%$search%", "%$search%", "%$search%"];
}

// Requête principale : jointure avec compte et formateur (LEFT JOIN pour récupérer tous les utilisateurs)
$sql = "
    SELECT
        u.id_user,
        u.nom,
        u.prenom,
        u.email,
        c.statut       AS compte_statut,
        f.id_formateur,
        f.etat         AS formateur_etat,
        CASE
            WHEN f.id_formateur IS NOT NULL THEN 'Formateur'
            ELSE 'Utilisateur'
        END AS role
    FROM utilisateur u
    LEFT JOIN compte    c ON c.id_user       = u.id_user
    LEFT JOIN formateur f ON f.id_formateur  = u.id_user
    $whereClause
    ORDER BY u.nom, u.prenom
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$utilisateurs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Filtrer par type côté PHP (formateur / compte simple)
if ($filtre_type === 'formateur') {
    $utilisateurs = array_filter($utilisateurs, fn($u) => $u['id_formateur'] !== null);
} elseif ($filtre_type === 'utilisateur') {
    $utilisateurs = array_filter($utilisateurs, fn($u) => $u['id_formateur'] === null);
}

$total = count($utilisateurs);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Utilisateurs</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f4f6fb;
            color: #222;
            font-size: 15px;
        }

        /* ---- En-tête ---- */
        header {
            background: #fff;
            border-bottom: 1px solid #e0e0e0;
            padding: 18px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        header h1 {
            font-size: 20px;
            font-weight: 600;
            color: #1a237e;
        }
        .badge-total {
            background: #e8eaf6;
            color: #3949ab;
            border-radius: 20px;
            padding: 4px 14px;
            font-size: 13px;
            font-weight: 600;
        }

        /* ---- Barre de filtre ---- */
        .toolbar {
            background: #fff;
            border-bottom: 1px solid #e0e0e0;
            padding: 14px 32px;
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
        }
        .toolbar form { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; width: 100%; }
        .toolbar input[type="text"] {
            border: 1px solid #c5cae9;
            border-radius: 8px;
            padding: 8px 14px;
            font-size: 14px;
            outline: none;
            flex: 1;
            min-width: 180px;
            transition: border 0.2s;
        }
        .toolbar input[type="text"]:focus { border-color: #3949ab; }
        .toolbar select {
            border: 1px solid #c5cae9;
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 14px;
            outline: none;
            background: #fff;
            cursor: pointer;
        }
        .btn {
            padding: 8px 20px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: background 0.2s, transform 0.1s;
        }
        .btn:active { transform: scale(0.97); }
        .btn-primary   { background: #3949ab; color: #fff; }
        .btn-primary:hover { background: #283593; }
        .btn-secondary { background: #e8eaf6; color: #3949ab; }
        .btn-secondary:hover { background: #c5cae9; }

        /* ---- Conteneur principal ---- */
        main { padding: 28px 32px; }

        /* ---- Toast notification ---- */
        #toast {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 22px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            display: none;
            z-index: 9999;
            box-shadow: 0 4px 16px rgba(0,0,0,0.12);
        }
        #toast.success { background: #e8f5e9; color: #2e7d32; border-left: 4px solid #43a047; }
        #toast.error   { background: #ffebee; color: #c62828; border-left: 4px solid #e53935; }

        /* ---- Table ---- */
        .table-wrapper {
            background: #fff;
            border-radius: 12px;
            border: 1px solid #e0e0e0;
            overflow: hidden;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        thead {
            background: #e8eaf6;
        }
        thead th {
            padding: 13px 16px;
            text-align: left;
            font-weight: 600;
            color: #3949ab;
            border-bottom: 1px solid #c5cae9;
            white-space: nowrap;
        }
        tbody tr {
            border-bottom: 1px solid #f0f0f0;
            transition: background 0.15s;
        }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: #f5f6ff; }
        tbody td {
            padding: 12px 16px;
            vertical-align: middle;
            color: #333;
        }

        /* ---- Avatar ---- */
        .avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #c5cae9;
            color: #283593;
            font-weight: 700;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 8px;
            vertical-align: middle;
        }
        .user-name {
            vertical-align: middle;
            font-weight: 500;
        }

        /* ---- Badges de rôle ---- */
        .role-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .role-formateur { background: #e3f2fd; color: #1565c0; }
        .role-user      { background: #f3e5f5; color: #6a1b9a; }

        /* ---- Toggle switches ---- */
        .switch {
            position: relative;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }
        .switch input { display: none; }
        .slider {
            width: 44px;
            height: 24px;
            background: #ccc;
            border-radius: 24px;
            position: relative;
            transition: background 0.3s;
            flex-shrink: 0;
        }
        .slider::before {
            content: '';
            position: absolute;
            top: 3px;
            left: 3px;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: #fff;
            transition: transform 0.3s;
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }
        input:checked + .slider { background: #43a047; }
        input:checked + .slider::before { transform: translateX(20px); }

        .switch-label {
            font-size: 12px;
            font-weight: 500;
            color: #555;
            white-space: nowrap;
        }
        .switch-label.on  { color: #2e7d32; }
        .switch-label.off { color: #c62828; }

        /* ---- Pas de résultats ---- */
        .no-result {
            text-align: center;
            padding: 48px;
            color: #999;
        }
        .no-result .icon { font-size: 40px; margin-bottom: 12px; }

        /* ---- Colonne N/A ---- */
        .na-badge {
            color: #bbb;
            font-size: 12px;
            font-style: italic;
        }

        @media (max-width: 768px) {
            main, .toolbar, header { padding: 14px 12px; }
            table { font-size: 12px; }
            thead th, tbody td { padding: 8px 8px; }
        }
    </style>
</head>
<body>

<!-- Toast -->
<div id="toast"></div>

<!-- En-tête -->
<header>
    <h1>&#128100; Gestion des Utilisateurs</h1>
    <span class="badge-total"><?= $total ?> utilisateur<?= $total > 1 ? 's' : '' ?></span>
</header>

<!-- Barre de recherche & filtres -->
<div class="toolbar">
    <form method="GET" action="">
        <input
            type="text"
            name="search"
            placeholder="Rechercher par nom, prénom ou email..."
            value="<?= htmlspecialchars($search) ?>"
        >
        <select name="filtre_type">
            <option value="tous"        <?= $filtre_type === 'tous'        ? 'selected' : '' ?>>Tous</option>
            <option value="formateur"   <?= $filtre_type === 'formateur'   ? 'selected' : '' ?>>Formateurs</option>
            <option value="utilisateur" <?= $filtre_type === 'utilisateur' ? 'selected' : '' ?>>Utilisateurs simples</option>
        </select>
        <button type="submit" class="btn btn-primary">&#128269; Rechercher</button>
        <?php if ($search || $filtre_type !== 'tous'): ?>
            <a href="?" class="btn btn-secondary">&#10005; Réinitialiser</a>
        <?php endif; ?>
    </form>
</div>

<!-- Tableau principal -->
<main>
    <div class="table-wrapper">
        <?php if (empty($utilisateurs)): ?>
            <div class="no-result">
                <div class="icon">&#128204;</div>
                <p>Aucun utilisateur trouvé.</p>
            </div>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Utilisateur</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Compte (statut)</th>
                    <th>Formateur (état)</th>
                </tr>
            </thead>
            <tbody>
            <?php $i = 1; foreach ($utilisateurs as $u): ?>
            <?php
                $initiales = strtoupper(mb_substr($u['prenom'], 0, 1) . mb_substr($u['nom'], 0, 1));
                $est_actif_compte    = ($u['compte_statut'] === 'actif');
                $est_actif_formateur = ($u['formateur_etat'] == 1);
                $est_formateur       = ($u['id_formateur'] !== null);
            ?>
                <tr id="row-<?= $u['id_user'] ?>">
                    <td style="color:#aaa; font-size:13px;"><?= $i++ ?></td>
                    <td>
                        <span class="avatar"><?= htmlspecialchars($initiales) ?></span>
                        <span class="user-name">
                            <?= htmlspecialchars($u['prenom'] . ' ' . $u['nom']) ?>
                        </span>
                    </td>
                    <td style="color:#555;"><?= htmlspecialchars($u['email']) ?></td>
                    <td>
                        <?php if ($est_formateur): ?>
                            <span class="role-badge role-formateur">Formateur</span>
                        <?php else: ?>
                            <span class="role-badge role-user">Utilisateur</span>
                        <?php endif; ?>
                    </td>

                    <!-- Toggle Compte -->
                    <td>
                        <?php if ($u['compte_statut'] !== null): ?>
                        <label class="switch">
                            <input
                                type="checkbox"
                                <?= $est_actif_compte ? 'checked' : '' ?>
                                onchange="toggleStatut(this, <?= $u['id_user'] ?>, 'compte')"
                            >
                            <span class="slider"></span>
                            <span class="switch-label <?= $est_actif_compte ? 'on' : 'off' ?>" id="label-compte-<?= $u['id_user'] ?>">
                                <?= $est_actif_compte ? 'Actif' : 'Désactivé' ?>
                            </span>
                        </label>
                        <?php else: ?>
                            <span class="na-badge">— Aucun compte</span>
                        <?php endif; ?>
                    </td>

                    <!-- Toggle Formateur -->
                    <td>
                        <?php if ($est_formateur): ?>
                        <label class="switch">
                            <input
                                type="checkbox"
                                <?= $est_actif_formateur ? 'checked' : '' ?>
                                onchange="toggleStatut(this, <?= $u['id_user'] ?>, 'formateur')"
                            >
                            <span class="slider"></span>
                            <span class="switch-label <?= $est_actif_formateur ? 'on' : 'off' ?>" id="label-formateur-<?= $u['id_user'] ?>">
                                <?= $est_actif_formateur ? 'Actif' : 'Inactif' ?>
                            </span>
                        </label>
                        <?php else: ?>
                            <span class="na-badge">— Non formateur</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</main>

<script>
// Affiche un toast (notification)
function showToast(msg, type = 'success') {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.className = type;
    t.style.display = 'block';
    clearTimeout(t._timer);
    t._timer = setTimeout(() => { t.style.display = 'none'; }, 3500);
}

// Gestion toggle (compte ou formateur)
function toggleStatut(checkbox, idUser, type) {
    const action = (type === 'compte') ? 'toggle_compte' : 'toggle_formateur';

    const formData = new FormData();
    formData.append('action', action);
    formData.append('id_user', idUser);

    // Désactiver pendant la requête
    checkbox.disabled = true;

    fetch(window.location.pathname, {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        checkbox.disabled = false;
        if (data.success) {
            // Mettre à jour le label
            const labelId = 'label-' + type + '-' + idUser;
            const label = document.getElementById(labelId);
            if (label) {
                if (type === 'compte') {
                    const actif = data.nouveau_statut === 'actif';
                    label.textContent = actif ? 'Actif' : 'Désactivé';
                    label.className = 'switch-label ' + (actif ? 'on' : 'off');
                } else {
                    const actif = data.nouvel_etat == 1;
                    label.textContent = actif ? 'Actif' : 'Inactif';
                    label.className = 'switch-label ' + (actif ? 'on' : 'off');
                }
            }
            showToast(data.message, 'success');
        } else {
            // Revenir en arrière en cas d'erreur
            checkbox.checked = !checkbox.checked;
            showToast(data.message || 'Erreur inconnue.', 'error');
        }
    })
    .catch(() => {
        checkbox.disabled = false;
        checkbox.checked = !checkbox.checked;
        showToast('Erreur de connexion au serveur.', 'error');
    });
}
</script>
</body>
</html>