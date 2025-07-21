<?php
session_start();
include('config.php');

if (!isset($_SESSION['username']) || !isset($_GET['target_user'])) {
    if (!isset($_SESSION['username'])){
        echo "bobob"; //Juste une série de test pour vérifier qu'on atterie sur cette page avec les bonnes info
    }
    if (!isset($_SESSION['target_user'])){
        echo "bababa";
        echo $_GET['target_user'];
    }
    echo "Requête invalide.";
    exit;
}

$current_username = $_SESSION['username'];
$target_username = $_GET['target_user'];

// Récupération des IDs
$stmt = $bdd->prepare("SELECT id FROM users WHERE username = ?");
$stmt->execute([$current_username]);
$current_user_id = $stmt->fetchColumn();

$stmt = $bdd->prepare("SELECT id FROM users WHERE username = ?");
$stmt->execute([$target_username]);
$target_user_id = $stmt->fetchColumn();

// Vérifie si la relation existe déjà
$stmt = $bdd->prepare("SELECT COUNT(*) FROM friends WHERE user_id = ? AND friend_id = ?");
$stmt->execute([$current_user_id, $target_user_id]);
$exists = $stmt->fetchColumn();

if ($exists == 0) {//Si pas de relation, alors c'est bon
    $insert = $bdd->prepare("INSERT INTO friends (user_id, friend_id) VALUES (?, ?)");
    $insert->execute([$current_user_id, $target_user_id]);
}

// Redirige vers la page de profil
header("Location: Home_page_illusion.php?user=" . $target_username);
exit;
?>
