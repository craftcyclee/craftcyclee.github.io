<?php
$DB_HOST = '127.0.0.1';
$DB_PORT = 3306; // default MySQL port
$DB_NAME = 'craftcycle'; // change to your database name
$DB_USER = 'root';
$DB_PASS = ''; // XAMPP default is empty for root

// --- PDO connection (recommended) ---
function getPDO()
{
    global $DB_HOST, $DB_PORT, $DB_NAME, $DB_USER, $DB_PASS;
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    $dsn = "mysql:host={$DB_HOST};port={$DB_PORT};dbname={$DB_NAME};charset=utf8mb4";
    $opts = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    try {
        $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $opts);
        return $pdo;
    } catch (PDOException $e) {
        // In production, avoid echoing details. Log instead.
        http_response_code(500);
        echo "Database connection failed: " . htmlspecialchars($e->getMessage());
        exit;
    }
}

function getMySQLi()
{
    global $DB_HOST, $DB_PORT, $DB_NAME, $DB_USER, $DB_PASS;
    static $mysqli = null;
    if ($mysqli !== null) {
        return $mysqli;
    }

    $mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, $DB_PORT);
    if ($mysqli->connect_errno) {
        http_response_code(500);
        echo "MySQLi connection failed: " . htmlspecialchars($mysqli->connect_error);
        exit;
    }
    $mysqli->set_charset('utf8mb4');
    return $mysqli;
}

function fetchProducts($limit = null, $search = null)
{
    $pdo = getPDO();
    // Build base SQL with optional search
    $base = 'SELECT id, name, price, img_data AS image, featured FROM product';
    $params = [];
    if ($search !== null && $search !== '') {
        $base .= ' WHERE name LIKE :search';
        $params[':search'] = '%' . $search . '%';
    }
    $base .= ' ORDER BY id DESC';

    if ($limit === null) {
        $stmt = $pdo->prepare($base);
    } else {
        $base .= ' LIMIT :lim';
        $stmt = $pdo->prepare($base);
        $stmt->bindValue(':lim', (int)$limit, PDO::PARAM_INT);
    }

    foreach ($params as $k => $v) {
        $stmt->bindValue($k, $v, PDO::PARAM_STR);
    }

    $stmt->execute();
    return $stmt->fetchAll();
}

function getProductById($id)
{
    $pdo = getPDO();
    // return img_data as image for template compatibility
    $stmt = $pdo->prepare('SELECT id, name, price, img_data AS image, featured FROM product WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $id]);
    return $stmt->fetch();
}

function createProduct(array $data)
{
    $pdo = getPDO();
    $stmt = $pdo->prepare('INSERT INTO product (name, price, img_data, featured) VALUES (:name, :price, :img, :featured)');
    $stmt->execute([
        ':name' => $data['name'] ?? '',
        ':price' => $data['price'] ?? 0,
        ':img' => $data['img_data'] ?? null,
        ':featured' => !empty($data['featured']) ? 1 : 0,
    ]);
    return $pdo->lastInsertId();
}

function updateProduct($id, array $data)
{
    $pdo = getPDO();
    $stmt = $pdo->prepare('UPDATE product SET name = :name, price = :price, img_data = :img, featured = :featured WHERE id = :id');
    return $stmt->execute([
        ':name' => $data['name'] ?? '',
        ':price' => $data['price'] ?? 0,
        ':img' => $data['img_data'] ?? null,
        ':featured' => !empty($data['featured']) ? 1 : 0,
        ':id' => $id,
    ]);
}

function deleteProduct($id)
{
    $pdo = getPDO();
    $stmt = $pdo->prepare('DELETE FROM product WHERE id = :id');
    return $stmt->execute([':id' => $id]);
}

// --- Tutorial helpers ---
/**
 * Fetch tutorials from the tutorial table.
 * Optional filters: $limit (int), $product_id (int) to fetch tutorials for a product, $search (string) for title/body search
 * Returns an array of associative arrays.
 */
function fetchTutorials($limit = null, $product_id = null, $search = null)
{
    $pdo = getPDO();
    $sql = 'SELECT id, product_id, title, body, youtube_id, created_at FROM tutorial';
    $conds = [];
    $params = [];

    if ($product_id !== null) {
        $conds[] = 'product_id = :product_id';
        $params[':product_id'] = (int)$product_id;
    }
    if ($search !== null && $search !== '') {
        $conds[] = '(title LIKE :s OR body LIKE :s)';
        $params[':s'] = '%' . $search . '%';
    }

    if (!empty($conds)) {
        $sql .= ' WHERE ' . implode(' AND ', $conds);
    }

    // order by newest id first (no created_at column required)
    $sql .= ' ORDER BY id DESC';
    if ($limit !== null) {
        $sql .= ' LIMIT :lim';
    }

    $stmt = $pdo->prepare($sql);
    foreach ($params as $k => $v) {
        $stmt->bindValue($k, $v, PDO::PARAM_STR);
    }
    if ($limit !== null) {
        $stmt->bindValue(':lim', (int)$limit, PDO::PARAM_INT);
    }
    $stmt->execute();
    return $stmt->fetchAll();
}

function getTutorial($id)
{
    $pdo = getPDO();
    $stmt = $pdo->prepare('SELECT id, product_id, title, body, youtube_id FROM tutorial WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $id]);
    return $stmt->fetch();
}

