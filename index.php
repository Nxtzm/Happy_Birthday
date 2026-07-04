<?php
/**
 * DeliveryPlatform - Clean Architecture Monolith
 * A premium multi-vendor delivery platform.
 */

namespace App\Core;

// --- CONFIG & ERRORS ---
error_reporting(E_ALL);
ini_set('display_errors', 1);
if (session_status() === PHP_SESSION_NONE) session_start();

class Database {
    private static $instance = null;
    private $db;

    private function __construct() {
        $dbPath = __DIR__ . '/database.sqlite';
        $isNew = !file_exists($dbPath) || filesize($dbPath) === 0;
        $this->db = new \SQLite3($dbPath);
        $this->db->enableExceptions(true);
        if ($isNew) $this->initSchema();
    }

    private function initSchema() {
        $sql = "
        CREATE TABLE IF NOT EXISTS categories (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT NOT NULL, type TEXT NOT NULL, icon TEXT, status INTEGER DEFAULT 1);
        CREATE TABLE IF NOT EXISTS users (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT NOT NULL, phone TEXT UNIQUE NOT NULL, email TEXT UNIQUE, password TEXT, role TEXT NOT NULL, status TEXT DEFAULT 'active', avatar TEXT, created_at DATETIME DEFAULT CURRENT_TIMESTAMP);
        CREATE TABLE IF NOT EXISTS stores (id INTEGER PRIMARY KEY AUTOINCREMENT, vendor_id INTEGER NOT NULL, category_id INTEGER NOT NULL, name TEXT NOT NULL, description TEXT, address TEXT, lat REAL, lng REAL, logo TEXT, cover TEXT, rating REAL DEFAULT 0, review_count INTEGER DEFAULT 0, status TEXT DEFAULT 'open', opening_hours TEXT, delivery_time TEXT, min_order REAL DEFAULT 0, commission_rate REAL DEFAULT 10.0, FOREIGN KEY(vendor_id) REFERENCES users(id), FOREIGN KEY(category_id) REFERENCES categories(id));
        CREATE TABLE IF NOT EXISTS products (id INTEGER PRIMARY KEY AUTOINCREMENT, store_id INTEGER NOT NULL, category_id INTEGER, name TEXT NOT NULL, description TEXT, price REAL NOT NULL, discount_price REAL, stock INTEGER DEFAULT -1, image TEXT, variants TEXT, status INTEGER DEFAULT 1, created_at DATETIME DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY(store_id) REFERENCES stores(id));
        CREATE TABLE IF NOT EXISTS orders (id INTEGER PRIMARY KEY AUTOINCREMENT, customer_id INTEGER NOT NULL, store_id INTEGER NOT NULL, rider_id INTEGER, total_amount REAL NOT NULL, subtotal REAL NOT NULL, delivery_fee REAL DEFAULT 0, status TEXT DEFAULT 'pending', payment_method TEXT NOT NULL, payment_status TEXT DEFAULT 'pending', delivery_address TEXT, otp TEXT, created_at DATETIME DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP);
        CREATE TABLE IF NOT EXISTS order_items (id INTEGER PRIMARY KEY AUTOINCREMENT, order_id INTEGER NOT NULL, product_id INTEGER NOT NULL, quantity INTEGER NOT NULL, price REAL NOT NULL);
        CREATE TABLE IF NOT EXISTS riders (id INTEGER PRIMARY KEY AUTOINCREMENT, user_id INTEGER NOT NULL, status TEXT DEFAULT 'offline', wallet_balance REAL DEFAULT 0);
        ";
        $this->db->exec($sql);
        // Seed categories if empty
        $count = $this->db->querySingle("SELECT COUNT(*) FROM categories");
        if ($count == 0) {
            $this->db->exec("INSERT INTO categories (name, type, icon) VALUES ('Food', 'food', 'restaurant'), ('Grocery', 'grocery', 'shopping_basket'), ('Pharmacy', 'pharmacy', 'medical_services'), ('Meat', 'meat', 'kebab_dining'), ('Stationery', 'stationery', 'edit')");
        }
    }

    public static function getInstance() {
        if (self::$instance === null) self::$instance = new self();
        return self::$instance->db;
    }
}

// --- DOMAIN LAYER (Entities & Repository Interfaces) ---

namespace App\Domain\Entity;

class User {
    public $id, $name, $phone, $role, $status;
    public function __construct($data) {
        $this->id = $data['id'] ?? null;
        $this->name = $data['name'] ?? '';
        $this->phone = $data['phone'] ?? '';
        $this->role = $data['role'] ?? 'customer';
        $this->status = $data['status'] ?? 'active';
    }
}

