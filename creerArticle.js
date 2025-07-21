/**
 * Projet de programmation Web de Lakshya Selvakumar et Nissi Otouli
 */


/**
 * On souhaite agir spécifiquement sur une partie HTML du code alors on utilise document.
 * querySelector et il s'execute le code quand on publie l'article (submit). 
 * Empêche également le comportement par défaut du formulaire.
 */
document.querySelector('.formulaire').addEventListener('submit', function (e) {
    e.preventDefault();

    /** 
     * Récupère dans ces variables les éléments contenus dans les champs sauf date :)
     */
    const titre = document.getElementById('titre').value;
    const contenu = document.getElementById('contenu').value;
    const auteur = document.getElementById('nom').value;
    const date = new Date().toLocaleDateString("fr-FR");

    /**
     * On crée un objet contenant les informations saisies. On utilise localStorage qui
     * est une propriété qui permet de stocker des données localement. L'avantage c'est
     * qu'on ne perd rien même si le navigateur est fermé. 
     */
    const nouvelArticle = {
        titre,
        contenu,
        auteur,
        date
    };

    /**
     * Récupère ce qui est déjà présent dans localStorage et le converti en tableau. 
     * Ajoute le nouvelle article au tableau. Puis sauvegarde l'articles, JSON.stringify
     * sert convertir en texte car localStorage ne stocke que du texte.
     */
    let articles = JSON.parse(localStorage.getItem('articles')) || [];
    articles.push(nouvelArticle);
    localStorage.setItem('articles', JSON.stringify(articles));

    /** Affiche un message d'alerte quand l'article est publié sur la page principale et
     * renvoie directement vers ce dernier.
     */
    alert("Article publié !");
    window.location.href = "projet_V1.html";
});
