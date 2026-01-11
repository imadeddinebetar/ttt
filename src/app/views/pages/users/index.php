<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title) ?></title>
</head>

<body>
    <?php foreach ($users as $user): ?>
        <article>
            <h2><?= e($user['first_name']) . ' ' . e($user['last_name']) ?></h2>
            <p><?= e($user['email']) ?></p>
        </article>
    <?php endforeach; ?>
</body>

</html>