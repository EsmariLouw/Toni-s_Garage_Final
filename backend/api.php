<?php
// ================================================
// api.php — Car Marketplace API (secured with API key)
// ================================================
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: https://' . $_SERVER['HTTP_HOST']); // same-origin
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-API-Key');



if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

/* ========= SECURITY: API KEY =========
   - All requests must include header:  X-API-Key: <your_key>
   - Change the value below to a strong random string.
   - Optional: store in a file outside webroot or env var.
*/
const API_KEY = 'YOUR_SUPER_SECRET_KEY_HERE';

function require_api_key(): void
{
    $headers = function_exists('getallheaders') ? getallheaders() : [];

    $key =
        $_SERVER['HTTP_X_API_KEY']
        ?? $headers['X-API-Key']
        ?? $headers['x-api-key']
        ?? ($_POST['api_key'] ?? null);

    if (!$key || !hash_equals(API_KEY, trim($key))) {
        http_response_code(401);
        echo json_encode([
            'ok' => false,
            'error' => 'Unauthorized',
            'debug_received_key' => $key // TEMP DEBUG
        ]);
        exit;
    }
}

$action = $_GET['action'] ?? 'vehicles';

if ($action !== 'payments') {
    require_api_key();
}

//“If my project is currently running on the Solace server, use the Solace database settings.”
// “Otherwise, run it locally — use localhost database settings.”
$host = $_SERVER['HTTP_HOST'] ?? '';

//“Does the current website address contain solace.ist.rit.edu anywhere inside it?”
if (strpos($host, 'solace.ist.rit.edu') !== false) {

    // === DATABASE CONFIGURATION FOR SERVER===
    $DB_HOST = 'localhost';
    $DB_NAME = 'it4527';
    $DB_USER = 'it4527';
    $DB_PASS = 'Optime9=saunter';

    // === DATABASE CONFIGURATION FOR LOCALHOST ===
} else {
    $DB_HOST = 'localhost';
    $DB_NAME = 'toni_garage';
    $DB_USER = 'root';       // or whatever you use locally
    $DB_PASS = "Ivaylo2001!";           // your local password
}

try {
    $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'DB connection failed' . $e->getMessage()]);
    exit;
}

