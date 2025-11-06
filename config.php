<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

const USER_DATA_FILE = __DIR__ . '/data/users.json';

/**
 * @return array<string, array{username: string, password: string, createdAt: int}>
 */
function load_users(): array
{
    if (!file_exists(USER_DATA_FILE)) {
        return [];
    }

    $json = file_get_contents(USER_DATA_FILE);
    if ($json === false || $json === '') {
        return [];
    }

    try {
        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
    } catch (\JsonException $exception) {
        error_log('Failed to decode users file: ' . $exception->getMessage());
        return [];
    }

    if (!is_array($data)) {
        return [];
    }

    return $data;
}

/**
 * @param array<string, array{username: string, password: string, createdAt: int}> $users
 */
function save_users(array $users): void
{
    $directory = dirname(USER_DATA_FILE);
    if (!is_dir($directory)) {
        mkdir($directory, 0775, true);
    }

    $json = json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    if ($json === false) {
        throw new \RuntimeException('Failed to encode users.');
    }

    $tempFile = USER_DATA_FILE . '.tmp';
    $bytes = file_put_contents($tempFile, $json, LOCK_EX);
    if ($bytes === false) {
        throw new \RuntimeException('Failed to write users data.');
    }

    rename($tempFile, USER_DATA_FILE);
}

function sanitize_username(string $username): string
{
    return strtolower(trim($username));
}

/**
 * @return array{username: string, password: string, createdAt: int}|null
 */
function find_user(string $username): ?array
{
    $users = load_users();
    $key = sanitize_username($username);

    return $users[$key] ?? null;
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

    $users = load_users();

    if (isset($users[$username])) {
        return ['success' => false, 'error' => 'Username already exists.'];
    }

    $users[$username] = [
        'username' => $username,
        'password' => password_hash($password, PASSWORD_DEFAULT),
        'createdAt' => time(),
    ];

    save_users($users);

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
        header('Location: login.php');
        exit;
    }
}