namespace App\Domain\Repository;

interface UserRepositoryInterface {
    public function findByPhone(string $phone);
    public function findById(int $id);
    public function save(array $data);
}

interface OrderRepositoryInterface {
    public function findById(int $id);
    public function findByCustomer(int $userId);
    public function findByStore(int $storeId);
    public function findAvailableForRider();
    public function save(array $data);
    public function updateStatus(int $id, string $status, ?int $riderId = null);
}

interface StoreRepositoryInterface {
    public function findAll();
    public function findById(int $id);
    public function findByVendor(int $vendorId);
    public function search(string $query);
}

// --- INFRASTRUCTURE LAYER (Persistence) ---

namespace App\Infrastructure\Persistence;

use App\Core\Database;
use App\Domain\Repository\UserRepositoryInterface;
use App\Domain\Repository\OrderRepositoryInterface;
use App\Domain\Repository\StoreRepositoryInterface;

class SQLiteUserRepository implements UserRepositoryInterface {
    public function findByPhone(string $phone) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM users WHERE phone = :p");
        $stmt->bindValue(':p', $phone);
        $res = $stmt->execute()->fetchArray(SQLITE3_ASSOC);
        return $res ?: null;
    }
    public function findById(int $id) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->bindValue(':id', $id);
        return $stmt->execute()->fetchArray(SQLITE3_ASSOC);
    }
    public function save(array $data) {
        $db = Database::getInstance();
        $stmt = $db->prepare("INSERT INTO users (name, phone, password, role) VALUES (:n, :p, :pw, :r)");
        $stmt->bindValue(':n', $data['name']);
        $stmt->bindValue(':p', $data['phone']);
        $stmt->bindValue(':pw', password_hash($data['password'], PASSWORD_DEFAULT));
        $stmt->bindValue(':r', $data['role'] ?? 'customer');
        $stmt->execute();
        return $db->lastInsertRowID();
    }
}

class SQLiteOrderRepository implements OrderRepositoryInterface {
    public function findById(int $id) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT o.*, s.name as store_name FROM orders o JOIN stores s ON o.store_id = s.id WHERE o.id = :id");
        $stmt->bindValue(':id', $id);
        $order = $stmt->execute()->fetchArray(SQLITE3_ASSOC);
        if ($order) {
            $stmt = $db->prepare("SELECT oi.*, p.name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = :oid");
            $stmt->bindValue(':oid', $id);
            $res = $stmt->execute();
            $items = [];
            while($row = $res->fetchArray(SQLITE3_ASSOC)) $items[] = $row;
            $order['items'] = $items;
        }
        return $order;
    }
    public function findByCustomer(int $userId) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT o.*, s.name as store_name FROM orders o JOIN stores s ON o.store_id = s.id WHERE o.customer_id = :uid ORDER BY o.created_at DESC");
        $stmt->bindValue(':uid', $userId);
        $res = $stmt->execute();
        $orders = [];
        while($row = $res->fetchArray(SQLITE3_ASSOC)) $orders[] = $row;
        return $orders;
    }
    public function findByStore(int $storeId) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM orders WHERE store_id = :sid ORDER BY created_at DESC");
        $stmt->bindValue(':sid', $storeId);
        $res = $stmt->execute();
        $orders = [];
        while($row = $res->fetchArray(SQLITE3_ASSOC)) $orders[] = $row;
        return $orders;
    }
    public function findAvailableForRider() {
        $db = Database::getInstance();
        $res = $db->query("SELECT o.*, s.name as store_name, s.address as store_address FROM orders o JOIN stores s ON o.store_id = s.id WHERE o.status = 'ready' OR (o.status = 'accepted' AND o.rider_id IS NULL)");
        $orders = [];
        while($row = $res->fetchArray(SQLITE3_ASSOC)) $orders[] = $row;
        return $orders;
    }
    public function save(array $data) {
        $db = Database::getInstance();
        $db->exec("BEGIN TRANSACTION");
        try {
            $otp = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
            $stmt = $db->prepare("INSERT INTO orders (customer_id, store_id, total_amount, subtotal, delivery_fee, payment_method, delivery_address, otp) VALUES (:uid, :sid, :t, :s, :df, :pm, :addr, :otp)");
            $stmt->bindValue(':uid', $data['customer_id']);
            $stmt->bindValue(':sid', $data['store_id']);
            $stmt->bindValue(':t', $data['total']);
            $stmt->bindValue(':s', $data['subtotal']);
            $stmt->bindValue(':df', $data['delivery_fee']);
            $stmt->bindValue(':pm', $data['payment_method']);
            $stmt->bindValue(':addr', $data['delivery_address']);
            $stmt->bindValue(':otp', $otp);
            $stmt->execute();
            $orderId = $db->lastInsertRowID();
            foreach ($data['items'] as $item) {
                $stmt = $db->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (:oid, :pid, :q, :p)");
                $stmt->bindValue(':oid', $orderId);
                $stmt->bindValue(':pid', $item['product_id']);
                $stmt->bindValue(':q', $item['quantity']);
                $stmt->bindValue(':p', $item['price']);
                $stmt->execute();
            }
            $db->exec("COMMIT");
            return ['id' => $orderId, 'otp' => $otp];
        } catch (\Exception $e) {
            $db->exec("ROLLBACK");
            throw $e;
        }
    }
    public function updateStatus(int $id, string $status, ?int $riderId = null) {
        $db = Database::getInstance();
        $sql = "UPDATE orders SET status = :s, updated_at = CURRENT_TIMESTAMP";
        if ($riderId) $sql .= ", rider_id = :rid";
        $sql .= " WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':s', $status);
        $stmt->bindValue(':id', $id);
        if ($riderId) $stmt->bindValue(':rid', $riderId);
        $stmt->execute();
    }
}