// === HELPERS ===
function ok($data)
{
    echo json_encode(['ok' => true,  'data' => $data], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}
function fail($c, $m)
{
    http_response_code($c);
    echo json_encode(['ok' => false, 'error' => $m]);
    exit;
}

$action = $_GET['action'] ?? 'vehicles';

// === ENDPOINTS (same as before; trimmed for brevity) ===
if ($action === 'vehicle') {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if ($id <= 0) fail(400, 'Missing or invalid ID');
    $sql = "SELECT
      v.vehicle_id, v.name, v.model, v.doc, v.price, v.owned_before, v.description,
      v.number_sold, v.color_1, v.color_2, v.color_3, v.color_4,
      vi.image_url AS vehicle_image_url,
      t.type_id, t.name AS type_name,
      u.user_id, CONCAT(u.first_name,' ',u.last_name) AS seller_name, u.email AS seller_email,
      f.feature_id, f.fuel, f.mileage,
      e.engine_id, e.name AS engine_name, e.type AS engine_type, e.horse_power,
      tr.transmission_id, tr.name AS transmission_name, tr.type AS transmission_type,
      ii.image_url AS interior_image_url
    FROM vehicle v
    LEFT JOIN vehicle_images vi ON vi.image_id = v.image_id
    LEFT JOIN types t           ON t.type_id = v.type_id
    LEFT JOIN users u           ON u.user_id = v.user_id
    LEFT JOIN features f        ON f.feature_id = v.features_id
    LEFT JOIN engine e          ON e.engine_id = f.engine_id
    LEFT JOIN transmission tr   ON tr.transmission_id = f.transmission_id
    LEFT JOIN interior i        ON i.interior_id = f.interior_id
    LEFT JOIN interior_images ii ON ii.image_id = i.image_id
    WHERE v.vehicle_id = :id
    LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch();
    if (!$row) fail(404, 'Vehicle not found');
    ok($row);
} elseif ($action === 'vehicles') {
    $sql = "SELECT v.vehicle_id, v.name, v.model, v.price, v.doc, v.owned_before,
                 t.name AS type_name, vi.image_url AS vehicle_image_url, f.mileage, f.fuel
          FROM vehicle v
          LEFT JOIN types t ON t.type_id=v.type_id
          LEFT JOIN vehicle_images vi ON vi.image_id=v.image_id
          LEFT JOIN features f ON f.feature_id=v.features_id
          ORDER BY v.vehicle_id ASC";
    ok($pdo->query($sql)->fetchAll());
} elseif ($action === 'types') {
    $sql = "SELECT t.type_id, t.name AS type_name, COUNT(v.vehicle_id) AS vehicles_available
          FROM types t LEFT JOIN vehicle v ON v.type_id=t.type_id
          GROUP BY t.type_id, t.name ORDER BY t.name";
    ok($pdo->query($sql)->fetchAll());
} elseif ($action === 'featured') {

    $sql = "SELECT v.vehicle_id AS id, v.name, v.price, v.description, vi.image_url, v.number_sold,
           JSON_OBJECT(
           'ac', i.ac, 
           'model', v.model,
           'transmission', t.type,
           'smart_screen', i.smart_screen
           ) AS features
           FROM vehicle v
              LEFT JOIN vehicle_images vi ON vi.image_id = v.image_id
                LEFT JOIN features f ON v.features_id = f.feature_id
                LEFT JOIN interior i ON f.interior_id = i.interior_id
                LEFT JOIN transmission t ON f.transmission_id = t.transmission_id
                WHERE v.is_featured = 1";
    ok($pdo->query($sql)->fetchAll());
} elseif ($action === 'payments') {

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        fail(405, 'POST method required');
    }

    try {

        $firstName  = $_POST['credit_holder_fname'] ?? null;
        $lastName   = $_POST['credit_holder_lname'] ?? null;
        $cardNumber = $_POST['last_four'] ?? null;

        if (!$firstName || !$lastName || !$cardNumber) {
            throw new Exception('Missing payment fields');
        }

        $firstName  = trim($firstName);
        $lastName   = trim($lastName);
        $cardNumber = preg_replace('/\D+/', '', $cardNumber); // only digits

        $lastFourDigits = substr($cardNumber, -4);

        $sql = "INSERT INTO payments 
            (credit_holder_fname, credit_holder_lname, last_four, transaction_time)
            VALUES (:credit_holder_fname, :credit_holder_lname, :last_four, NOW())";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':credit_holder_fname' => $firstName,
            ':credit_holder_lname' => $lastName,
            ':last_four'           => $lastFourDigits,
        ]);

        echo json_encode([
            'ok' => true,
            'message' => 'Payment stored successfully'
        ]);
        exit;
    } catch (Throwable $e) {
        http_response_code(500);
        echo json_encode([
            'ok' => false,
            'error' => 'Payment insert failed',
            'debug' => $e->getMessage()
        ]);
        exit;
    }
} else if ($action === 'login') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        fail(405, 'POST method required');
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $email = trim($input['email'] ?? '');
    $password = $input['password'] ?? '';

    if ($email === '' || $password === '') {
        fail(400, 'Email and password are required.');
    }

    $sql = "SELECT user_id, role_id, first_name, last_name, password, email,
                   phone_number, address, state, zip_code, country
            FROM users
            WHERE email = :email
              AND password = :password
            LIMIT 1";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':email'    => $email,
        ':password' => $password,   // plain text compare
    ]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        fail(401, 'Invalid email or password.');
    }

    // Don’t send password back (we didn’t select it anyway)
    ok($user);
} else {
    fail(400, 'Unknown action. Try action=vehicle&id=1, action=vehicles, or action=types');
}
