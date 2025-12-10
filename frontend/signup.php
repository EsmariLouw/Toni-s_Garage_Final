<?php
session_start();

$api = 'https://solace.ist.rit.edu/~it4527/Toni-s_Garage_Final/backend/api.php';
$apiKey = 'YOUR_SUPER_SECRET_KEY_HERE';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $fullName = $_POST['full_name'] ?? '';
    $email    = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    $ch = curl_init($api . '?action=signup');

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Accept: application/json',
            'X-API-Key: ' . $apiKey,
        ],
        CURLOPT_POSTFIELDS => json_encode([
            'full_name'        => $fullName,
            'email'            => $email,
            'password'         => $password,
            'confirm_password' => $confirm,
        ]),
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);

    if (!empty($data['ok'])) {
        header('Location: login.php');
        exit;
    }

    $error = $data['error'] ?? 'Signup failed';
}
?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Sign Up</title>

        <link href="https://fonts.googleapis.com/css?family=Open+Sans:100,300,400,700&display=swap" rel="stylesheet" />
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" />

        <link rel="stylesheet" href="loginstyles.css" />
    </head>

    <body>

    <form class="login-form" action="signup.php" method="post" novalidate>
        <p class="login-text" aria-hidden="true">
            <span class="fa-stack fa-lg" aria-hidden="true">
                <i class="fa fa-circle fa-stack-2x"></i>
                <i class="fa fa-user-plus fa-stack-1x"></i>
            </span>
        </p>

        <input type="text" class="login-username" name="full_name" autocomplete="name" required placeholder="Full name" aria-label="Full name" maxlength="60" />
        <input type="email" class="login-username" name="email" autocomplete="email" required placeholder="Email" aria-label="Email" maxlength="40" />
        <input type="password" class="login-password" name="password" autocomplete="new-password" required placeholder="Password" aria-label="Password" maxlength="24" />
        <input type="password" class="login-password" name="confirm_password" autocomplete="new-password" required placeholder="Confirm password" aria-label="Confirm password" maxlength="24" />
        <button type="submit" class="login-submit">Sign up</button>
    </form>

    <a href="login.php" class="login-forgot-pass">Already have an account? Log in</a>

    <div class="underlay-photo"></div>
    <div class="underlay-black"></div>

    </body>

    </html>
