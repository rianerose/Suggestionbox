<?php
require_once __DIR__ . '/../config.php';

if (!empty($_SESSION['user'])) {
    header('Location: ../dashboard/index.php');
    exit;
}

$errors = [];
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    if ($username === '') {
        $errors[] = 'Username is required.';
    }

    if ($password === '') {
        $errors[] = 'Password is required.';
    }

    if (!$errors) {
        if (authenticate_user($username, $password)) {
            $_SESSION['user'] = sanitize_username($username);
            header('Location: ../dashboard/index.php');
            exit;
        }

        $errors[] = 'Invalid username or password.';
    }
}

$usernameValue = htmlspecialchars($username, ENT_QUOTES);
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
  <title>Login</title>
</head>
<body>
  <div
    class="body-login"
    style="
      background: url(body-login.png) center;
      background-size: contain;
      background-repeat: no-repeat;
    "
  >
    <form class="login-form" method="post" novalidate>
      <img class="logo" src="../images/logo-login.png" alt="Log in">

      <?php if ($errors): ?>
        <div class="form-alert form-alert--error">
          <?php foreach ($errors as $error): ?>
            <p><?= htmlspecialchars($error) ?></p>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <label class="field-group">
        <span class="label-text">Username:</span>
        <input
          class="text-input"
          type="text"
          name="username"
          value="<?= $usernameValue ?>"
          autocomplete="username"
          required
        >
      </label>

      <label class="field-group">
        <span class="label-text">Password:</span>
        <input
          class="text-input"
          type="password"
          name="password"
          autocomplete="current-password"
          required
        >
      </label>

      <div class="button-group">
        <button class="login-button" type="submit">Login</button>
        <a class="signup-button" href="../signup/index.php">Sign Up</a>
      </div>
    </form>
  </div>
</body>
</html>
