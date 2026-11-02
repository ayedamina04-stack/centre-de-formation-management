<?php
header('Content-Type: application/json; charset=utf-8');

/* ── Connexion base de données ── */
require_once __DIR__ . '/db_pdo.php';

/* ── Récupération des champs ── */
$titre        = trim($_POST['titre']        ?? '');
$description  = trim($_POST['description']  ?? '');
$prerequis    = trim($_POST['prerequis']    ?? '');
$type         = trim($_POST['type']         ?? '');
$duree        = (int)($_POST['duree']       ?? 0);
$id_formateur = (int)($_POST['id_formateur'] ?? 0);
$id_salle     = !empty($_POST['id_salle']) ? (int)$_POST['id_salle'] : null;
$date_debut   = trim($_POST['date_debut']   ?? '');
$date_fin     = trim($_POST['date_fin']     ?? '');

/* ── Validation ── */
if ($titre === '') {
    echo json_encode(['success' => false, 'message' => 'Le titre est obligatoire.']);
    exit;
}
if ($duree < 1) {
    echo json_encode(['success' => false, 'message' => 'La durée doit être au moins 1 heure.']);
    exit;
}
if ($id_formateur < 1) {
    echo json_encode(['success' => false, 'message' => "L'identifiant du formateur est requis."]);
    exit;
}
if ($date_debut === '' || $date_fin === '') {
    echo json_encode(['success' => false, 'message' => 'Les dates de début et de fin sont obligatoires.']);
    exit;
}
if ($date_fin < $date_debut) {
    echo json_encode(['success' => false, 'message' => 'La date de fin doit être après la date de début.']);
    exit;
}

/* ── Insertion ── */
try {
    $sql = "INSERT INTO formations
                (titre, description, prerequis, type, duree, id_formateur, id_salle, date_debut, date_fin, created_at)
            VALUES
                (:titre, :description, :prerequis, :type, :duree, :id_formateur, :id_salle, :date_debut, :date_fin, NOW())";

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
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'La formation "' . htmlspecialchars($titre) . '" a été enregistrée avec succès.'
    ]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()]);
}