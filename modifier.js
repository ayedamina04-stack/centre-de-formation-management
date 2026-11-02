// Simulation de base de données
const baseDonneesSalles = {
    "S101": { nom: "Salle Turing", type: "TP", capa: 20, dispo: "Libre" },
    "S102": { nom: "Amphi Einstein", type: "Amphi", capa: 150, dispo: "Occupée" }
};

function rechercherSalle() {
    const ref = document.getElementById('searchRef').value.toUpperCase();
    const message = document.getElementById('message');
    const inputs = document.querySelectorAll('#formModifier input, #formModifier select, #btnSave');

    if (baseDonneesSalles[ref]) {
        const salle = baseDonneesSalles[ref];
        
        // Remplir les champs
        document.getElementById('nom').value = salle.nom;
        document.getElementById('type_salle').value = salle.type;
        document.getElementById('capacite').value = salle.capa;
        document.getElementById('disponibilite').value = salle.dispo;

        // Activer les champs
        inputs.forEach(el => el.disabled = false);
        message.innerText = "Salle trouvée ! Vous pouvez modifier.";
        message.style.color = "green";
    } else {
        message.innerText = "Référence introuvable.";
        message.style.color = "red";
        inputs.forEach(el => el.disabled = true);
    }
}

function enregistrerModifs() {
    const ref = document.getElementById('searchRef').value.toUpperCase();
    
    // Ici, vous feriez normalement un appel API (fetch) vers un serveur
    alert(`Succès ! La salle ${ref} a été mise à jour dans le système.`);
}