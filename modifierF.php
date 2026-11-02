<?php
session_start();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['csrf_token'];

require_once __DIR__ . '/db_pdo.php';

$message      = '';
$type_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $message      = "Erreur de sécurité : token invalide. Veuillez recharger la page.";
        $type_message = 'danger';
    } else {

        $action = $_POST['action'] ?? '';

        if ($action === 'supprimer') {
            $id = (int)($_POST['id'] ?? 0);

            if ($id < 1) {
                $message      = "Identifiant invalide.";
                $type_message = 'danger';
            } else {
                $check = $pdo->prepare("SELECT id FROM formations WHERE id = :id");
                $check->execute([':id' => $id]);

                if (!$check->fetch()) {
                    $message      = "Formation introuvable.";
                    $type_message = 'danger';
                } else {
                    try {
                        $stmt = $pdo->prepare("DELETE FROM formations WHERE id = :id");
                        $stmt->execute([':id' => $id]);
                        $message      = "La formation a été supprimée avec succès.";
                        $type_message = 'success';
                    } catch (PDOException $e) {
                        $message      = "Erreur lors de la suppression : " . $e->getMessage();
                        $type_message = 'danger';
                    }
                }
            }
        }

        if ($action === 'modifier') {
            $id           = (int)($_POST['id']           ?? 0);
            $titre        = trim($_POST['titre']         ?? '');
            $description  = trim($_POST['description']   ?? '');
            $prerequis    = trim($_POST['prerequis']     ?? '');
            $type         = trim($_POST['type']          ?? '');
            $duree        = (int)($_POST['duree']        ?? 0);
            $id_formateur = (int)($_POST['id_formateur'] ?? 0);
            $id_salle     = !empty($_POST['id_salle']) ? (int)$_POST['id_salle'] : null;
            $date_debut   = trim($_POST['date_debut']    ?? '');
            $date_fin     = trim($_POST['date_fin']      ?? '');

            if ($titre === '' || $duree < 1 || $id_formateur < 1 || $date_debut === '' || $date_fin === '') {
                $message      = "Veuillez remplir tous les champs obligatoires.";
                $type_message = 'danger';
            } elseif ($date_fin < $date_debut) {
                $message      = "La date de fin doit être après la date de début.";
                $type_message = 'danger';
            } elseif (!in_array($type, ['Presentiel', 'enligne'], true)) {
                $message      = "Type de formation invalide.";
                $type_message = 'danger';
            } else {
                try {
                    $sql = "UPDATE formations SET
                                titre        = :titre,
                                description  = :description,
                                prerequis    = :prerequis,
                                type         = :type,
                                duree        = :duree,
                                id_formateur = :id_formateur,
                                id_salle     = :id_salle,
                                date_debut   = :date_debut,
                                date_fin     = :date_fin
                            WHERE id = :id";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([
                        ':titre'        => $titre,
                        ':description'  => $description ?: null,
                        ':prerequis'    => $prerequis   ?: null,
                        ':type'         => $type,
                        ':duree'        => $duree,
                        ':id_formateur' => $id_formateur,
                        ':id_salle'     => $id_salle,
                        ':date_debut'   => $date_debut,
                        ':date_fin'     => $date_fin,
                        ':id'           => $id,
                    ]);
                    $message      = "La formation a été modifiée avec succès.";
                    $type_message = 'success';
                } catch (PDOException $e) {
                    $message      = "Erreur lors de la modification : " . $e->getMessage();
                    $type_message = 'danger';
                }
            }
        }

        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $csrf = $_SESSION['csrf_token'];
    }
}

$formations = $pdo->query("SELECT * FROM formations ORDER BY date_debut DESC")->fetchAll(PDO::FETCH_ASSOC);

