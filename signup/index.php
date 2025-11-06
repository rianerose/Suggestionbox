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
    $confirmPassword = (string)($_POST['confirm_password'] ?? '');

    if ($username === '') {
        $errors[] = 'Username is required.';
    }

    if ($password === '') {
        $errors[] = 'Password is required.';
    }

    if (strlen($password) > 0 && strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters.';
    }

    if ($password !== $confirmPassword) {
        $errors[] = 'Passwords do not match.';
    }

    if (!$errors) {
        $result = register_user($username, $password);

        if ($result['success']) {
            $_SESSION['user'] = sanitize_username($username);
            $_SESSION['flash_success'] = 'Account created successfully!';
            header('Location: ../dashboard/index.php');
            exit;
        }

        if (!empty($result['error'])) {
            $errors[] = $result['error'];
        }
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
  <link rel="stylesheet" href="../login/vars.css">
  <link rel="stylesheet" href="../login/style.css">
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
  <title>Sign Up</title>
</head>
<body>
  <div
    class="body-login signup-background"
  >
    <form class="login-form" method="post" novalidate>
      <img class="logo" src="../images/logo-login.png" alt="Sign up">

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
          autocomplete="new-password"
          required
        >
      </label>

      <label class="field-group">
        <span class="label-text">Confirm Password:</span>
        <input
          class="text-input"
          type="password"
          name="confirm_password"
          autocomplete="new-password"
          required
        >
      </label>

      <div class="button-group">
        <button class="login-button" type="submit">Create Account</button>
        <a class="signup-button" href="../login/index.php">Back to Login</a>
      </div>
    </form>
  </div>
</body>
</html>
