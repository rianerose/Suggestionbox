<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function sanitize_username(string $username): string
{
    return strtolower(trim($username));
}

function get_db_connection(): \PDO
{
    static $pdo = null;

    if ($pdo instanceof \PDO) {
        return $pdo;
    }

    $host = getenv('DB_HOST') ?: '127.0.0.1';
    $port = getenv('DB_PORT') ?: '3306';
    $database = getenv('DB_NAME') ?: 'my_app';
    $username = getenv('DB_USER') ?: 'my_app_user';
    $password = getenv('DB_PASSWORD') ?: 'secret';
    $charset = 'utf8mb4';

    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', $host, $port, $database, $charset);

    $options = [
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        \PDO::ATTR_EMULATE_PREPARES => false,
    ];

    $pdo = new \PDO($dsn, $username, $password, $options);
    ensure_users_table_exists($pdo);

    return $pdo;
}

function ensure_users_table_exists(\PDO $pdo): void
{
    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS users (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(190) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );
}

/**
 * @return array{username: string, password: string, created_at: string}|null
 */
function find_user(string $username): ?array
{
    $pdo = get_db_connection();
    $sql = 'SELECT username, password, created_at FROM users WHERE username = :username LIMIT 1';
    $statement = $pdo->prepare($sql);
    $statement->execute(['username' => sanitize_username($username)]);

    $user = $statement->fetch();

    return $user === false ? null : $user;
}

/**
 * @return array{success: bool, error?: string}
 */
function register_user(string $username, string $password): array
{
    $username = sanitize_username($username);
    if ($username === '') {
        return ['success' => false, 'error' => 'Username is required.'];
    }

    if (strlen($password) < 6) {
        return ['success' => false, 'error' => 'Password must be at least 6 characters.'];
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);

    try {
        $pdo = get_db_connection();
        $sql = 'INSERT INTO users (username, password) VALUES (:username, :password)';
        $statement = $pdo->prepare($sql);
        $statement->execute([
            'username' => $username,
            'password' => $hash,
        ]);
    } catch (\PDOException $exception) {
        if ((int) $exception->errorInfo[1] === 1062) {
            return ['success' => false, 'error' => 'Username already exists.'];
        }

        error_log('Failed to register user: ' . $exception->getMessage());
        return ['success' => false, 'error' => 'An unexpected error occurred.'];
    }

    return ['success' => true];
}

function authenticate_user(string $username, string $password): bool
{
    $user = find_user($username);
    if ($user === null) {
        return false;
    }

    return password_verify($password, $user['password']);
}

function ensure_logged_in(): void
{
    if (empty($_SESSION['user'])) {
        header('Location: ../login/index.php');
        exit;
    }
}

