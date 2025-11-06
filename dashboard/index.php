<?php
require_once __DIR__ . '/../config.php';

ensure_logged_in();

$username = htmlspecialchars((string)($_SESSION['user'] ?? ''), ENT_QUOTES);
$flashSuccess = null;

if (!empty($_SESSION['flash_success'])) {
    $flashSuccess = (string)$_SESSION['flash_success'];
    unset($_SESSION['flash_success']);
}
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
  <title>Dashboard</title>
</head>
<body>
  <div class="dashboard">
    <header class="dashboard__header">
      <img class="dashboard__logo" src="../images/logo-login.png" alt="Dashboard">
      <div class="dashboard__user">
        <span class="dashboard__welcome">Welcome, <?= $username ?>!</span>
        <a class="dashboard__logout" href="../logout.php">Logout</a>
      </div>
    </header>

    <?php if ($flashSuccess): ?>
      <div class="form-alert form-alert--success dashboard__flash">
        <p><?= htmlspecialchars($flashSuccess) ?></p>
      </div>
    <?php endif; ?>

    <main class="dashboard__main">
      <section class="dashboard__card">
        <h1 class="dashboard__title">Dashboard</h1>
        <p class="dashboard__text">
          You are now logged in. Use the navigation above to manage your session or return to the home page.
        </p>
        <div class="dashboard__actions">
          <a class="dashboard__action" href="../index.php">Go to Home</a>
        </div>
      </section>
    </main>
  </div>
</body>
</html>