function tronquer(string $texte, int $max = 60): string {
    if (function_exists('mb_strimwidth')) {
        return mb_strimwidth($texte, 0, $max, '…');
    }
    return strlen($texte) > $max ? substr($texte, 0, $max) . '…' : $texte;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Liste des Formations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f0f4f8; }
        .card { border-radius: 16px; border: none; }
        .card-header { background: linear-gradient(135deg, #0d6efd, #0a58ca) !important; border-radius: 16px 16px 0 0 !important; }
        .table-hover tbody tr:hover { background-color: #e9f0ff; cursor: pointer; }
        .badge-presentiel { background-color: #0d6efd; }
        .badge-enligne    { background-color: #198754; }
        .btn-action { width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; }
        .modal-header { background: linear-gradient(135deg, #0d6efd, #0a58ca); color: white; border-radius: 12px 12px 0 0; }
        .modal-header .btn-close { filter: invert(1); }
        .section-title { font-size: .75rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #6c757d; margin-bottom: .5rem; }
        .empty-state { text-align: center; padding: 60px 20px; color: #6c757d; }
        .empty-state i { font-size: 3rem; margin-bottom: 1rem; opacity: .4; }
    </style>
</head>
<body>

<div class="container mt-5 mb-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">
                <i class="fas fa-graduation-cap me-2 text-primary"></i>Gestion des Formations
            </h4>
            <small class="text-muted"><?= count($formations) ?> formation(s) au total</small>
        </div>
        <a href="ajouterF.php" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i>Nouvelle formation
        </a>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-<?= htmlspecialchars($type_message) ?> alert-dismissible fade show" role="alert">
            <i class="fas fa-<?= $type_message === 'success' ? 'check-circle' : 'exclamation-circle' ?> me-2"></i>
            <?= htmlspecialchars($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-header text-white p-3">
            <h6 class="mb-0"><i class="fas fa-list me-2"></i>Liste des formations</h6>
        </div>
        <div class="card-body p-0">
            <?php if (empty($formations)): ?>
                <div class="empty-state">
                    <i class="fas fa-folder-open d-block"></i>
                    <p class="fw-semibold">Aucune formation enregistrée.</p>
                    <a href="ajouterF.php" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i>Ajouter une formation
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Titre</th>
                                <th>Type</th>
                                <th>Durée</th>
                                <th>Formateur</th>
                                <th>Début</th>
                                <th>Fin</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($formations as $f): ?>
                            <tr>
                                <td class="text-muted small"><?= (int)$f['id'] ?></td>
                                <td>
                                    <div class="fw-semibold"><?= htmlspecialchars($f['titre']) ?></div>
                                    <?php if (!empty($f['description'])): ?>
                                        <small class="text-muted"><?= htmlspecialchars(tronquer($f['description'])) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge <?= $f['type'] === 'Presentiel' ? 'badge-presentiel' : 'badge-enligne' ?> text-white">
                                        <?= $f['type'] === 'Presentiel' ? 'Présentiel' : 'En ligne' ?>
                                    </span>
                                </td>
                                <td><?= (int)$f['duree'] ?>h</td>
                                <td><?= (int)$f['id_formateur'] ?></td>
                                <td><?= date('d/m/Y', strtotime($f['date_debut'])) ?></td>
                                <td><?= date('d/m/Y', strtotime($f['date_fin'])) ?></td>
                                <td class="text-center">
                                    <div class="d-flex gap-1 justify-content-center">

                                        <!-- Bouton Modifier : toutes les données en data-* (pas de JSON inline) -->
                                        <button class="btn btn-outline-primary btn-action btn-modifier"
                                                title="Modifier"
                                                data-id="<?= (int)$f['id'] ?>"
                                                data-titre="<?= htmlspecialchars($f['titre'], ENT_QUOTES) ?>"
                                                data-description="<?= htmlspecialchars($f['description'] ?? '', ENT_QUOTES) ?>"
                                                data-prerequis="<?= htmlspecialchars($f['prerequis'] ?? '', ENT_QUOTES) ?>"
                                                data-type="<?= htmlspecialchars($f['type'], ENT_QUOTES) ?>"
                                                data-duree="<?= (int)$f['duree'] ?>"
                                                data-formateur="<?= (int)$f['id_formateur'] ?>"
                                                data-salle="<?= $f['id_salle'] ? (int)$f['id_salle'] : '' ?>"
                                                data-debut="<?= htmlspecialchars($f['date_debut'], ENT_QUOTES) ?>"
                                                data-fin="<?= htmlspecialchars($f['date_fin'], ENT_QUOTES) ?>">
                                            <i class="fas fa-pen fa-xs"></i>
                                        </button>

                                        <!-- Bouton Supprimer : seulement id et titre en data-* -->
                                        <button class="btn btn-outline-danger btn-action btn-supprimer"
                                                title="Supprimer"
                                                data-id="<?= (int)$f['id'] ?>"
                                                data-titre="<?= htmlspecialchars($f['titre'], ENT_QUOTES) ?>">
                                            <i class="fas fa-trash fa-xs"></i>
                                        </button>

                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- MODAL MODIFICATION -->
<div class="modal fade" id="modalModif" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-pen me-2"></i>Modifier la formation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="modifierF.php">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                <input type="hidden" name="action" value="modifier">
                <input type="hidden" name="id" id="modif_id">

                <div class="modal-body p-4">
                    <p class="section-title"><i class="fas fa-info-circle me-1"></i>Informations générales</p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Titre <span class="text-danger">*</span></label>
                        <input type="text" name="titre" id="modif_titre" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Description</label>
                        <textarea name="description" id="modif_description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Prérequis</label>
                        <input type="text" name="prerequis" id="modif_prerequis" class="form-control">
                    </div>
                    <p class="section-title mt-3"><i class="fas fa-sliders-h me-1"></i>Détails</p>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Type <span class="text-danger">*</span></label>
                            <select name="type" id="modif_type" class="form-select">
                                <option value="Presentiel">Présentiel</option>
                                <option value="enligne">En ligne</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Durée (h) <span class="text-danger">*</span></label>
                            <input type="number" name="duree" id="modif_duree" class="form-control" min="1" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">ID Formateur <span class="text-danger">*</span></label>
                            <input type="number" name="id_formateur" id="modif_id_formateur" class="form-control" min="1" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">ID Salle</label>
                            <input type="number" name="id_salle" id="modif_id_salle" class="form-control" min="1">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Date début <span class="text-danger">*</span></label>
                            <input type="date" name="date_debut" id="modif_date_debut" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Date fin <span class="text-danger">*</span></label>
                            <input type="date" name="date_fin" id="modif_date_fin" class="form-control" required>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Annuler
                    </button>
                    <button type="submit" class="btn btn-primary fw-bold">
                        <i class="fas fa-save me-1"></i>Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL SUPPRESSION -->
<div class="modal fade" id="modalSuppr" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white" style="border-radius: 12px 12px 0 0;">
                <h5 class="modal-title"><i class="fas fa-trash me-2"></i>Confirmer la suppression</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="modifierF.php">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                <input type="hidden" name="action" value="supprimer">
                <input type="hidden" name="id" id="suppr_id">

                <div class="modal-body text-center py-4">
                    <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                    <p class="mb-1">Voulez-vous vraiment supprimer la formation :</p>
                    <p class="fw-bold" id="suppr_titre_affiche"></p>
                    <small class="text-muted">Cette action est irréversible.</small>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-outline-secondary me-2" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Annuler
                    </button>
                    <button type="submit" class="btn btn-danger fw-bold">
                        <i class="fas fa-trash me-1"></i>Oui, supprimer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const modalModif = new bootstrap.Modal(document.getElementById('modalModif'));
    const modalSuppr = new bootstrap.Modal(document.getElementById('modalSuppr'));

    /* Boutons MODIFIER */
    document.querySelectorAll('.btn-modifier').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const d = this.dataset;
            document.getElementById('modif_id').value           = d.id;
            document.getElementById('modif_titre').value        = d.titre;
            document.getElementById('modif_description').value  = d.description;
            document.getElementById('modif_prerequis').value    = d.prerequis;
            document.getElementById('modif_type').value         = d.type;
            document.getElementById('modif_duree').value        = d.duree;
            document.getElementById('modif_id_formateur').value = d.formateur;
            document.getElementById('modif_id_salle').value     = d.salle;
            document.getElementById('modif_date_debut').value   = d.debut;
            document.getElementById('modif_date_fin').value     = d.fin;
            modalModif.show();
        });
    });

    /* Boutons SUPPRIMER */
    document.querySelectorAll('.btn-supprimer').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('suppr_id').value                  = this.dataset.id;
            document.getElementById('suppr_titre_affiche').textContent = this.dataset.titre;
            modalSuppr.show();
        });
    });

});
</script>
</body>
</html>