<?php
require_once __DIR__ . '/config/db.php';

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $users = [];

    // Admins
    for ($i = 1; $i <= 3; $i++) {
        $users[] = [
            'nom' => 'Admin ' . $i,
            'email' => 'admin' . $i . '@test.com',
            'mot_de_passe' => 'Admin' . $i . 'Admin' . $i,
            'role' => 'admin',
        ];
    }

    // Users
    for ($i = 1; $i <= 10; $i++) {
        $users[] = [
            'nom' => 'User ' . $i,
            'email' => 'user' . $i . '@test.com',
            'mot_de_passe' => 'User' . $i . 'User' . $i,
            'role' => 'user',
        ];
    }

    $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
    $insertStmt = $pdo->prepare("
        INSERT INTO users (nom, email, mot_de_passe, role)
        VALUES (:nom, :email, :mot_de_passe, :role)
    ");

    $created = 0;
    $skipped = 0;

    foreach ($users as $user) {
        $checkStmt->execute([
            ':email' => $user['email']
        ]);

        if ($checkStmt->fetch()) {
            $skipped++;
            continue;
        }

        $insertStmt->execute([
            ':nom' => $user['nom'],
            ':email' => $user['email'],
            ':mot_de_passe' => password_hash($user['mot_de_passe'], PASSWORD_DEFAULT),
            ':role' => $user['role']
        ]);

        $created++;
    }

    echo "<h2>Seed terminé</h2>";
    echo "<p>Utilisateurs créés : {$created}</p>";
    echo "<p>Utilisateurs ignorés (déjà existants) : {$skipped}</p>";

    echo "<h3>Comptes de démo</h3>";
    echo "<ul>";
    echo "<li>admin1@test.com / Admin1Admin1</li>";
    echo "<li>admin2@test.com / Admin2Admin2</li>";
    echo "<li>admin3@test.com / Admin3Admin3</li>";
    echo "<li>user1@test.com / User1User1</li>";
    echo "<li>user2@test.com / User2User2</li>";
    echo "<li>user3@test.com / User3User3</li>";
    echo "<li>user4@test.com / User4User4</li>";
    echo "<li>user5@test.com / User5User5</li>";
    echo "<li>user6@test.com / User6User6</li>";
    echo "<li>user7@test.com / User7User7</li>";
    echo "<li>user8@test.com / User8User8</li>";
    echo "<li>user9@test.com / User9User9</li>";
    echo "<li>user10@test.com / User10User10</li>";
    echo "</ul>";

} catch (PDOException $e) {
    die("Erreur : " . htmlspecialchars($e->getMessage()));
}