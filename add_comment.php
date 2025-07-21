<?php
session_start();
include('config.php');
//Faut quand meme vérifier si les prérequis sont la (La personne qui écrit/ dans qu'elle article / commentaire)
if (!isset($_SESSION['username']) || !isset($_POST['commentaire']) || !isset($_POST['article_id'])) {
    echo "Erreur";
    exit;
}

$current_user = $_SESSION['username'];
$commentaire = $_POST['commentaire'];
$article_id = $_POST['article_id'];

// Récupérer l'ID de l'utilisateur connecté
$stmt = $bdd->prepare("SELECT id FROM users WHERE username = ?");
$stmt->execute([$current_user]);
$current_user_id = $stmt->fetchColumn();

// Insérer le commentaire
$stmt = $bdd->prepare("INSERT INTO comments (user_id, article_id, com) VALUES (?, ?, ?)");
$stmt->execute([$current_user_id, $article_id, $commentaire]);

// Rediriger vers l'article
header("Location: article_page.php?article_id=" . $article_id);
exit;
