<?php
session_start();

$email = '';
$errorMsg = '';

if (isset($_SESSION['user_id']) && isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    $redirect = $_GET['redirect'] ?? 'index.php';
    header('Location: ' . $redirect);
    exit;
}

$api = 'https://solace.ist.rit.edu/~it4527/Toni-s_Garage_Final/backend/api.php';
$apiKey = 'YOUR_SUPER_SECRET_KEY_HERE';




if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $errorMsg = 'Please enter both email and password.';
    } else {
        $ch = curl_init($api . '?action=login');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode([
                'email' => $email,
                'password' => $password
            ]),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json',
                'X-API-Key: ' . $apiKey
            ],
        ]);
        $resp = curl_exec($ch);

        if ($resp === false) {
            // cURL totally failed – let's see why
            $curlError = curl_error($ch);
            $curlInfo  = curl_getinfo($ch);

            echo '<pre>';
            echo "cURL ERROR:\n";
            var_dump($curlError);
            echo "\n\ncURL INFO:\n";
            var_dump($curlInfo);
            exit;
        }

        $data = json_decode($resp, true);


        if (
            $resp !== false &&
            is_array($data) &&
            isset($data['ok']) &&
            $data['ok'] === true &&
            isset($data['data']['user_id'])
        ) {
            $_SESSION['logged_in'] = true;
            $_SESSION['user_id']   = $data['data']['user_id'] ?? null;
            $_SESSION['user_email'] = $data['data']['email'] ?? $email;
            $_SESSION['user_name']  = trim(($data['data']['first_name'] ?? '') . ' ' . ($data['data']['last_name'] ?? ''));
            $_SESSION['role_id']    = $data['data']['role_id'] ?? null;

            $redirect = $_GET['redirect'] ?? 'index.php';
            header('Location: ' . $redirect);
            exit;
        } else {
            $errorMsg = (is_array($data) && isset($data['error']))
                ? $data['error']
                : 'Invalid email or password.';
                 $_SESSION['logged_in'] = false;
        }
    }
}
?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login</title>

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:100,300,400,700&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="loginstyles.css" />
</head>

<body>

    <form class="login-form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?><?php echo !empty($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : ''; ?>" method="post" novalidate>
        <p class="login-text" aria-hidden="true">
            <span class="fa-stack fa-lg" aria-hidden="true">
                <i class="fa fa-circle fa-stack-2x"></i>
                <i class="fa fa-lock fa-stack-1x"></i>
            </span>
        </p>

        <?php if (!empty($errorMsg)): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($errorMsg); ?>
            </div>
        <?php endif; ?>

        <input type="email" class="login-username" name="email" autocomplete="username" autofocus required
            placeholder="Email" aria-label="Email" maxlength="40"
            value="<?php echo htmlspecialchars($email); ?>" />

        <input type="password" class="login-password" name="password" autocomplete="current-password" required
            placeholder="Password" aria-label="Password" maxlength="24" />

        <button type="submit" class="login-submit">
            Login
        </button>
        <a class="signup-submit" href="signup.php">Sign up</a>
    </form>

    <a href="#" class="login-forgot-pass">forgot password?</a>

    <div class="underlay-photo"></div>
    <div class="underlay-black"></div>

</body>

</html>