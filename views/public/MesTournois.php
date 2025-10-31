<?php
$tournaments = [
    ["id" => 1, "name" => "Tournoi Inter-Equipes", "date" => "2025-11-02", "teams" => 8],
    ["id" => 2, "name" => "Coupe du Printemps", "date" => "2025-04-12", "teams" => 8],
    ["id" => 3, "name" => "Championnat Été", "date" => "2025-07-01", "teams" => 8],
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Tournois</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <header class="page-header">
        <h1>Mes Tournois</h1>
    </header>

    <section class="tournament-list">
        <?php foreach ($tournaments as $t) : ?>
            <div class="tournament-card">
                <h2><?= htmlspecialchars($t['name']) ?></h2>
                <div class="tournament-info">
                    <p><strong>Date :</strong> <?= htmlspecialchars($t['date']) ?></p>
                    <p><strong>Équipes :</strong> <?= htmlspecialchars($t['teams']) ?></p>
                </div>
                <a href="tournoi.php?id=<?= urlencode($t['id']) ?>" class="details-btn">Détails</a>
            </div>
        <?php endforeach; ?>
    </section>
</body>
</html>
