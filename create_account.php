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
        // Si l'utilisateur existe déjà, le signaler 
        $error = 'Cette utilisateur existe deja !';
    } else {
        //Sinon, on le crée ! 
        $stmt = $bdd->prepare("INSERT INTO users(username, password) VALUES (? , ?)");
        $stmt->execute([$username, $password]);
        //Connexion automatique (car c'est un site pratique)
        $_SESSION['username'] = $username;
        $_SESSION['password'] = $password; 

        header('Location: projet_V1.php');
        exit;
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Création du compte</title>
    <link rel="stylesheet" href="conPage.css">
</head>
<body>
    <div id="container">
        <h1>Création</h1>
        <?php if (isset($error)): ?>
            <p><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Nom d'utilisateur" required>
            <input type="password" name="password" placeholder="Mot de passe" required>
            <button type="submit">Crée le compte</button>
        </form>
    </div>
</body>
</html>