class SQLiteStoreRepository implements StoreRepositoryInterface {
    public function findAll() {
        $db = Database::getInstance();
        $res = $db->query("SELECT * FROM stores");
        $stores = [];
        while($row = $res->fetchArray(SQLITE3_ASSOC)) $stores[] = $row;
        return $stores;
    }
    public function findById(int $id) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM stores WHERE id = :id");
        $stmt->bindValue(':id', $id);
        $store = $stmt->execute()->fetchArray(SQLITE3_ASSOC);
        if ($store) {
            $stmt = $db->prepare("SELECT * FROM products WHERE store_id = :sid AND status = 1");
            $stmt->bindValue(':sid', $id);
            $res = $stmt->execute();
            $products = [];
            while($row = $res->fetchArray(SQLITE3_ASSOC)) $products[] = $row;
            $store['products'] = $products;
        }
        return $store;
    }
    public function findByVendor(int $vendorId) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM stores WHERE vendor_id = :vid");
        $stmt->bindValue(':vid', $vendorId);
        return $stmt->execute()->fetchArray(SQLITE3_ASSOC);
    }
    public function search(string $query) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM stores WHERE name LIKE :q");
        $stmt->bindValue(':q', "%$query%");
        $res = $stmt->execute();
        $stores = [];
        while($row = $res->fetchArray(SQLITE3_ASSOC)) $stores[] = $row;
        return $stores;
    }
}

// --- APPLICATION LAYER (Services) ---

namespace App\Application\Service;

use App\Domain\Repository\UserRepositoryInterface;
use App\Domain\Repository\OrderRepositoryInterface;

interface PaymentGatewayInterface {
    public function process(float $amount, array $meta): array;
}

class CashOnDeliveryProvider implements PaymentGatewayInterface {
    public function process(float $amount, array $meta): array {
        return ['status' => 'pending', 'transaction_id' => 'COD-' . uniqid()];
    }
}

class EsewaProvider implements PaymentGatewayInterface {
    public function process(float $amount, array $meta): array {
        return ['status' => 'initiated', 'redirect_url' => 'https://esewa.com.np/pay?amt=' . $amount];
    }
}

class PaymentService {
    private $gateways = [];
    public function __construct() {
        $this->gateways['cod'] = new CashOnDeliveryProvider();
        $this->gateways['esewa'] = new EsewaProvider();
    }
    public function initiate(string $method, float $amount, array $meta) {
        if (!isset($this->gateways[$method])) throw new \Exception("Payment method not supported");
        return $this->gateways[$method]->process($amount, $meta);
    }
}

class AuthService {
    private $userRepo;
    public function __construct(UserRepositoryInterface $userRepo) {
        $this->userRepo = $userRepo;
    }
    public function login(string $phone, string $password) {
        $user = $this->userRepo->findByPhone($phone);
        if ($user && !empty($user['password']) && password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user;
            unset($_SESSION['user']['password']);
            return $_SESSION['user'];
        }
        return null;
    }
    public function register(array $data) {
        return $this->userRepo->save($data);
    }
    public function getCurrentUser() {
        return $_SESSION['user'] ?? null;
    }
    public function checkRole(array $allowedRoles) {
        $user = $this->getCurrentUser();
        return $user && in_array($user['role'], $allowedRoles);
    }
}

