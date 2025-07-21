<?php  
session_start();
include('config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password']; 

    // Vérification si l'utilisateur existe déjà
    $stmt = $bdd->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Si l'utilisateur existe déjà, vérifie le mot de passe 
        if ($password == $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header('Location: projet_V1.php');
            exit;
        } else {
            $error = 'Mot de passe incorrect.';
        }
    } else {
        $error = 'Utilisateur non trouvé.';
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="conPage.css">
</head>
<body>
    
    <div id="container">
        <h1 class="acc"><a href="projet_V1.php" class="acc">Accueil</a></h1>
        <h2>Connexion</h2>
        <?php if (isset($error)): ?>
            <p><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Nom d'utilisateur" required>
            <input type="password" name="password" placeholder="Mot de passe" required>
            <button type="submit">Se connecter</button>
        </form>
        <p>Pas encore de compte ? <a href="create_account.php">Créez un compte ici</a>.</p>
    </div>
</body>
</html>
