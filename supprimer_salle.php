<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Salles</title>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@400;600;800&family=Instrument+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:            #f5f3ee;
            --surface:       #ffffff;
            --card:          #ffffff;
            --border:        #e2ddd6;
            --border-dark:   #c8c2b8;
            --accent:        #1a1a2e;
            --accent2:       #c84b31;
            --accent2-soft:  #fdf0ed;
            --accent2-border:#f4c5bb;
            --text-primary:  #1a1a2e;
            --text-secondary:#6b6560;
            --text-muted:    #a09b94;
            --success:       #2d6a4f;
            --success-soft:  #eaf4ef;
            --danger:        #c84b31;
            --danger-soft:   #fdf0ed;
            --warning:       #b5830a;
            --warning-soft:  #fdf6e3;
            --tag-1-bg:      #ede9fe; --tag-1-text:#5b21b6;
            --tag-2-bg:      #d1fae5; --tag-2-text:#065f46;
            --tag-3-bg:      #fee2e2; --tag-3-text:#991b1b;
            --tag-4-bg:      #fef3c7; --tag-4-text:#92400e;
            --tag-5-bg:      #dbeafe; --tag-5-text:#1e40af;
        }

        body {
            font-family: 'Instrument Sans', sans-serif;
            background: var(--bg);
            color: var(--text-primary);
            min-height: 100vh;
        }

        /* ─── Header ─── */
        header {
            background: var(--accent);
            padding: 0 48px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 200;
        }

        .logo {
            font-family: 'Bricolage Grotesque', sans-serif;
            font-weight: 800;
            font-size: 18px;
            color: #fff;
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .logo-mark {
            width: 26px; height: 26px;
            background: var(--accent2);
            border-radius: 6px;
            display: flex; align-items: center; justify-content: center;
            font-size: 13px;
        }

        .nav-pill {
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.15);
            color: rgba(255,255,255,0.8);
            font-size: 12px;
            font-weight: 500;
            padding: 5px 14px;
            border-radius: 20px;
            letter-spacing: 0.3px;
        }

        /* ─── Layout ─── */
        main {
            max-width: 1080px;
            margin: 0 auto;
            padding: 48px 40px 80px;
        }

        /* ─── Page heading ─── */
        .page-head {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            margin-bottom: 36px;
            flex-wrap: wrap;
            gap: 16px;
        }

        .page-head h1 {
            font-family: 'Bricolage Grotesque', sans-serif;
            font-weight: 800;
            font-size: 40px;
            letter-spacing: -1.5px;
            line-height: 1;
            color: var(--text-primary);
        }

        .page-head h1 span {
            color: var(--accent2);
        }

        .page-sub {
            font-size: 14px;
            color: var(--text-secondary);
            margin-top: 6px;
            font-weight: 300;
        }

        /* ─── Stats ─── */
        .stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
            margin-bottom: 32px;
        }

        .stat {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 18px 22px;
            transition: box-shadow 0.2s;
        }

        .stat:hover { box-shadow: 0 4px 20px rgba(0,0,0,0.07); }

        .stat-label {
            font-size: 11px;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 6px;
        }

        .stat-val {
            font-family: 'Bricolage Grotesque', sans-serif;
            font-weight: 800;
            font-size: 30px;
            color: var(--text-primary);
        }

        .stat-val.red { color: var(--accent2); }

        /* ─── Toast notification ─── */
        .toast {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 13px 18px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 24px;
            border: 1px solid;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-8px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .toast.success { background: var(--success-soft); color: var(--success); border-color: #a7d7bc; }
        .toast.error   { background: var(--danger-soft);  color: var(--danger);  border-color: var(--accent2-border); }

        .toast-icon { font-size: 16px; }

        /* ─── Toolbar ─── */
        .toolbar {
            display: flex;
            gap: 12px;
            margin-bottom: 20px;
        }

        .search-wrap { position: relative; flex: 1; }

        .search-ico {
            position: absolute; left: 13px; top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted); font-size: 15px;
            pointer-events: none;
        }

        .search-input {
            width: 100%;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 10px 14px 10px 38px;
            font-family: 'Instrument Sans', sans-serif;
            font-size: 14px;
            color: var(--text-primary);
            outline: none;
            transition: border-color 0.2s;
        }

        .search-input::placeholder { color: var(--text-muted); }
        .search-input:focus { border-color: var(--accent); }

        .filter-sel {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 10px 14px;
            font-family: 'Instrument Sans', sans-serif;
            font-size: 14px;
            color: var(--text-primary);
            outline: none;
            cursor: pointer;
            min-width: 190px;
            transition: border-color 0.2s;
        }

        .filter-sel:focus { border-color: var(--accent); }

        /* ─── Table card ─── */
        .table-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 2px 16px rgba(0,0,0,0.04);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead tr {
            background: var(--bg);
            border-bottom: 1px solid var(--border);
        }

        th {
            font-family: 'Bricolage Grotesque', sans-serif;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1.3px;
            color: var(--text-muted);
            padding: 14px 20px;
            text-align: left;
        }

        td {
            padding: 14px 20px;
            font-size: 14px;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
        }

        tbody tr:last-child td { border-bottom: none; }

        tbody tr { transition: background 0.15s; }
        tbody tr:hover { background: #faf9f7; }

        /* ─── Avatar ─── */
        .avatar {
            width: 38px; height: 38px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-family: 'Bricolage Grotesque', sans-serif;
            font-weight: 700;
            font-size: 13px;
            flex-shrink: 0;
        }

        .user-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-name {
            font-weight: 500;
            font-size: 14px;
            color: var(--text-primary);
        }

        .user-email {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 1px;
        }

        /* ─── ID Badge ─── */
        .id-badge {
            font-family: 'Bricolage Grotesque', sans-serif;
            font-size: 12px;
            font-weight: 700;
            background: #f0ece6;
            color: var(--text-secondary);
            border: 1px solid var(--border-dark);
            border-radius: 6px;
            padding: 3px 9px;
            display: inline-block;
            letter-spacing: 0.3px;
        }

        /* ─── Spec Badge ─── */
        .spec {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 11px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .spec::before {
            content: '';
            width: 5px; height: 5px;
            border-radius: 50%;
            display: inline-block;
        }

        /* Couleurs dynamiques */
        .s0 { background: var(--tag-1-bg); color: var(--tag-1-text); }
        .s0::before { background: var(--tag-1-text); }
        .s1 { background: var(--tag-2-bg); color: var(--tag-2-text); }
        .s1::before { background: var(--tag-2-text); }
        .s2 { background: var(--tag-3-bg); color: var(--tag-3-text); }
        .s2::before { background: var(--tag-3-text); }
        .s3 { background: var(--tag-4-bg); color: var(--tag-4-text); }
        .s3::before { background: var(--tag-4-text); }
        .s4 { background: var(--tag-5-bg); color: var(--tag-5-text); }
        .s4::before { background: var(--tag-5-text); }

        /* ─── Delete button ─── */
        .btn-del {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            background: transparent;
            border: 1px solid var(--border-dark);
            border-radius: 8px;
            font-family: 'Instrument Sans', sans-serif;
            font-size: 13px;
            font-weight: 500;
            color: var(--text-secondary);
            cursor: pointer;
            transition: all 0.18s;
        }

        .btn-del:hover {
            background: var(--danger-soft);
            border-color: var(--accent2-border);
            color: var(--accent2);
        }

        .btn-del svg {
            width: 14px; height: 14px;
            stroke: currentColor;
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
            flex-shrink: 0;
        }

        /* ─── Table footer ─── */
        .table-foot {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 13px 20px;
            border-top: 1px solid var(--border);
            background: var(--bg);
        }

        .foot-info {
            font-size: 13px;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .live-dot {
            width: 7px; height: 7px;
            border-radius: 50%;
            background: var(--success);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }

        /* ─── Empty ─── */
        .empty {
            text-align: center;
            padding: 70px 24px;
        }

        .empty-icon { font-size: 44px; opacity: 0.25; margin-bottom: 12px; }

        .empty-title {
            font-family: 'Bricolage Grotesque', sans-serif;
            font-size: 20px;
            font-weight: 700;
            color: var(--text-secondary);
        }

        .empty-sub { font-size: 13px; color: var(--text-muted); margin-top: 4px; }

        /* ─── Modal overlay ─── */
        .modal-overlay {
            position: fixed; inset: 0;
            background: rgba(26,26,46,0.45);
            backdrop-filter: blur(4px);
            z-index: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s;
        }

        .modal-overlay.open {
            opacity: 1;
            pointer-events: all;
        }

        .modal {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 32px 32px 28px;
            width: 400px;
            max-width: 90vw;
            box-shadow: 0 24px 60px rgba(0,0,0,0.15);
            transform: scale(0.96);
            transition: transform 0.2s;
        }

        .modal-overlay.open .modal { transform: scale(1); }

        .modal-icon {
            width: 52px; height: 52px;
            border-radius: 14px;
            background: var(--danger-soft);
            border: 1px solid var(--accent2-border);
            display: flex; align-items: center; justify-content: center;
            font-size: 22px;
            margin-bottom: 18px;
        }

        .modal-title {
            font-family: 'Bricolage Grotesque', sans-serif;
            font-size: 20px;
            font-weight: 800;
            color: var(--text-primary);
            margin-bottom: 8px;
        }

        .modal-msg {
            font-size: 14px;
            color: var(--text-secondary);
            line-height: 1.6;
            margin-bottom: 24px;
        }

        .modal-name {
            font-weight: 600;
            color: var(--text-primary);
        }

        .modal-btns {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        .btn-cancel {
            padding: 9px 20px;
            background: transparent;
            border: 1px solid var(--border-dark);
            border-radius: 10px;
            font-family: 'Instrument Sans', sans-serif;
            font-size: 14px;
            font-weight: 500;
            color: var(--text-secondary);
            cursor: pointer;
            transition: background 0.15s;
        }

        .btn-cancel:hover { background: var(--bg); }

        .btn-confirm {
            padding: 9px 20px;
            background: var(--accent2);
            border: none;
            border-radius: 10px;
            font-family: 'Instrument Sans', sans-serif;
            font-size: 14px;
            font-weight: 600;
            color: white;
            cursor: pointer;
            transition: background 0.15s;
        }

        .btn-confirm:hover { background: #a83a22; }

        /* ─── Responsive ─── */
        @media (max-width: 700px) {
            main { padding: 28px 16px; }
            header { padding: 0 20px; }
            .stats { grid-template-columns: 1fr 1fr; }
            th:nth-child(1), td:nth-child(1) { display: none; }
            .user-email { display: none; }
        }
    </style>
</head>
<body>

<!-- ─── Header ─── -->
<header>
    <div class="logo">
        <div class="logo-mark">🎓</div>
        CentreFormations
    </div>
    <span class="nav-pill">Administration</span>
</header>

<main>

<?php
// ══════════════════════════════════════
//  CONNEXION
// ══════════════════════════════════════
$con = new mysqli("localhost", "root", "", "centredeformations");

if ($con->connect_error) {
    echo "<div class='toast error'><span class='toast-icon'>✖</span>Erreur de connexion : " . htmlspecialchars($con->connect_error) . "</div>";
    exit;
}

// ══════════════════════════════════════
//  SUPPRESSION (avant tout affichage)
// ══════════════════════════════════════
$toast = "";

if (isset($_POST['supprimer']) && !empty($_POST['ref'])) {

    $id = $_POST['ref'];

    // Supprimer d'abord le formateur (héritage : enfant avant parent)
    $stmt = $con->prepare("DELETE FROM formation WHERE reference = ?");
    $stmt->bind_param("s", $id);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        // Supprimer ensuite la salle parent
        $stmt2 = $con->prepare("DELETE FROM utilisateur WHERE id_user = ?");
        $stmt2->bind_param("s", $id);
        $stmt2->execute();
        $stmt2->close();
        $toast = "<div class='toast success'><span class='toast-icon'>✔</span>Formateur <strong>" . htmlspecialchars($id) . "</strong> supprimé avec succès.</div>";
    } else {
        $toast = "<div class='toast error'><span class='toast-icon'>✖</span>Erreur lors de la suppression.</div>";
    }
    $stmt->close();
}

// ══════════════════════════════════════
//  RÉCUPÉRATION DES DONNÉES
//  JOIN formateur ← utilisateur (héritage)
// ══════════════════════════════════════
$sql = "
    SELECT
        f.id_formateur,
        f.specialite,
        u.nom,
        u.prenom,
        u.email,
        u.role
    FROM formateur f
    INNER JOIN utilisateur u ON f.id_formateur = u.id_user
    ORDER BY u.nom ASC, u.prenom ASC
";

$result = $con->query($sql);

if (!$result) {
    echo "<div class='toast error'><span class='toast-icon'>✖</span>Erreur SQL : " . htmlspecialchars($con->error) . "</div>";
    exit;
}

$formateurs  = [];
$specialites = [];

while ($row = $result->fetch_assoc()) {
    $formateurs[] = $row;
    $spec = $row['specialite'];
    if (!in_array($spec, $specialites)) $specialites[] = $spec;
}

$total       = count($formateurs);
$nbSpec      = count($specialites);

// Palette de couleurs pour les badges spécialité
$palette = ['s0','s1','s2','s3','s4'];
$specMap  = [];
foreach ($specialites as $i => $s) {
    $specMap[$s] = $palette[$i % count($palette)];
}

// Couleurs avatar
$avatarColors = [
    ['bg'=>'#ede9fe','color'=>'#5b21b6'],
    ['bg'=>'#d1fae5','color'=>'#065f46'],
    ['bg'=>'#fee2e2','color'=>'#991b1b'],
    ['bg'=>'#fef3c7','color'=>'#92400e'],
    ['bg'=>'#dbeafe','color'=>'#1e40af'],
    ['bg'=>'#fce7f3','color'=>'#9d174d'],
];
?>

    <!-- ─── Page head ─── -->
    <div class="page-head">
        <div>
            <h1>Liste des <span>Formateurs</span></h1>
            <p class="page-sub">Gestion complète — affichage &amp; suppression</p>
        </div>
    </div>

    <?= $toast ?>

    <!-- ─── Stats ─── -->
    <div class="stats">
        <div class="stat">
            <div class="stat-label">Total</div>
            <div class="stat-val red"><?= $total ?></div>
        </div>
        <div class="stat">
            <div class="stat-label">Spécialités</div>
            <div class="stat-val"><?= $nbSpec ?></div>
        </div>
        <div class="stat">
            <div class="stat-label">Rôle</div>
            <div class="stat-val" style="font-size:18px;padding-top:8px;">Formateur</div>
        </div>
        <div class="stat">
            <div class="stat-label">Base</div>
            <div class="stat-val" style="font-size:15px;padding-top:10px;display:flex;align-items:center;gap:7px;">
                <span class="live-dot"></span>Active
            </div>
        </div>
    </div>

    <!-- ─── Toolbar ─── -->
    <div class="toolbar">
        <div class="search-wrap">
            <span class="search-ico">🔍</span>
            <input type="text" class="search-input" id="searchInput"
                   placeholder="Rechercher par nom, prénom, ID, email…"
                   oninput="filterTable()">
        </div>
        <select class="filter-sel" id="filterSpec" onchange="filterTable()">
            <option value="">Toutes les spécialités</option>
            <?php foreach ($specialites as $s): ?>
                <option value="<?= htmlspecialchars($s) ?>"><?= htmlspecialchars($s) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- ─── Table ─── -->
    <div class="table-card">

        <?php if ($total === 0): ?>
            <div class="empty">
                <div class="empty-icon">📭</div>
                <div class="empty-title">Aucun formateur</div>
                <div class="empty-sub">La table ne contient aucune donnée.</div>
            </div>
        <?php else: ?>

        <table id="mainTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Formateur</th>
                    <th>ID</th>
                    <th>Spécialité</th>
                    <th>Email</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($formateurs as $i => $f):
                $id     = htmlspecialchars($f['id_formateur']);
                $nom    = htmlspecialchars($f['nom']);
                $prenom = htmlspecialchars($f['prenom']);
                $email  = htmlspecialchars($f['email']);
                $spec   = htmlspecialchars($f['specialite']);
                $role   = htmlspecialchars($f['role']);

                $initials = strtoupper(substr($prenom,0,1) . substr($nom,0,1));
                $av = $avatarColors[$i % count($avatarColors)];
                $specClass = $specMap[$f['specialite']] ?? 's0';
            ?>
                <tr>
                    <td style="color:var(--text-muted);font-size:12px;font-family:'Bricolage Grotesque',sans-serif;font-weight:700;">
                        <?= str_pad($i+1, 2, '0', STR_PAD_LEFT) ?>
                    </td>
                    <td>
                        <div class="user-cell">
                            <div class="avatar" style="background:<?= $av['bg'] ?>;color:<?= $av['color'] ?>;"><?= $initials ?></div>
                            <div>
                                <div class="user-name"><?= $prenom ?> <?= $nom ?></div>
                                <div class="user-email"><?= $email ?></div>
                            </div>
                        </div>
                    </td>
                    <td><span class="id-badge"><?= $id ?></span></td>
                    <td><span class="spec <?= $specClass ?>"><?= $spec ?></span></td>
                    <td style="color:var(--text-secondary);font-size:13px;"><?= $email ?></td>
                    <td>
                        <!-- Bouton déclenche la modal JS -->
                        <button class="btn-del"
                                onclick="openModal('<?= $id ?>', '<?= addslashes($prenom . ' ' . $nom) ?>')">
                            <svg viewBox="0 0 24 24">
                                <polyline points="3 6 5 6 21 6"/>
                                <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                <path d="M10 11v6M14 11v6"/>
                                <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                            </svg>
                            Supprimer
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <div class="table-foot">
            <span class="foot-info">
                <span class="live-dot"></span>
                <span id="countLabel"><?= $total ?> formateur<?= $total>1?'s':'' ?> affiché<?= $total>1?'s':'' ?></span>
            </span>
            <span class="foot-info">Mis à jour le <?= date('d/m/Y à H:i') ?></span>
        </div>

        <?php endif; ?>
    </div>

</main>

<!-- ─── Modal confirmation ─── -->
<div class="modal-overlay" id="modalOverlay">
    <div class="modal">
        <div class="modal-icon">🗑️</div>
        <div class="modal-title">Confirmer la suppression</div>
        <p class="modal-msg">
            Vous êtes sur le point de supprimer la salle<br>
            <span class="modal-name" id="modalName"></span>.<br><br>
            Cette action supprimera également son compte utilisateur. Elle est <strong>irréversible</strong>.
        </p>
        <div class="modal-btns">
            <button class="btn-cancel" onclick="closeModal()">Annuler</button>
            <!-- Formulaire soumis uniquement après confirmation -->
            <form method="post" id="deleteForm" style="margin:0;">
                <input type="hidden" name="id_formateur" id="deleteId">
                <button type="submit" name="supprimer" class="btn-confirm">Supprimer définitivement</button>
            </form>
        </div>
    </div>
</div>

<script>
/* ── Filtrage dynamique ── */
function filterTable() {
    const q    = document.getElementById('searchInput').value.toLowerCase();
    const spec = document.getElementById('filterSpec').value.toLowerCase();
    const rows = document.querySelectorAll('#mainTable tbody tr');
    let n = 0;

    rows.forEach(row => {
        const txt  = row.innerText.toLowerCase();
        const spTd = row.cells[3] ? row.cells[3].innerText.toLowerCase() : '';
        const show = txt.includes(q) && (!spec || spTd.includes(spec));
        row.style.display = show ? '' : 'none';
        if (show) n++;
    });

    const lbl = document.getElementById('countLabel');
    if (lbl) lbl.textContent = n + ' formateur' + (n>1?'s':'') + ' affiché' + (n>1?'s':'');
}

/* ── Modal ── */
function openModal(id, name) {
    document.getElementById('deleteId').value   = id;
    document.getElementById('modalName').textContent = name + ' (ID : ' + id + ')';
    document.getElementById('modalOverlay').classList.add('open');
}

function closeModal() {
    document.getElementById('modalOverlay').classList.remove('open');
}

/* Fermer en cliquant hors du modal */
document.getElementById('modalOverlay').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});

/* Fermer avec Échap */
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });
</script>

<?php $con->close(); ?>
</body>
</html>