// --- INTERFACES LAYER (API Handlers / Controllers) ---

namespace App\Interfaces\Api;

use App\Application\Service\AuthService;
use App\Application\Service\PaymentService;
use App\Domain\Repository\StoreRepositoryInterface;
use App\Domain\Repository\OrderRepositoryInterface;

class ApiHandler {
    protected $authService;
    protected $paymentService;
    public function __construct(AuthService $authService, ?PaymentService $paymentService = null) {
        $this->authService = $authService;
        $this->paymentService = $paymentService;
    }
    protected function json($data) { header('Content-Type: application/json'); echo json_encode(['success' => true, 'data' => $data]); exit; }
    protected function error($msg, $code = 400) { header('Content-Type: application/json'); http_response_code($code); echo json_encode(['success' => false, 'error' => $msg]); exit; }
    protected function requireRole(array $roles) { if (!$this->authService->checkRole($roles)) $this->error("Access Denied", 403); }
    protected function requireAuth() { if (!$this->authService->getCurrentUser()) $this->error("Unauthorized", 401); }
}

class CustomerHandler extends ApiHandler {
    private $storeRepo;
    private $orderRepo;
    public function __construct(AuthService $auth, StoreRepositoryInterface $storeRepo, OrderRepositoryInterface $orderRepo, PaymentService $paymentService) {
        parent::__construct($auth, $paymentService);
        $this->storeRepo = $storeRepo;
        $this->orderRepo = $orderRepo;
    }
    public function get_stores() { $this->json($this->storeRepo->findAll()); }
    public function get_store() { $this->json($this->storeRepo->findById((int)$_GET['id'])); }
    public function search() {
        $q = $_GET['q'] ?? '';
        $stores = $this->storeRepo->search($q);
        // Also search products
        $db = \App\Core\Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM products WHERE name LIKE :q");
        $stmt->bindValue(':q', "%$q%");
        $res = $stmt->execute();
        $products = [];
        while($row = $res->fetchArray(SQLITE3_ASSOC)) $products[] = $row;
        $this->json(['stores' => $stores, 'products' => $products]);
    }
    public function place_order() {
        $this->requireAuth();
        $user = $this->authService->getCurrentUser();
        $data = json_decode(file_get_contents('php://input'), true);
        $data['customer_id'] = $user['id'];

        $payment = $this->paymentService->initiate($data['payment_method'], $data['total'], ['user' => $user]);
        $order = $this->orderRepo->save($data);

        $this->json(['order' => $order, 'payment' => $payment]);
    }
    public function get_orders() {
        $this->requireAuth();
        $user = $this->authService->getCurrentUser();
        $this->json($this->orderRepo->findByCustomer($user['id']));
    }
    public function get_order() {
        $this->requireAuth();
        $this->json($this->orderRepo->findById((int)$_GET['id']));
    }
}

class MerchantHandler extends ApiHandler {
    private $storeRepo;
    private $orderRepo;
    public function __construct(AuthService $auth, StoreRepositoryInterface $storeRepo, OrderRepositoryInterface $orderRepo) {
        parent::__construct($auth);
        $this->storeRepo = $storeRepo;
        $this->orderRepo = $orderRepo;
    }
    public function get_dashboard() {
        $this->requireRole(['merchant', 'admin']);
        $user = $this->authService->getCurrentUser();
        $store = $this->storeRepo->findByVendor($user['id']);
        if (!$store) $this->error("Store not found for merchant");
        $orders = $this->orderRepo->findByStore($store['id']);

        $db = \App\Core\Database::getInstance();
        $stats = [
            'today_earnings' => $db->querySingle("SELECT SUM(total_amount) FROM orders WHERE store_id = {$store['id']} AND DATE(created_at) = DATE('now')") ?? 0,
            'pending_count' => $db->querySingle("SELECT COUNT(*) FROM orders WHERE store_id = {$store['id']} AND status = 'pending'"),
            'preparing_count' => $db->querySingle("SELECT COUNT(*) FROM orders WHERE store_id = {$store['id']} AND status = 'preparing'")
        ];

        $this->json(['store' => $store, 'orders' => $orders, 'stats' => $stats]);
    }
    public function update_status() {
        $this->requireRole(['merchant', 'admin']);
        $data = json_decode(file_get_contents('php://input'), true);
        $this->orderRepo->updateStatus($data['order_id'], $data['status']);
        $this->json(['message' => 'Updated']);
    }
    public function get_products() {
        $this->requireRole(['merchant', 'admin']);
        $user = $this->authService->getCurrentUser();
        $store = $this->storeRepo->findByVendor($user['id']);
        $db = \App\Core\Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM products WHERE store_id = :sid");
        $stmt->bindValue(':sid', $store['id']);
        $res = $stmt->execute();
        $products = [];
        while($row = $res->fetchArray(SQLITE3_ASSOC)) $products[] = $row;
        $this->json($products);
    }
    public function save_product() {
        $this->requireRole(['merchant', 'admin']);
        $user = $this->authService->getCurrentUser();
        $store = $this->storeRepo->findByVendor($user['id']);
        $data = json_decode(file_get_contents('php://input'), true);
        $db = \App\Core\Database::getInstance();

        if (isset($data['id'])) {
            $stmt = $db->prepare("UPDATE products SET name = :n, price = :p, description = :d, variants = :v WHERE id = :id");
            $stmt->bindValue(':id', $data['id']);
        } else {
            $stmt = $db->prepare("INSERT INTO products (store_id, name, price, description, variants) VALUES (:sid, :n, :p, :d, :v)");
            $stmt->bindValue(':sid', $store['id']);
        }
        $stmt->bindValue(':n', $data['name']);
        $stmt->bindValue(':p', $data['price']);
        $stmt->bindValue(':d', $data['description'] ?? '');
        $stmt->bindValue(':v', json_encode($data['category_data'] ?? []));
        $stmt->execute();
        $this->json(['success' => true]);
    }
}

