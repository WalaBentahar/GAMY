<?php
require_once '../../config.php';

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=guides_videos.xls");
header("Pragma: no-cache");
header("Expires: 0");

// Connexion à la base de données
$stmt = config::getConnexion()->query("SELECT id, title, author, created_at, 'Texte' as type FROM guides UNION SELECT id, title, author, created_at, 'Video' as type FROM videoguides ORDER BY id DESC");
$guides = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Génération du contenu Excel via HTML
echo "<table border='1'>";
echo "<tr><th>ID</th><th>Titre</th><th>Auteur</th><th>Type</th><th>Date</th></tr>";

foreach ($guides as $g) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($g['id']) . "</td>";
    echo "<td>" . htmlspecialchars($g['title']) . "</td>";
    echo "<td>" . htmlspecialchars($g['author']) . "</td>";
    echo "<td>" . htmlspecialchars($g['type']) . "</td>";
    echo "<td>" . htmlspecialchars($g['created_at']) . "</td>";
    echo "</tr>";
}

echo "</table>";
exit;
?>