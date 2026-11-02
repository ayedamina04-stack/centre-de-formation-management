var formateurs = [];
 
  var compteur = 3;
  var idEnEdition = null;
 
  function afficher() {
    var tableau = document.getElementById("tableau");
    tableau.innerHTML = "";
 
    for (var i = 0; i < formateurs.length; i++) {
      var f = formateurs[i];
      var ligne = "<tr>";
      ligne += "<td>" + f.nom + "</td>";
      ligne += "<td>" + f.prenom + "</td>";
      ligne += "<td>" + f.email + "</td>";
      ligne += "<td>" + f.tel + "</td>";
      ligne += "<td>" + f.mot + "</td>";
      ligne += "<td>" + f.specialite + "</td>";
      ligne += "<td>";
      ligne += "<button class='btn-modifier' onclick='modifier(" + f.id + ")'>✏ Modifier</button>";
      ligne += "<button class='btn-supprimer' onclick='supprimer(" + f.id + ")'>🗑 Supprimer</button>";
      ligne += "</td></tr>";
      tableau.innerHTML += ligne;
    }
  }
 
  function ouvrirModal() {
    idEnEdition = null;
    document.getElementById("titreModal").textContent = "Ajouter un Formateur";
    document.getElementById("nom").value = "";
    document.getElementById("prenom").value = "";
    document.getElementById("email").value = "";
    document.getElementById("tel").value = "";
    document.getElementById("mot").value = "";
    document.getElementById("specialite").value = "";
    document.getElementById("modal").classList.add("ouvert");
  }
 
  function fermerModal() {
    document.getElementById("modal").classList.remove("ouvert");
  }
 
  function enregistrer() {
    var nom = document.getElementById("nom").value;
    var prenom = document.getElementById("prenom").value;
    var email = document.getElementById("email").value;
    var tel = document.getElementById("tel").value;
    var mot = document.getElementById("mot").value;
    var specialite = document.getElementById("specialite").value;
 
    if (!nom || !prenom || !email ||!tel||!mot ||!specialite) {
      alert("Veuillez remplir tous les champs.");
      return;
    }
    if (nom=="" || !alpha(nom)){
      alert("erreur : Saisir votre nom correctement !");
      return
    }

    if (idEnEdition === null) {
      formateurs.push({ id: compteur++, nom: nom, prenom: prenom, email: email,tel:tel,mot:mot ,specialite: specialite });
    } else {
      for (var i = 0; i < formateurs.length; i++) {
        if (formateurs[i].id === idEnEdition) {
          formateurs[i].nom = nom;
          formateurs[i].prenom = prenom;
          formateurs[i].email = email;
          formateurs[i].tel = tel;
          formateurs[i].mot = mot;
          formateurs[i].specialite = specialite;
        }
      }
    }
 
    fermerModal();
    afficher();
  }
 
  function modifier(id) {
    for (var i = 0; i < formateurs.length; i++) {
      if (formateurs[i].id === id) {
        idEnEdition = id;
        document.getElementById("titreModal").textContent = "Modifier le Formateur";
        document.getElementById("nom").value = formateurs[i].nom;
        document.getElementById("prenom").value = formateurs[i].prenom;
        document.getElementById("email").value = formateurs[i].email;
        document.getElementById("tel").value = formateurs[i].tel;
        document.getElementById("mot").value = formateurs[i].mot;
        document.getElementById("specialite").value = formateurs[i].specialite;
        document.getElementById("modal").classList.add("ouvert");
      }
    }
  }
 
  function supprimer(id) {
    if (confirm("Voulez-vous supprimer ce formateur ?")) {
      for (var i = 0; i < formateurs.length; i++) {
        if (formateurs[i].id === id) {
          formateurs.splice(i, 1);
          break;
        }
      }
      afficher();
    }
  }
 
  afficher();

 function alpha(ch) {
  for (var i = 0; i < ch.length; i++) {
    if (ch[i] < 'A' || (ch[i] > 'Z' && ch[i] < 'a') || ch[i] > 'z') {
      return false;
    }
  }
  return true;
}
  