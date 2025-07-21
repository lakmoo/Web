<!-- Projet de programmation Web de Lakshya Selvakumar et Nissi Otouli -->
<?php 
session_start();
include('config.php');?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erreur 404 - Page introuvable</title>
    <link rel="stylesheet" href="projet_V1.css">
    <link rel="icon" href="img/icon.png" type="image/png">
</head>

<body>
    <header>
        <?php if (isset($_SESSION['username'])): ?>
                <div class="welcome-message">
                    <p>Bienvenue, <a href="Home_page.php"><?php echo $_SESSION['username']; ?> </a>!</p>
                </div>
        <?php endif; ?>
        <h1>Bits &amp; Curiosités</h1>
        <!-- Barre de navigation -->
        <nav>
            <ul>
                 <?php if (isset($_SESSION['username'])):?>
                    <li><a href="amis.php">Amis</a></li>
                    <li><a href="creerArticle.php">Créer un article</a></li>
                    <li><a href="deconnexionPage.php">Déconnexion</a></li>
                <?php endif; ?>
                <?php if (!isset($_SESSION['username'])): ?>
                    <li><a href="connexionPage.php">Connexion</a></li>
                <?php endif; ?>

                <li> <img src="img/Nuit.png" alt="Nuit" id="theme"></li>
            </ul>
        </nav>


        <div id = "Mus">
            <img src="img/Music_logo_on.png" alt="MusicON" id="iconM">
            <audio id="Musique">
                <source src="musique/NMWI.mp3" type="audio/mp3">
                Votre navigateur ne prend pas en charge la balise audio.
            </audio> 
        </div>

        

    </header>
    
    <main class="articles">
        <section id="bloc_articles">
            <h2>Derniers posts en tendance ! </h2>
            <?php

                // On récupère les posts d'un utilisateur grâce à son id
                $stmt = $bdd->prepare("SELECT id FROM users WHERE username = ?");
                $stmt->execute([$_SESSION['username']]);
                $user_id = $stmt->fetchColumn();    

                $stmt = $bdd->prepare("
                    SELECT a.title, a.content, a.created_at, a.author_id, a.id, 
                        likeCount AS NbLike, commentCount AS NbCommentaire
                    FROM articles a
                    LEFT JOIN (
                        SELECT article_id, COUNT(*) AS likeCount 
                        FROM likes 
                        WHERE like_dislike = 1 
                        GROUP BY article_id
                    ) l ON a.id = l.article_id
                    LEFT JOIN (
                        SELECT article_id, COUNT(*) AS commentCount 
                        FROM comments 
                        GROUP BY article_id
                    ) c ON a.id = c.article_id
                    ORDER BY NbLike DESC, NbCommentaire DESC
                ");
                $stmt->execute();

                // Récupère les résultats dans un tableau
                $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Si le tableau est vide alors un petit message s'affiche
                if (empty($articles)) {
                    echo "<p> C'est bien vide ici...</p>";
                } else {
                    // Sinon les posts vont s'afficher un à un grâce à la boucle for
                    foreach ($articles as $article) {
                        $stmtLikes = $bdd->prepare("SELECT COUNT(*) FROM likes WHERE article_id = ? AND like_dislike = 1");
                        $stmtLikes->execute([$article['id']]);
                        $likeCount = $stmtLikes->fetchColumn();

                        // Compter les dislikes pour cet article
                        $stmtDislikes = $bdd->prepare("SELECT COUNT(*) FROM likes WHERE article_id = ? AND like_dislike = 0");
                        $stmtDislikes->execute([$article['id']]);
                        $dislikeCount = $stmtDislikes->fetchColumn();

                        if ($article["author_id"]==$user_id) { //Juste un effet pour signaler que c'est le tiens 
                            echo '<article class="tonArticle">';
                            echo '<h3>' . $article['title'] . '</h3>';
                            echo '<p>' . $article['content'] . '</p>';
                            echo '<p class="date"><em>Publié le ' . date('d/m/Y', strtotime($article['created_at'])) . '</em></p>';
                            echo ' <a href="article_page.php?article_id=' . $article['id'] . '">commentaire</a>';
                            echo '<a href="like.php?article_id=' . $article['id'] . '&action=like"> <img src="img/like.png" alt="like" class="likeUdislike">'.$likeCount.'</a>';
                            echo '<a href="like.php?article_id=' . $article['id'] . '&action=dislike"> <img src="img/dislike.png" alt="dislike" class="likeUdislike">'.$dislikeCount.' </a>';
                            echo '</article>';
                        }
                        else {
                            echo '<article class="article">';
                            echo '<h3>' . $article['title'] . '</h3>';
                            echo '<p>' . $article['content'] . '</p>';
                            echo '<p class="date"><em>Publié le ' . date('d/m/Y', strtotime($article['created_at'])) . '</em></p>';
                            echo ' <a href="article_page.php?article_id=' . $article['id'] . '">commentaire</a>';
                            echo '<a href="like.php?article_id=' . $article['id'] . '&action=like"> <img src="img/like.png" alt="like" class="likeUdislike">'.$likeCount.'</a>';
                            echo '<a href="like.php?article_id=' . $article['id'] . '&action=dislike"> <img src="img/dislike.png" alt="dislike" class="likeUdislike">'.$dislikeCount.' </a>';
                            echo '</article>';
                        }
                    }
                }
            

            ?>       
        </section>
    </main>
                   
    <footer>
        <p>&copy; 2025 Blog de Lakshya et Nissi. Tous droits réservés.</p>
    </footer>
        <script src="Mode_jour_nuit.js"></script>    

        <script src="background_musique.js"></script>
</body>

</html>