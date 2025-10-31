<?php
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php $page = isset($_GET['page']) ? $_GET['page'] : 'home'; ?>
  <title>FootTime<?php echo $page !== 'home' ? ' - ' . htmlspecialchars($page) : ''; ?></title>
  <link rel="stylesheet" href="assets/css/Style.css">
</head>
<body>
  <?php require __DIR__ . '/includes/Navbar.php'; ?>

  <div class="ft-shell">
    <main class="ft-content" aria-label="Contenu principal">
      
    </main>
  </div>

  <?php require __DIR__ . '/includes/Footer.php'; ?>
</body>
</html>

