<?php
session_start();
include('config.php');

// Vérification si l'utilisateur est connecté
if (!isset($_SESSION['username'])) {
    echo "Pas connecté ";
    exit;
}

$user_id = $_SESSION['user_id']; // ID de l'utilisateur connecté
$article_id = $_GET['article_id']; // ID de l'article 
$action = $_GET['action']; // (like ou dislike)

// Vérification si l'utilisateur a déjà intéragit avec l'article
$stmt = $bdd->prepare("SELECT id, like_dislike FROM likes WHERE user_id = ? AND article_id = ?");
$stmt->execute([$user_id, $article_id]);
$like_dislike = $stmt->fetch(PDO::FETCH_ASSOC);

if ($like_dislike) {
    // Si un like ou dislike existe déjà, on gère le changement
    if ($like_dislike['like_dislike'] == 1 && $action == 'like') {
        // L'utilisateur a déjà liké => on supprime 
        $stmt = $bdd->prepare("DELETE FROM likes WHERE id = ?");
        $stmt->execute([$like_dislike['id']]);
    } elseif ($like_dislike['like_dislike'] == 0 && $action == 'dislike') {
        // L'utilisateur a déjà disliké => on supprime 
        $stmt = $bdd->prepare("DELETE FROM likes WHERE id = ?");
        $stmt->execute([$like_dislike['id']]);
    } elseif ($like_dislike['like_dislike'] == 0 && $action == 'like') {
        // L'utilisateur avait disliké => on remplace par un like
        $stmt = $bdd->prepare("UPDATE likes SET like_dislike = 1 WHERE id = ?");
        $stmt->execute([$like_dislike['id']]);
    } elseif ($like_dislike['like_dislike'] == 1 && $action == 'dislike') {
        // L'utilisateur avait liké => on remplace par un dislike
        $stmt = $bdd->prepare("UPDATE likes SET like_dislike = 0 WHERE id = ?");
        $stmt->execute([$like_dislike['id']]);
    }
} else {
    // Premiere intéraction avec l'article
    if ($action == 'like') {
        $stmt = $bdd->prepare("INSERT INTO likes (user_id, article_id, like_dislike) VALUES (?, ?, 1)");
        $stmt->execute([$user_id, $article_id]);
    } elseif ($action == 'dislike') {
        $stmt = $bdd->prepare("INSERT INTO likes (user_id, article_id, like_dislike) VALUES (?, ?, 0)");
        $stmt->execute([$user_id, $article_id]);
    }
}

header("Location: article_page.php?article_id=" . $article_id); // Redirige vers la page de l'article
exit;
?>
