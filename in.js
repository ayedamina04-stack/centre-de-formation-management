
const form = document.querySelector("form");

form.addEventListener("submit", function (e) {
form.addEventListener("submit", function (e) {
    e.preventDefault(); // Bloquer la soumission par défaut

    const email    = (document.getElementById("mail") || document.getElementById("email"))?.value.trim();
    const password = (document.getElementById("password") || document.getElementById("password2"))?.value.trim();

    const nomRegex   = /^[A-Za-zÀ-ÿ\s]+$/;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const telRegex   = /^[0-9]{8,15}$/;

    const nomField = document.getElementById("nom");

    // =========================
    // 🔐 LOGIN
    // =========================
    if (!nomField) {
        if (!emailRegex.test(email)) {
            alert("Email invalide ❌");
            return;
        }
        if (!password || password.length < 6) {
            alert("Mot de passe doit contenir au moins 6 caractères ❌");
            return;
        }

        // ✅ Envoyer les données PHP en arrière-plan puis rediriger
        fetch(form.action, {
            method: "POST",
            body: new FormData(form)
        })
        .then(res => res.text())
        .then(response => {
            // Rediriger selon la réponse PHP (PHP retourne le rôle)
            if (response.includes("admin")) {
                window.location.href = "admin.html";
            } else if (response.includes("formateur")) {
                window.location.href = "gestionFormateur.html";
            } else {
                window.location.href = "service.html";
            }
        })
        .catch(() => alert("Erreur de connexion"));

        return;
    }

    // =========================
    // 📝 INSCRIPTION
    // =========================
    const nom    = document.getElementById("nom")?.value.trim();
    const prenom = document.getElementById("prenom")?.value.trim();
    const tel    = document.getElementById("tel")?.value.trim();

    if (!nomRegex.test(nom)) { alert("Nom invalide"); return; }
    if (!nomRegex.test(prenom)) { alert("Prénom invalide"); return; }
    if (!emailRegex.test(email)) { alert("Email invalide"); return; }
    if (!telRegex.test(tel)) { alert("Téléphone invalide"); return; }
    if (!password || password.length < 6) { alert("Mot de passe invalide"); return; }

    const roleSelect = document.getElementById("roleSelect");

    if (roleSelect) {
        const role = roleSelect.value;
        if (!role) { alert("Veuillez choisir un rôle"); return; }

        // ✅ Envoyer les données PHP en arrière-plan puis rediriger
        fetch(form.action, {
            method: "POST",
            body: new FormData(form)
        })
        .then(() => {
            if (role === "admin") {
                window.location.href = "admin.html";
            } else if (role === "formateur") {
                window.location.href = "gestionFormateur.html";
            } else {
                window.location.href = "service.html";
            }
        })
        .catch(() => alert("Erreur lors de l'inscription"));
    }
});

});