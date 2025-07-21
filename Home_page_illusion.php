
<?php
session_start();
include('config.php');

if (!isset($_GET['user'])) {
    echo "Aucun utilisateur spécifié.";
    exit;
}

$target_user = $_GET['user'];

// ID de l'utilisateur connecté
$stmt = $bdd->prepare("SELECT id FROM users WHERE username = ?");
$stmt->execute([$_SESSION['username']]);
$current_user_id = $stmt->fetchColumn();

// ID de l'utilisateur ciblé
$stmt = $bdd->prepare("SELECT id FROM users WHERE username = ?");
$stmt->execute([$target_user]);
$target_user_id = $stmt->fetchColumn();

// Vérifie si l'utilisateur connecté a ajouté le target_user
$stmt = $bdd->prepare("SELECT COUNT(*) FROM friends WHERE user_id = ? AND friend_id = ?");
$stmt->execute([$current_user_id, $target_user_id]);
$sent_exists = $stmt->fetchColumn() > 0;

// Vérifie si le target_user a ajouté l'utilisateur connecté
$stmt = $bdd->prepare("SELECT COUNT(*) FROM friends WHERE user_id = ? AND friend_id = ?");
$stmt->execute([$target_user_id, $current_user_id]);
$received_exists = $stmt->fetchColumn() > 0;

// Déterminer le statut de leur relation 
if ($sent_exists && $received_exists) {
    $friend_status = "Amis";
} elseif ($sent_exists) {
    $friend_status = "Demande envoyée";
} elseif ($received_exists) {
    $friend_status = "En attente de réponse";
} else {
    $friend_status = "Ajouter";
}
?>




<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Page de <?php echo $target_user; ?></title>
    <link rel="stylesheet" href="projet_V1.css">
    <script src="Mode_jour_nuit.js"></script>
</head>
<body>
    <header>
        <?php if (isset($_SESSION['username'])): ?>
                <div class="welcome-message">
                    <p>Bienvenue, <a href="Home_page.php"><?php echo $_SESSION['username']; ?> </a>!</p>
                </div>
        <?php endif; ?>
        <h1>Profil de <?php echo $target_user; ?></h1>
        <nav>
            <ul>
                <li><a href="projet_V1.php">Accueil</a></li>
                <li><a href="amis.php">Amis</a></li>
                <li><a href="creerArticle.html">Posts</a></li>
                <li><a href="creerArticle.html">likes & comments</a></li>
                <li><a href="deconnexionPage.php">Déconnexion</a></li>
                <li>
                    <?php if ($friend_status === "Amis"): ?>
                        <p><img src="img/friend.png" alt="Ami"> </p>
                    <?php elseif ($friend_status === "Demande envoyée"): ?>
                        <p><img src="img/friend_maybe.png" alt="Ami" ></p>
                    <?php elseif ($friend_status === "En attente de réponse"): ?>
                        <a href="add_friend.php?target_user=<?php echo $target_user; ?>"><img src="img/no_friend.png" alt="Ami" id="friend"></a>
                    <?php else: ?>
                        <a href="add_friend.php?target_user=<?php echo $target_user; ?>"><img src="img/no_friend.png" alt="Ami" id="friend"></a>
                    <?php endif; ?>
                </li>
                <li> <img src="img/Nuit.png" alt="Nuit" id="theme"></li>
            </ul>
        </nav>
    </header>

    <main>

        <section>
            <h2> <?php echo $target_user; ?> profil</h2>
            <?php
                // Un utilisateur non connecté ne peut voir les posts(Home_page est normalement pas trouvable sans connexion mais c'est juste au cas ou)
        
                // On récupère les posts d'un utilisateur grâce à son id
                $stmt = $bdd->prepare("SELECT id FROM users WHERE username = ?");
                $stmt->execute([$target_user_id]);
                $user_id = $stmt->fetchColumn();
                $stmt = $bdd->prepare("
                    SELECT title, content, created_at,id
                    FROM articles 
                    WHERE author_id = ?
                    ORDER BY created_at DESC");
                $stmt->execute([$target_user_id]);

                // Récupère les résultats dans un tableau
                $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Si le tableau est vide alors un petit message s'affiche
                if (empty($articles)) {
                    echo "<p> Vous n'avez encore rien publié :) </p>";
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

                        echo '<article class="article">';
                        echo '<h2>' . $article['title'] . '</h2>';
                        echo '<p>' . $article['content'] . '</p>';
                        echo '<p class="date"><em>Publié le ' . date('d/m/Y', strtotime($article['created_at'])) . '</em></p>';
                        echo '<a href="like.php?article_id=' . $article['id'] . '&action=like"> <img src="img/like.png" alt="like" class="likeUdislike">'.$likeCount.'</a>';
                        echo '<a href="like.php?article_id=' . $article['id'] . '&action=dislike"> <img src="img/dislike.png" alt="dislike" class="likeUdislike">'.$dislikeCount.' </a>';
                        echo '</article>';
                    }
                }
                
            ?>       
        </section>
    </main>

    <footer>
        <p>&copy; 2025 Blog de Lakshya et Nissi. Tous droits réservés.</p>
    </footer>
    <script src="add_friend.js"></script>
        

</body>
</html>
