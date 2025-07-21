<?php
session_start();
include('config.php');

if (!isset($_SESSION['username'])) {
    //Si l'utilisateur n'est pas connecté
    header("Location: connexionPage.php");
    exit;
}

// Recherche des utilisateurs en fonction de ce qui est saisi
$search = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search'])) {
    $search = $_POST['search']; // Récupère la valeur de la recherche
}

// Préparation de la requête SQL pour récupérer les utilisateurs correspondants
if (!empty($search)) {
    $stmt = $bdd->prepare("SELECT * FROM users WHERE username LIKE ?");
    $stmt->execute(["%$search%"]);
} else {
    $stmt = $bdd->prepare("SELECT * FROM users");
    $stmt->execute();
}
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> <?php echo $_SESSION['username']; ?> Home page  </title>
    <link rel="stylesheet" href="projet_V1.css">
    <script src="Mode_jour_nuit.js"></script>  
</head>

<body>
    <header>
        <h1> Home page : <?php echo $_SESSION['username']; ?></h1>
        <nav>
            <ul>
                <li><a href="projet_V1.php">Accueil</a></li>
                <li><a href="amis.php">Amis</a></li>
                <li><a href="creerArticle.php">Crée un post</a></li>
                <li><a href="creerArticle.html">likes & comments</a></li>
                <li><a href="deconnexionPage.php">Déconnexion</a></li>
                <li> <img src="img/Nuit.png" alt="Nuit" id="theme"></li>
            </ul>
        </nav>
    </header>

    <main>
        <section>
            <h1>Bienvenue sur votre page de profil !</h1>
            <br>
            <h1>Vos posts</h1>
              <?php
                // Un utilisateur non connecté ne peut voir les posts(Home_page est normalement pas trouvable sans connexion mais c'est juste au cas ou)
            
                    // On récupère les posts d'un utilisateur grâce à son id
                    $stmt = $bdd->prepare("SELECT id FROM users WHERE username = ?");
                    $stmt->execute([$_SESSION['username']]);
                    $user_id = $stmt->fetchColumn();

                    if ($user_id) {
                        $stmt = $bdd->prepare("
                            SELECT title, content, created_at,id
                            FROM articles 
                            WHERE author_id = ?
                            ORDER BY created_at DESC");
                        $stmt->execute([$user_id]);

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
                    } else {
                        echo "<p> Utilisateur introuvable :o </p>";
                    }
            ?>       
        </section>


        <?php if (!empty($users)): ?>
            <h3>Utilisateur connecter : </h3>
                <form method="POST">
                    <input type="text" name="search" value="<?php echo $search; ?>" placeholder="Rechercher un utilisateur">
                    <button type="submit">Rechercher</button>
                </form>
            <ul>
                <?php foreach ($users as $user): ?>
                    <?php if ($user['username'] === $_SESSION['username']){?> 
                        <li>
                            <a href="Home_page.php">
                                <?php echo $user['username']; ?>
                            </a>
                        </li>
                    <?php } else { ?>
                        <li>
                            <a href="Home_page_illusion.php?user=<?php echo $user['username']; ?>">
                                <?php echo $user['username']; ?>
                            </a>
                        </li>
                    <?php }  ?>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Aucun utilisateur trouvé.</p>
        <?php endif; ?>

        
    </main>

    <footer>
        <p>&copy; 2025 Blog de Lakshya et Nissi. Tous droits réservés.</p>
    </footer>
</body>

</html>
