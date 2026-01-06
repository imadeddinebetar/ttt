<?php
// using your Model methods (they use the same PDO instance so participate in the transaction)
try {
    $result = \App\Core\DB::transaction(function(\PDO $db) {
        $userModel = new \App\Models\User();
        $profileModel = new \App\Models\Profile();

        $user = $userModel->insert(['name' => 'Bob', 'email' => 'bob@example.com']);
        if (empty($user)) {
            throw new \Exception('User insert failed');
        }

        $profile = $profileModel->insert(['user_id' => $user['id'], 'bio' => '...']);
        if (empty($profile)) {
            throw new \Exception('Profile insert failed');
        }

        return ['user' => $user, 'profile' => $profile];
    });
} catch (\Throwable $e) {
    // transaction rolled back, handle error
}