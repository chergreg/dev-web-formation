<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/functions.php';

function is_logged_in(): bool
{
    return isset($_SESSION['user_id']);
}

function current_user(): ?array
{
    if (!is_logged_in()) {
        return null;
    }

    return [
        'id' => $_SESSION['user_id'] ?? null,
        'nom' => $_SESSION['user_name'] ?? null,
        'email' => $_SESSION['user_email'] ?? null,
        'role' => $_SESSION['user_role'] ?? null,
    ];
}

function require_login(): void
{
    if (!is_logged_in()) {
        redirect('login.php');
    }
}

function require_admin(): void
{
    require_login();

    if (($_SESSION['user_role'] ?? '') !== 'admin') {
        http_response_code(403);
        echo '<!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <title>Accès refusé</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    padding: 40px;
                    background: #f8f9fa;
                    color: #212529;
                }
                .box {
                    max-width: 600px;
                    margin: 40px auto;
                    background: #fff;
                    border: 1px solid #ddd;
                    border-radius: 8px;
                    padding: 24px;
                    text-align: center;
                }
                a {
                    display: inline-block;
                    margin-top: 16px;
                    text-decoration: none;
                    color: #0d6efd;
                }
            </style>
        </head>
        <body>
            <div class="box">
                <h1>Accès refusé</h1>
                <p>Vous êtes connecté, mais vous n’avez pas l’autorisation d’accéder à cette page.</p>
                <a href="profile.php">Retour au profil</a>
            </div>
        </body>
        </html>';
        exit;
    }
}