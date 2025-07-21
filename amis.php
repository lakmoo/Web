<?php
session_start();
include('config.php');

if (!isset($_SESSION['username'])) {
    header("Location: connexionPage.php");
    exit;
}

$current_user = $_SESSION['username'];

// Récupère son ID
$stmt = $bdd->prepare("SELECT id FROM users WHERE username = ?");
$stmt->execute([$current_user]);
$current_user_id = $stmt->fetchColumn();

// Récupère tous ses amis
$stmt = $bdd->prepare("
    SELECT u.username FROM users u
    INNER JOIN friends f ON u.id = f.friend_id
    WHERE f.user_id = ?
");
$stmt->execute([$current_user_id]);
$friends = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupère tout le monde (sauf nous)
$stmt = $bdd->prepare("SELECT username FROM users WHERE username != ?");
$stmt->execute([$current_user]);
$all_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title> Amis</title>
    <link rel="stylesheet" href="projet_V1.css">
    <script src="Mode_jour_nuit.js" ></script>
</head>
<body>
    <header>
        <?php if (isset($_SESSION['username'])): ?>
                <div class="welcome-message">
                    <p><a href="Home_page.php"><?php echo $_SESSION['username']; ?> </a></p>
                </div>
        <?php endif; ?>
        <h1>Amis</h1>
        <nav>
            <ul>
                <li><a href="projet_V1.php">Accueil</a></li>
                <li><a href="creerArticle.php">Créer un article</a></li>
                <li><a href="deconnexionPage.php">Déconnexion</a></li>
                <li><img src="img/Nuit.png" alt="Thème" id="theme"></li>
            </ul>
        </nav>
    </header>

    <main>


        <section>
            <h2>T'es amis ! </h2>
            <ul>
                <?php foreach ($all_users as $user): ?>
                    <?php
                        $username = $user['username'];
                        $your_friend = false;
                        $is_friend = false;
                        $you_request = false;
                        $is_request = false;

                        // L'utilisateur connecté a ajouté cette personne ?
                        foreach ($friends as $friend) {
                            if ($friend['username'] === $username) {
                                $is_friend = true;
                                break;
                            }
                        }

                        // T'as t-il ajouté en retour ?
                        $stmt = $bdd->prepare("
                            SELECT COUNT(*) FROM friends 
                            WHERE user_id = (SELECT id FROM users WHERE username = ?) 
                            AND friend_id = ?
                        ");
                        $stmt->execute([$username, $current_user_id]);
                        $wait_friend = $stmt->fetchColumn() != 0;
                        //Il est ton amis MAIS toi tu ne l'es pas ? => Il attend une réponse
                        if (!$is_friend && $wait_friend) {
                            $you_request = true;
                        }
                        //Il est pas ton amis MAIS toi tu l'es ? => Tu attend une réponse
                        elseif ($is_friend && !$wait_friend) {
                            $is_request = true;
                        }
                        //Il est  ton amis ET toi tu l'es aussi ? => Vous etes amis 
                        elseif ($is_friend && $wait_friend) {
                            $your_friend = true;
                        }

                    ?>

                    <?php if ($your_friend) {?>
                    <li>
                        <a href="Home_page_illusion.php?user=<?php echo $username ; ?>">
                            <?php echo $username; ?>
                        </a>
                    </li>
                    <?php } ?>
                    
                <?php endforeach; ?>
            </ul>
        </section>

        <section>
            <h2>En attente </h2>
            <ul>
                <?php foreach ($all_users as $user): ?>
                    <?php
                        $username = $user['username'];
                        $your_friend = false;
                        $is_friend = false;
                        $you_request = false;
                        $is_request = false;

                        // L'utilisateur connecté a ajouté cette personne ?
                        foreach ($friends as $friend) {
                            if ($friend['username'] === $username) {
                                $is_friend = true;
                                break;
                            }
                        }

                        // T'as t-il ajouté en retour ?
                        $stmt = $bdd->prepare("
                            SELECT COUNT(*) FROM friends 
                            WHERE user_id = (SELECT id FROM users WHERE username = ?) 
                            AND friend_id = ?
                        ");
                        $stmt->execute([$username, $current_user_id]);
                        $wait_friend = $stmt->fetchColumn() != 0;
                        //Il est ton amis MAIS toi tu ne l'es pas ? => Il attend une réponse
                        if (!$is_friend && $wait_friend) {
                            $you_request = true;
                        }
                        //Il est pas ton amis MAIS toi tu l'es ? => Tu attend une réponse
                        elseif ($is_friend && !$wait_friend) {
                            $is_request = true;
                        }
                        //Il est  ton amis ET toi tu l'es aussi ? => Vous etes amis 
                        elseif ($is_friend && $wait_friend) {
                            $your_friend = true;
                        }

                    ?>
                    <li>
                        <?php if ($you_request || $is_request ) {?>
                            <a href="Home_page_illusion.php?user=<?php echo $username ; ?>">
                                <?php echo $username; ?>
                            </a>
                            
                        <?php } ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>                    

        <section>
            <h2>Liste utilisateurs </h2>
            <ul>
                <?php foreach ($all_users as $user): ?>
                    <?php
                        $username = $user['username'];
                        $your_friend = false;
                        $is_friend = false;
                        $you_request = false;
                        $is_request = false;

                        // L'utilisateur connecté a ajouté cette personne ?
                        foreach ($friends as $friend) {
                            if ($friend['username'] === $username) {
                                $is_friend = true;
                                break;
                            }
                        }

                        // T'as t-il ajouté en retour ?
                        $stmt = $bdd->prepare("
                            SELECT COUNT(*) FROM friends 
                            WHERE user_id = (SELECT id FROM users WHERE username = ?) 
                            AND friend_id = ?
                        ");
                        $stmt->execute([$username, $current_user_id]);
                        $wait_friend = $stmt->fetchColumn() != 0;
                        //Il est ton amis MAIS toi tu ne l'es pas ? => Il attend une réponse
                        if (!$is_friend && $wait_friend) {
                            $you_request = true;
                        }
                        //Il est pas ton amis MAIS toi tu l'es ? => Tu attend une réponse
                        elseif ($is_friend && !$wait_friend) {
                            $is_request = true;
                        }
                        //Il est  ton amis ET toi tu l'es aussi ? => Vous etes amis 
                        elseif ($is_friend && $wait_friend) {
                            $your_friend = true;
                        }
                        //sinon on laisse, pas besoin de changer vous etes des inconnues 

                    ?>
                    <li>
                        <?php if (!$is_friend && !$you_request) {?>
                            <a href="Home_page_illusion.php?user=<?php echo $username ; ?>">
                                <?php echo $username;  ?>
                            </a>
                            
                        <?php } ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>                    
                        







    </main>

    <footer>
        <p>&copy; 2025 Blog de Lakshya et Nissi. Tous droits réservés.</p>
    </footer>
</body>
</html>
