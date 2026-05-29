<?php
// fonctions.php
require_once 'config.php';

// Force le fuseau horaire sur Lomé
date_default_timezone_set('Africa/Lome');

function verifierDisponibiliteSalades() {
    $jourActuel = (int)date('N'); 
    // Autorisé du Lundi (1) au Mercredi (3)
    return ($jourActuel >= 1 && $jourActuel <= 3);
}

function recupererCatalogue($pdo) {
    $query = $pdo->query("SELECT * FROM produits");
    return $query->fetchAll(PDO::FETCH_ASSOC);
}