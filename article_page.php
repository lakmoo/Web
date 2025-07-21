<?php
session_start();
include('config.php');

if (!isset($_GET['article_id'])) {
    echo "Article non trouvé.";
    exit;
}

$article_id = $_GET['article_id'];

// Récupérer l'article
$stmt = $bdd->prepare("SELECT title, content, created_at, id FROM articles WHERE id = ?");
$stmt->execute([$article_id]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$article) {
    echo "Article introuvable.";
    exit;
}

// Récupérer les commentaires de l'article
$stmt = $bdd->prepare("SELECT u.username, c.com, c.created_at FROM comments c 
                       INNER JOIN users u ON c.user_id = u.id 
                       WHERE c.article_id = ? 
                       ORDER BY c.created_at DESC");
$stmt->execute([$article_id]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $article['title']; ?></title>
    <link rel="stylesheet" href="projet_V1.css">
</head>

<body>
    <header>
        <?php if (isset($_SESSION['username'])): ?>
            <div class="welcome-message">
                <p><a href="Home_page.php"><?php echo $_SESSION['username']; ?> </a></p>
            </div>
        <?php endif; ?>
        <h1>Article : <?php echo $article['title']; ?></h1>
        <nav>
            <ul>
                <li><a href="projet_V1.php">Accueil</a></li>
                <li><a href="amis.php">Amis</a></li>
                <li><a href="creerArticle.php">Créer un article</a></li>
                <li><a href="deconnexionPage.php">Déconnexion</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section>
            <?php


                // Si le tableau est vide alors un petit message s'affiche
                if (empty($article)) {
                    echo "<p> Existe pas ! </p>";
                } else {
                    $stmtLikes = $bdd->prepare("SELECT COUNT(*) FROM likes WHERE article_id = ? AND like_dislike = 1");
                    $stmtLikes->execute([$article['id']]);
                    $likeCount = $stmtLikes->fetchColumn();

                    // Compter les dislikes pour cet article
                    $stmtDislikes = $bdd->prepare("SELECT COUNT(*) FROM likes WHERE article_id = ? AND like_dislike = 0");
                    $stmtDislikes->execute([$article['id']]);
                    $dislikeCount = $stmtDislikes->fetchColumn();
                    echo '<article class="article">';
                    echo '<h3>' . $article['title'] . '</h3>';
                    echo '<p>' . $article['content'] . '</p>';
                    echo '<p class="date"><em>Publié le ' . date('d/m/Y', strtotime($article['created_at'])) . '</em></p>';
                    echo '<a href="like.php?article_id=' . $article['id'] . '&action=like"> <img src="img/like.png" alt="like" class="likeUdislike">'.$likeCount.'</a>';
                    echo '<a href="like.php?article_id=' . $article['id'] . '&action=dislike"> <img src="img/dislike.png" alt="dislike" class="likeUdislike">'.$dislikeCount.' </a>';
                    echo '</article>';
                }
                
                
            ?>       
        </section>

        <section class = "article">
            <h3>Commentaires</h3>
            <?php if (count($comments) > 0): ?>
                <ul>
                    <?php foreach ($comments as $comment): ?>
                        <li>
                            <strong><?php echo $comment['username']; ?> :</strong>
                            <p><?php echo $comment['com']; ?></p>
                            <p><em>Le <?php echo date('d/m/Y', strtotime($comment['created_at'])); ?></em></p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>Aucun commentaire pour cet article.</p>
            <?php endif; ?>

            <?php if (isset($_SESSION['username'])): ?>
                <form method="POST" action="add_comment.php">
                    <textarea name="commentaire" placeholder="Ajoutez un commentaire" required></textarea>
                    <input type="hidden" name="article_id" value="<?php echo $article_id; ?>">
                    <button type="submit">Ajouter un commentaire</button>
                </form>
            <?php else: ?>
                <p><a href="connexionPage.php">Connectez-vous</a> pour ajouter un commentaire.</p>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 Blog de Lakshya et Nissi. Tous droits réservés.</p>
    </footer>
</body>

</html>
