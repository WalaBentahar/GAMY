<?php
// Connexion à la base de données
try {
    $pdo = new PDO("mysql:host=localhost;dbname=gamy_bd", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Récupération des données
$result = $pdo->query("SELECT * FROM produits");
$data = $result->fetchAll(PDO::FETCH_ASSOC);

// En-têtes CSV
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="produits.csv"');

// Ouvre un fichier PHP pour écrire
$output = fopen('php://output', 'w');

// En-têtes des colonnes
fputcsv($output, array_keys($data[0]));

// Données des produits
foreach ($data as $row) {
    fputcsv($output, $row);
}

// Fermer le fichier
fclose($output);
?>
