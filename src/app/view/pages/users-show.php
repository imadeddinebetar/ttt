<?php
$title = 'User: ' . $user['first_name'] . ' ' . $user['last_name'];
require __DIR__ . '/../templates/header.php';
?>

<body>

    <main>
        Show User Page:
        <?php

        echo "<br>";
        echo "User ID: " . $user['id'] . " - Name: " . $user['first_name'] . " " . $user['last_name'];

        ?>


    </main>



    <?php require __DIR__ . '/../templates/footer.php'; ?>