class RiderHandler extends ApiHandler {
    private $orderRepo;
    public function __construct(AuthService $auth, OrderRepositoryInterface $orderRepo) {
        parent::__construct($auth);
        $this->orderRepo = $orderRepo;
    }
    public function get_available() {
        $this->requireRole(['rider', 'admin']);
        $this->json($this->orderRepo->findAvailableForRider());
    }
    public function accept() {
        $this->requireRole(['rider', 'admin']);
        $user = $this->authService->getCurrentUser();
        $data = json_decode(file_get_contents('php://input'), true);
        $this->orderRepo->updateStatus($data['order_id'], 'picked_up', $user['id']);
        $this->json(['message' => 'Accepted']);
    }
    public function complete() {
        $this->requireRole(['rider', 'admin']);
        $data = json_decode(file_get_contents('php://input'), true);
        $order = $this->orderRepo->findById($data['order_id']);
        if ($order['otp'] !== $data['otp']) $this->error("Invalid OTP");
        $this->orderRepo->updateStatus($data['order_id'], 'delivered');
        $this->json(['message' => 'Delivered']);
    }
}

// --- BOOTSTRAP ---

namespace App;

use App\Infrastructure\Persistence\SQLiteUserRepository;
use App\Infrastructure\Persistence\SQLiteOrderRepository;
use App\Infrastructure\Persistence\SQLiteStoreRepository;
use App\Application\Service\AuthService;
use App\Application\Service\PaymentService;
use App\Interfaces\Api\CustomerHandler;
use App\Interfaces\Api\MerchantHandler;
use App\Interfaces\Api\RiderHandler;

$userRepo = new SQLiteUserRepository();
$orderRepo = new SQLiteOrderRepository();
$storeRepo = new SQLiteStoreRepository();
$authService = new AuthService($userRepo);
$paymentService = new PaymentService();

$api = $_GET['api'] ?? null;
if ($api) {
    $action = $_GET['action'] ?? 'index';
    $handlers = [
        'customer' => new CustomerHandler($authService, $storeRepo, $orderRepo, $paymentService),
        'merchant' => new MerchantHandler($authService, $storeRepo, $orderRepo),
        'rider' => new RiderHandler($authService, $orderRepo),
        'auth' => new class($authService) extends \App\Interfaces\Api\ApiHandler {
            public function login() { $data = json_decode(file_get_contents('php://input'), true); if($u = $this->authService->login($data['phone'], $data['password'])) $this->json(['user' => $u]); $this->error("Invalid credentials"); }
            public function register() { $data = json_decode(file_get_contents('php://input'), true); $this->json(['id' => $this->authService->register($data)]); }
            public function logout() { session_destroy(); $this->json(['message' => 'Logged out']); }
        }
    ];
    if (isset($handlers[$api]) && method_exists($handlers[$api], $action)) {
        $handlers[$api]->$action();
    } else {
        header('Content-Type: application/json'); http_response_code(404); echo json_encode(['error' => 'Not found']);
    }
    exit;
}

$user = $authService->getCurrentUser();
include 'layout.php';
