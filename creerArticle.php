<?php
session_start();
include('config.php');

/**
 * Redirige l'utilisateur vers la page de connexion si ce
 * dernier n'est pas connecté à son compte. (Fonctionnalité désactivé car connexion pas 
 * possible pour le moment).
 */

if (!isset($_SESSION['username'])) {
    header("Location: connexionPage.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['titre'];
    $content = $_POST['contenu'];

    $stmt = $bdd->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$_SESSION['username']]);
    $author_id = $stmt->fetchColumn();

    // Usage du javacript pour afficher un message d'alerte lorsque le post est publié ou non
    if ($author_id && !empty($title) && !empty($content)) {
        $stmt = $bdd->prepare("INSERT INTO articles (title, content, author_id) VALUES (?, ?, ?)");
        $stmt->execute([$title, $content, $author_id]);
        echo "<script>alert('Article publié avec succès !');</script>";
    } else {
        echo "<script>alert('Tous les champs sont requis.');</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404</title>
    <link rel="stylesheet" href="creerArticle.css">
    <link rel="icon" href="img/icon.png">
</head>

<body>
    <header>

        <h1>Bits &amp; curiosités</h1>
        <nav>
            <ul>
                <li><a href="projet_V1.php">Accueil</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section id="creer_article" class="creer_article">
            <form class="formulaire" action="creerArticle.php" method="POST">
                <fieldset>
                    <legend>
                        <h2>Créez votre article !</h2>
                    </legend>

                    <input type="text" name="titre" placeholder="Titre de l'article" required>

                    <textarea name="contenu" placeholder="Contenu de l'article" required></textarea>

                    <input type="text" name="nom" placeholder="Votre nom" required>

                    <button type="submit">Publier</button>
                    <button type="reset">Réinitialiser</button>
                </fieldset>
            </form>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 Blog de Lakshya et Nissi.</p>
    </footer>

</body>
</html>