<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php
    $con=mysqli_connect('localhost','root','','centredeformations');
    if(!$con){
        eecho("echec de connexion");
    }


 //la partie de la recherche 
 $search       = $_POST['search'];
$filterType   = $_POST['filterType'];
$filterStatut = $_POST['filterStatut'];
$sortSelect   = $_POST['sortSelect'];
$sortOrder    = $_POST['sortOrder'] == 'desc' ? 'DESC' : 'ASC';

$sql = "SELECT * FROM salle WHERE 1=1";

if($search != '')       $sql .= " AND nom LIKE '%$search%'";
if($filterType != '')   $sql .= " AND type_salle='$filterType'";
if($filterStatut != '') $sql .= " AND disponibilite='$filterStatut'";
if($sortSelect != '')   $sql .= " ORDER BY $sortSelect $sortOrder";

$r = mysqli_query($con, $sql);

if(mysqli_num_rows($r)==0){
    echo "<p style='color:red;'>Aucune salle trouvée.</p>";
}else{
    while($salle=mysqli_fetch_assoc($r)){
        echo "<div class='col'>
                <div class='card h-100 shadow-sm'>
                    <div class='card-body'>
                        <h5 class='card-title'>".$salle['nom']."</h5>
                        <p class='card-text'>
                            <strong>Capacité :</strong> ".$salle['capacite']." personnes<br>
                            <strong>Type :</strong> ".$salle['type_salle']."<br>
                            <strong>Statut :</strong> ".$salle['disponibilite']."
                        </p>
                    </div>
                </div>
              </div>";
    }
}
    ?>
</body>
</html>