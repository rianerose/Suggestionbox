<?php
require_once __DIR__ . '/config.php';

$isLoggedIn = !empty($_SESSION['user']);
$startHref = $isLoggedIn ? 'dashboard/index.php' : 'login/index.php';
$exitHref = $isLoggedIn ? 'logout.php' : 'signup/index.php';
$startLabel = $isLoggedIn ? 'Dashboard' : 'Start';
$exitLabel = $isLoggedIn ? 'Logout' : 'Sign Up';
$infoMessage = isset($_GET['logout']) ? 'You have been logged out successfully.' : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./vars.css">
  <link rel="stylesheet" href="./style.css">
  <style>
    a,
    button,
    input,
    select,
    h1,
    h2,
    h3,
    h4,
    h5,
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      border: none;
      text-decoration: none;
      background: none;
      -webkit-font-smoothing: antialiased;
    }

    menu,
    ol,
    ul {
      list-style-type: none;
      margin: 0;
      padding: 0;
    }
  </style>
  <title>Welcome</title>
</head>
<body>
  <div
    class="body-index"
    style="
      background: url(body-index.png) center;
      background-size: contain;
      background-repeat: no-repeat;
    "
  >
    <?php if ($infoMessage): ?>
      <div class="form-alert form-alert--success landing-alert">
        <p><?= htmlspecialchars($infoMessage) ?></p>
      </div>
    <?php endif; ?>
    <nav class="nav" aria-label="Main navigation">
      <a class="a" href="<?= htmlspecialchars($startHref, ENT_QUOTES) ?>">
        <span class="start"><?= htmlspecialchars($startLabel) ?></span>
      </a>
      <a class="a2" href="<?= htmlspecialchars($exitHref, ENT_QUOTES) ?>">
        <span class="exit"><?= htmlspecialchars($exitLabel) ?></span>
      </a>
    </nav>
  </div>
</body>
</html>
