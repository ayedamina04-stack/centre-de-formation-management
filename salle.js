// --- INITIALISATION ---
let salles = JSON.parse(localStorage.getItem("salles")) || [];
let indexEdit = -1;

const form = document.getElementById("salleForm");
const erreur = document.getElementById("erreur");
const liste = document.getElementById("listeSalles");

const search = document.getElementById("search");
const filterType = document.getElementById("filterType");
const filterStatut = document.getElementById("filterStatut");
const sortSelect = document.getElementById("sortSelect");
const sortOrder = document.getElementById("sortOrder");

// --- TOAST NOTIFICATION ---
function showToast(msg, type="success"){
    const toast = document.createElement("div");
    toast.className = `toast ${type}`;
    toast.textContent = msg;s
    toast.style.position = "fixed";
    toast.style.top = "20px";
    toast.style.right = "20px";
    toast.style.padding = "10px 20px";
    toast.style.backgroundColor = type==="success"?"#28a745":"#dc3545";
    toast.style.color = "white";
    toast.style.borderRadius = "5px";
    toast.style.boxShadow = "0 2px 10px rgba(0,0,0,0.2)";
    toast.style.zIndex = 9999;
    document.body.appendChild(toast);
    setTimeout(()=> toast.remove(), 3000);
}

// --- AJOUT / MODIFICATION ---
form.addEventListener("submit", function(e){
    e.preventDefault();

    const nom = document.getElementById("nom").value.trim();
    const capacite = parseInt(document.getElementById("capacite").value);
    const type = document.getElementById("type").value;
    const statut = document.getElementById("statut").value;

    erreur.textContent = "";

    if(!nom){
        erreur.textContent = "Nom obligatoire";
        return;
    }
    if(!/[a-zA-Z]/.test(nom)){
        erreur.textContent = "Le nom doit contenir au moins une lettre";
        return;
    }
    if(salles.some((s,i)=> s.nom.toLowerCase() === nom.toLowerCase() && i !== indexEdit)){
        erreur.textContent = "Nom déjà utilisé";
        return;
    }
    if(isNaN(capacite) || capacite < 1){
        erreur.textContent = "Capacité invalide";
        return;
    }
    if(!type){
        erreur.textContent = "Choisir un type";
        return;
    }

    const salle = {nom, capacite, type, statut};

    if(indexEdit === -1){
        salles.push(salle);
        showToast("Salle ajoutée avec succès !", "success");
    } else {
        salles[indexEdit] = salle;
        indexEdit = -1;
        showToast("Salle modifiée avec succès !", "success");
    }

    localStorage.setItem("salles", JSON.stringify(salles));
    form.reset();
    filterAndSortSalles(); // affiche selon recherche/filtres/tri
});

// --- AFFICHAGE ---
function afficher(data){
    data = data || salles;
    liste.innerHTML = "";

    if(data.length === 0){
        liste.innerHTML = `<p class="text-center text-muted">Aucune salle disponible</p>`;
        return;
    }

    const colors = {
        informatique: "#cce5ff",
        conference: "#e0ccff",
        classique: "#d4edda"
    };

    data.forEach((s,index)=>{
        const div = document.createElement("div");
        div.className = "col";

        // Surbrillance texte recherché
        let nomHighlight = s.nom;
        const query = search.value.trim();
        if(query){
            const regex = new RegExp(`(${query})`, "gi");
            nomHighlight = s.nom.replace(regex, `<mark>$1</mark>`);
        }

        div.innerHTML = `
        <div class="card h-100 shadow-sm" style="background-color: ${colors[s.type] || "white"}">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-door-open"></i> ${nomHighlight}</h5>
                <p><strong>Capacité:</strong> ${s.capacite}</p>
                <p><strong>Type:</strong> ${s.type}</p>
                <p><strong>Statut:</strong> 
                    <span class="${s.statut==='Disponible'?'text-success':'text-danger'}">
                    ${s.statut}</span>
                </p>
            </div>
            <div class="card-footer d-flex justify-content-between">
                <button type="button" class="btn btn-warning btn-sm" onclick="modifier(${index})"><i class="fas fa-edit"></i></button>
                <button type="button" class="btn btn-danger btn-sm" onclick="supprimer(${index})"><i class="fas fa-trash"></i></button>
                <button type="button" class="btn btn-info btn-sm" onclick="toggleStatut(${index})"><i class="fas fa-sync-alt"></i></button>
            </div>
        </div>`;
        liste.appendChild(div);
    });
}

// --- SUPPRESSION ---
function supprimer(i){
    if(confirm("Voulez-vous vraiment supprimer cette salle ?")){
        salles.splice(i,1);
        localStorage.setItem("salles", JSON.stringify(salles));
        filterAndSortSalles();
        showToast("Salle supprimée !", "error");
    }
}

// --- MODIFICATION ---
function modifier(i){
    const s = salles[i];
    document.getElementById("nom").value = s.nom;
    document.getElementById("capacite").value = s.capacite;
    document.getElementById("type").value = s.type;
    document.getElementById("statut").value = s.statut;
    indexEdit = i;

    document.getElementById("salleForm").scrollIntoView({ behavior: "smooth", block: "start" });
    document.getElementById("nom").focus();
}

// --- CHANGER STATUT ---
function toggleStatut(i){
    salles[i].statut = salles[i].statut === "Disponible" ? "Indisponible" : "Disponible";
    localStorage.setItem("salles", JSON.stringify(salles));
    filterAndSortSalles();
    showToast(`Statut de ${salles[i].nom} changé !`, "success");
}

// --- FILTRE + TRI + ORDRE ---
[search, filterType, filterStatut, sortSelect, sortOrder].forEach(el => {
    el.addEventListener("input", filterAndSortSalles);
    el.addEventListener("keypress", e=>{
        if(e.key==="Enter"){
            e.preventDefault();
            filterAndSortSalles();
        }
    });
});

function filterAndSortSalles(){
    let data = [...salles];

    // --- RECHERCHE ---
    const query = search.value.toLowerCase().trim();
    if(query) data = data.filter(s => s.nom.toLowerCase().includes(query));

    // --- FILTRES ---
    if(filterType.value) data = data.filter(s => s.type.toLowerCase() === filterType.value.toLowerCase());
    if(filterStatut.value) data = data.filter(s => s.statut.toLowerCase() === filterStatut.value.toLowerCase());

    // --- TRI ---
    switch(sortSelect.value){
        case "nom":
            data.sort((a,b)=> a.nom.localeCompare(b.nom));
            break;
        case "capacite":
            data.sort((a,b)=> b.capacite - a.capacite);
            break;
        case "statut":
            data.sort((a,b)=>{
                if(a.statut===b.statut) return 0;
                return a.statut==="Disponible" ? -1 : 1;
            });
            break;
    }

    // --- ORDRE ASC / DESC ---
    if(sortOrder.value === "desc") data.reverse();

    afficher(data);
}

// --- INIT ---
filterAndSortSalles();