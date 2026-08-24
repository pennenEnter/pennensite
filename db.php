<?php
/**
 * PENNEN Footwear — Canonical Database Access Layer & Automatic Public Sync
 */
require_once __DIR__ . '/config.php';

class PennenDB {
    private static ?PennenDB $instance = null;
    private ?PDO $pdo = null;
    private string $jsonStorageFile;
    private bool $useJsonFallback = false;

    private function __construct() {
        $this->jsonStorageFile = __DIR__ . '/data/database.json';
        $this->initConnection();
    }

    public static function getInstance(): PennenDB {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function initConnection(): void {
        // Try PDO MySQL connection
        if (extension_loaded('pdo_mysql')) {
            try {
                $dsn = sprintf('mysql:host=%s;port=%s;charset=utf8mb4', DB_HOST, DB_PORT);
                $initPdo = new PDO($dsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_TIMEOUT => 2
                ]);
                $initPdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                
                $dbDsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', DB_HOST, DB_PORT, DB_NAME);
                $this->pdo = new PDO($dbDsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]);

                $this->initMySQLTables();
                return;
            } catch (Exception $e) {
                // Fall back to JSON storage if MySQL is unreachable
                $this->useJsonFallback = true;
            }
        } else {
            $this->useJsonFallback = true;
        }

        // Initialize JSON datastore if MySQL is offline
        $this->initJsonStore();
    }

    private function initMySQLTables(): void {
        if (!$this->pdo) return;

        // Admins table
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS admins (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) UNIQUE NOT NULL,
                password_hash VARCHAR(255) NOT NULL,
                name VARCHAR(100) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // Canonical Products table
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS products (
                id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                sku VARCHAR(100) NULL,
                gender ENUM('male','female','unisex') DEFAULT 'unisex',
                silhouette ENUM('sneaker','high-top','slip-on','slide','mule-clog','flip-flop','ballet-flat','wedge','loafer') NULL,
                slug VARCHAR(255) NULL UNIQUE,
                category VARCHAR(100) NOT NULL,
                description TEXT NULL,
                colorways TEXT NULL,
                price DECIMAL(10,2) DEFAULT 0.00,
                mrp DECIMAL(10,2) NULL,
                discount DECIMAL(5,2) NULL,
                sticker_ribbon ENUM('none','new_arrival','bestseller','hot') DEFAULT 'none',
                publication_status ENUM('published','draft','archived') DEFAULT 'draft',
                image VARCHAR(500) NULL,
                hover_image VARCHAR(500) NULL,
                gallery_image_1 VARCHAR(500) NULL,
                gallery_image_2 VARCHAR(500) NULL,
                amazon_url VARCHAR(1000) NULL,
                flipkart_url VARCHAR(1000) NULL,
                meesho_url VARCHAR(1000) NULL,
                myntra_url VARCHAR(1000) NULL,
                ajio_url VARCHAR(1000) NULL,
                snapdeal_url VARCHAR(1000) NULL,
                jiomart_url VARCHAR(1000) NULL,
                status ENUM('active','inactive') DEFAULT 'active',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // Seed default admin if empty
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM admins");
        if ($stmt->fetchColumn() == 0) {
            $passHash = password_hash(DEFAULT_ADMIN_PASS, PASSWORD_DEFAULT);
            $ins = $this->pdo->prepare("INSERT INTO admins (username, password_hash, name) VALUES (?, ?, ?)");
            $ins->execute([DEFAULT_ADMIN_USER, $passHash, DEFAULT_ADMIN_NAME]);
        }

        // Seed existing JSON products if table is empty
        $pCount = $this->pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
        if ($pCount == 0) {
            $this->seedFromExistingJson();
        }
    }

    private function initJsonStore(): void {
        if (!file_exists($this->jsonStorageFile)) {
            $initialData = [
                'admins' => [
                    [
                        'id' => 1,
                        'username' => DEFAULT_ADMIN_USER,
                        'password_hash' => password_hash(DEFAULT_ADMIN_PASS, PASSWORD_DEFAULT),
                        'name' => DEFAULT_ADMIN_NAME,
                        'created_at' => date('Y-m-d H:i:s')
                    ]
                ],
                'products' => []
            ];
            file_put_contents($this->jsonStorageFile, json_encode($initialData, JSON_PRETTY_PRINT));
            $this->seedFromExistingJson();
        }
    }

    private function getJsonData(): array {
        if (!file_exists($this->jsonStorageFile)) {
            $this->initJsonStore();
        }
        $raw = file_get_contents($this->jsonStorageFile);
        $data = json_decode($raw, true);
        return is_array($data) ? $data : ['admins' => [], 'products' => []];
    }

    private function saveJsonData(array $data): void {
        file_put_contents($this->jsonStorageFile, json_encode($data, JSON_PRETTY_PRINT));
    }

    /**
     * Maps legacy silhouette / shape strings to canonical silhouette ENUM.
     */
    public static function normalizeSilhouette(?string $val): ?string {
        if (empty($val)) return 'sneaker';
        $v = strtolower(trim($val));
        $map = [
            'sneaker' => 'sneaker',
            'hightop' => 'high-top',
            'high-top' => 'high-top',
            'slipon' => 'slip-on',
            'slip-on' => 'slip-on',
            'slide' => 'slide',
            'mule' => 'mule-clog',
            'mule-clog' => 'mule-clog',
            'clog' => 'mule-clog',
            'flipflop' => 'flip-flop',
            'flip-flop' => 'flip-flop',
            'flat' => 'ballet-flat',
            'ballet-flat' => 'ballet-flat',
            'wedge' => 'wedge',
            'loafer' => 'loafer'
        ];
        return $map[$v] ?? 'sneaker';
    }

    /**
     * Maps legacy sticker string to canonical sticker_ribbon ENUM.
     */
    public static function normalizeStickerRibbon(?string $val): string {
        if (empty($val)) return 'none';
        $v = strtolower(trim($val));
        if ($v === 'new' || $v === 'new_arrival') return 'new_arrival';
        if ($v === 'best' || $v === 'bestseller') return 'bestseller';
        if ($v === 'hot') return 'hot';
        return 'none';
    }

    /**
     * Maps gender string to canonical gender ENUM.
     */
    public static function normalizeGender(?string $val, string $category = ''): string {
        $v = strtolower(trim($val ?? ''));
        if ($v === 'men' || $v === 'male') return 'male';
        if ($v === 'women' || $v === 'female') return 'female';
        if ($v === 'unisex') return 'unisex';

        if (str_contains($category, 'women')) return 'female';
        if (str_contains($category, 'men')) return 'male';
        return 'unisex';
    }

    private function seedFromExistingJson(): void {
        $categoryFiles = [
            'men-shoes' => 'men-shoes.json',
            'men-slippers' => 'men-slippers.json',
            'women-shoes' => 'women-shoes.json',
            'women-slippers' => 'women-slippers.json'
        ];

        foreach ($categoryFiles as $cat => $file) {
            $path = __DIR__ . '/data/' . $file;
            if (file_exists($path)) {
                $raw = json_decode(file_get_contents($path), true);
                $items = isset($raw['products']) ? $raw['products'] : $raw;
                if (is_array($items)) {
                    foreach ($items as $item) {
                        $sku = (string)($item['id'] ?? uniqid('PNN-'));
                        $name = $item['name'] ?? 'PENNEN Footwear';
                        $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $name)) . '-' . strtolower($sku);

                        // Process colorways
                        $colorwaysStr = '';
                        if (!empty($item['colors'])) {
                            if (is_array($item['colors'])) {
                                $colorwaysStr = implode(', ', $item['colors']);
                            } else {
                                $colorwaysStr = (string)$item['colors'];
                            }
                        }

                        $gallery1 = null;
                        $gallery2 = null;
                        if (!empty($item['gallery_images']) && is_array($item['gallery_images'])) {
                            $gallery1 = $item['gallery_images'][0] ?? null;
                            $gallery2 = $item['gallery_images'][1] ?? null;
                        }

                        $productRecord = [
                            'name' => $name,
                            'sku' => $sku,
                            'slug' => $slug,
                            'category' => $cat,
                            'gender' => self::normalizeGender($item['gender'] ?? null, $cat),
                            'silhouette' => self::normalizeSilhouette($item['silhouette'] ?? $item['shape'] ?? null),
                            'description' => $item['description'] ?? 'PENNEN technical footwear.',
                            'colorways' => $colorwaysStr,
                            'price' => floatval($item['price'] ?? 1499),
                            'mrp' => !empty($item['mrp']) ? floatval($item['mrp']) : null,
                            'discount' => !empty($item['discount']) ? floatval($item['discount']) : null,
                            'sticker_ribbon' => self::normalizeStickerRibbon($item['sticker'] ?? $item['sticker_ribbon'] ?? null),
                            'publication_status' => 'published',
                            'image' => $item['image'] ?? 'hero-shoe.png',
                            'hover_image' => $item['hoverImage'] ?? $item['hover_image'] ?? null,
                            'gallery_image_1' => $gallery1,
                            'gallery_image_2' => $gallery2,
                            'amazon_url' => $item['amazon_url'] ?? ('https://www.amazon.in/s?k=' . urlencode($item['q'] ?? $name)),
                            'flipkart_url' => $item['flipkart_url'] ?? ('https://www.flipkart.com/search?q=' . urlencode($item['q'] ?? $name)),
                            'meesho_url' => $item['meesho_url'] ?? ('https://www.meesho.com/search?q=' . urlencode($item['q'] ?? $name)),
                            'myntra_url' => $item['myntra_url'] ?? ('https://www.myntra.com/' . urlencode($item['q'] ?? $name)),
                            'ajio_url' => $item['ajio_url'] ?? ('https://www.ajio.com/search/?text=' . urlencode($item['q'] ?? $name)),
                            'snapdeal_url' => $item['snapdeal_url'] ?? ('https://www.snapdeal.com/search?keyword=' . urlencode($item['q'] ?? $name)),
                            'jiomart_url' => $item['jiomart_url'] ?? ('https://www.jiomart.com/search/' . urlencode($item['q'] ?? $name)),
                            'status' => 'active'
                        ];

                        $this->saveProductInternal($productRecord, false);
                    }
                }
            }
        }
    }

    // ── Admin Authentication ──
    public function getAdminByUsername(string $username): ?array {
        if ($this->pdo) {
            $stmt = $this->pdo->prepare("SELECT * FROM admins WHERE username = ? LIMIT 1");
            $stmt->execute([$username]);
            $res = $stmt->fetch();
            return $res ?: null;
        }

        $data = $this->getJsonData();
        foreach ($data['admins'] as $admin) {
            if ($admin['username'] === $username) {
                return $admin;
            }
        }
        return null;
    }

    // ── Product Retrieval ──
    public function getAllProducts(array $filters = []): array {
        if ($this->pdo) {
            $sql = "SELECT * FROM products WHERE 1=1";
            $params = [];

            if (!empty($filters['category']) && $filters['category'] !== 'all') {
                $sql .= " AND category = ?";
                $params[] = $filters['category'];
            }
            if (!empty($filters['status']) && $filters['status'] !== 'all') {
                // Support filtering by publication_status or legacy status
                if (in_array($filters['status'], ['published', 'draft', 'archived'])) {
                    $sql .= " AND publication_status = ?";
                    $params[] = $filters['status'];
                } else {
                    $sql .= " AND status = ?";
                    $params[] = $filters['status'];
                }
            }
            if (!empty($filters['publication_status']) && $filters['publication_status'] !== 'all') {
                $sql .= " AND publication_status = ?";
                $params[] = $filters['publication_status'];
            }
            if (!empty($filters['sticker_ribbon']) && $filters['sticker_ribbon'] !== 'all') {
                $sql .= " AND sticker_ribbon = ?";
                $params[] = $filters['sticker_ribbon'];
            }
            if (!empty($filters['search'])) {
                $sql .= " AND (name LIKE ? OR sku LIKE ? OR description LIKE ? OR id = ?)";
                $s = '%' . $filters['search'] . '%';
                $params[] = $s;
                $params[] = $s;
                $params[] = $s;
                $params[] = is_numeric($filters['search']) ? (int)$filters['search'] : 0;
            }

            $sql .= " ORDER BY id DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        }

        $data = $this->getJsonData();
        $products = $data['products'] ?? [];

        return array_values(array_filter($products, function($p) use ($filters) {
            if (!empty($filters['category']) && $filters['category'] !== 'all' && ($p['category'] ?? '') !== $filters['category']) return false;
            if (!empty($filters['publication_status']) && $filters['publication_status'] !== 'all' && ($p['publication_status'] ?? 'published') !== $filters['publication_status']) return false;
            if (!empty($filters['status']) && $filters['status'] !== 'all' && ($p['publication_status'] ?? $p['status'] ?? 'published') !== $filters['status']) return false;
            if (!empty($filters['search'])) {
                $kw = strtolower($filters['search']);
                $n = strtolower($p['name'] ?? '');
                $s = strtolower($p['sku'] ?? (string)($p['id'] ?? ''));
                $d = strtolower($p['description'] ?? '');
                if (!str_contains($n, $kw) && !str_contains($s, $kw) && !str_contains($d, $kw)) return false;
            }
            return true;
        }));
    }

    public function getProductById(string|int $id): ?array {
        if (empty($id)) return null;

        if ($this->pdo) {
            $stmt = $this->pdo->prepare("SELECT * FROM products WHERE id = ? OR sku = ? LIMIT 1");
            $stmt->execute([$id, (string)$id]);
            $res = $stmt->fetch();
            return $res ?: null;
        }

        $data = $this->getJsonData();
        foreach ($data['products'] as $p) {
            if ((string)($p['id'] ?? '') === (string)$id || (string)($p['sku'] ?? '') === (string)$id) {
                return $p;
            }
        }
        return null;
    }

    /**
     * Search products strictly by product name using case-insensitive partial match (PDO prepared statement).
     *
     * @param string $query
     * @return array
     */
    public function searchProductsByName(string $query): array {
        $q = trim($query);
        if ($q === '') return [];

        if ($this->pdo) {
            $sql = "SELECT * FROM products 
                    WHERE name LIKE :search 
                    AND (publication_status = 'published' OR (publication_status IS NULL AND status = 'active'))
                    ORDER BY id DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':search' => '%' . $q . '%']);
            return $stmt->fetchAll();
        }

        $data = $this->getJsonData();
        $products = $data['products'] ?? [];
        $kw = strtolower($q);

        return array_values(array_filter($products, function($p) use ($kw) {
            $pubStatus = $p['publication_status'] ?? $p['status'] ?? 'published';
            if ($pubStatus !== 'published' && $pubStatus !== 'active') return false;
            $name = strtolower($p['name'] ?? '');
            return str_contains($name, $kw);
        }));
    }

    // ── Save Product (Insert or Update) ──
    public function saveProduct(array $p): bool {
        return $this->saveProductInternal($p, true);
    }

    private function saveProductInternal(array $p, bool $triggerSync = true): bool {
        $name = trim($p['name'] ?? '');
        if (empty($name)) return false;

        $sku = trim($p['sku'] ?? ($p['id'] ?? ''));
        if (empty($sku)) {
            $sku = 'PNN-' . strtoupper(substr(uniqid(), -6));
        }

        $slug = !empty($p['slug']) ? trim($p['slug']) : strtolower(preg_replace('/[^a-z0-9]+/i', '-', $name)) . '-' . strtolower($sku);
        $category = trim($p['category'] ?? 'men-shoes');
        $gender = self::normalizeGender($p['gender'] ?? null, $category);
        $silhouette = self::normalizeSilhouette($p['silhouette'] ?? $p['shape'] ?? null);
        $description = trim($p['description'] ?? '');
        $colorways = trim($p['colorways'] ?? ($p['colors'] ?? ''));
        $price = floatval($p['price'] ?? 0);
        $mrp = !empty($p['mrp']) ? floatval($p['mrp']) : null;
        $discount = !empty($p['discount']) ? floatval($p['discount']) : null;
        $stickerRibbon = self::normalizeStickerRibbon($p['sticker_ribbon'] ?? $p['sticker'] ?? null);
        $publicationStatus = in_array($p['publication_status'] ?? '', ['published', 'draft', 'archived']) ? $p['publication_status'] : 'draft';
        $status = in_array($p['status'] ?? '', ['active', 'inactive']) ? $p['status'] : 'active';

        $image = !empty($p['image']) ? trim($p['image']) : 'hero-shoe.png';
        $hoverImage = !empty($p['hover_image']) ? trim($p['hover_image']) : null;
        $galleryImage1 = !empty($p['gallery_image_1']) ? trim($p['gallery_image_1']) : null;
        $galleryImage2 = !empty($p['gallery_image_2']) ? trim($p['gallery_image_2']) : null;

        $amazonUrl = !empty($p['amazon_url']) ? trim($p['amazon_url']) : null;
        $flipkartUrl = !empty($p['flipkart_url']) ? trim($p['flipkart_url']) : null;
        $meeshoUrl = !empty($p['meesho_url']) ? trim($p['meesho_url']) : null;
        $myntraUrl = !empty($p['myntra_url']) ? trim($p['myntra_url']) : null;
        $ajioUrl = !empty($p['ajio_url']) ? trim($p['ajio_url']) : null;
        $snapdealUrl = !empty($p['snapdeal_url']) ? trim($p['snapdeal_url']) : null;
        $jiomartUrl = !empty($p['jiomart_url']) ? trim($p['jiomart_url']) : null;

        if ($this->pdo) {
            // Check if product already exists by numeric ID or SKU
            $existingId = null;
            if (!empty($p['id']) && is_numeric($p['id'])) {
                $chk = $this->pdo->prepare("SELECT id FROM products WHERE id = ? LIMIT 1");
                $chk->execute([(int)$p['id']]);
                $existingId = $chk->fetchColumn();
            }
            if (!$existingId && !empty($sku)) {
                $chk = $this->pdo->prepare("SELECT id FROM products WHERE sku = ? LIMIT 1");
                $chk->execute([$sku]);
                $existingId = $chk->fetchColumn();
            }

            if ($existingId) {
                // UPDATE existing row
                $sql = "UPDATE products SET 
                        name = ?, sku = ?, slug = ?, category = ?, gender = ?, silhouette = ?,
                        description = ?, colorways = ?, price = ?, mrp = ?, discount = ?,
                        sticker_ribbon = ?, publication_status = ?, image = ?, hover_image = ?,
                        gallery_image_1 = ?, gallery_image_2 = ?, amazon_url = ?, flipkart_url = ?,
                        meesho_url = ?, myntra_url = ?, ajio_url = ?, snapdeal_url = ?, jiomart_url = ?,
                        status = ?, updated_at = NOW()
                        WHERE id = ?";
                $stmt = $this->pdo->prepare($sql);
                $res = $stmt->execute([
                    $name, $sku, $slug, $category, $gender, $silhouette,
                    $description, $colorways, $price, $mrp, $discount,
                    $stickerRibbon, $publicationStatus, $image, $hoverImage,
                    $galleryImage1, $galleryImage2, $amazonUrl, $flipkartUrl,
                    $meeshoUrl, $myntraUrl, $ajioUrl, $snapdealUrl, $jiomartUrl,
                    $status, $existingId
                ]);
            } else {
                // INSERT new row
                $sql = "INSERT INTO products (
                        name, sku, slug, category, gender, silhouette,
                        description, colorways, price, mrp, discount,
                        sticker_ribbon, publication_status, image, hover_image,
                        gallery_image_1, gallery_image_2, amazon_url, flipkart_url,
                        meesho_url, myntra_url, ajio_url, snapdeal_url, jiomart_url,
                        status, created_at, updated_at
                        ) VALUES (
                        ?, ?, ?, ?, ?, ?,
                        ?, ?, ?, ?, ?,
                        ?, ?, ?, ?,
                        ?, ?, ?, ?,
                        ?, ?, ?, ?, ?,
                        ?, NOW(), NOW()
                        )";
                $stmt = $this->pdo->prepare($sql);
                $res = $stmt->execute([
                    $name, $sku, $slug, $category, $gender, $silhouette,
                    $description, $colorways, $price, $mrp, $discount,
                    $stickerRibbon, $publicationStatus, $image, $hoverImage,
                    $galleryImage1, $galleryImage2, $amazonUrl, $flipkartUrl,
                    $meeshoUrl, $myntraUrl, $ajioUrl, $snapdealUrl, $jiomartUrl,
                    $status
                ]);
            }

            if ($res && $triggerSync) {
                $this->syncPublicJson();
            }
            return (bool)$res;
        }

        // Fallback JSON save
        $data = $this->getJsonData();
        $record = [
            'id' => $sku,
            'name' => $name,
            'sku' => $sku,
            'slug' => $slug,
            'category' => $category,
            'gender' => $gender,
            'silhouette' => $silhouette,
            'description' => $description,
            'colorways' => $colorways,
            'price' => $price,
            'mrp' => $mrp,
            'discount' => $discount,
            'sticker_ribbon' => $stickerRibbon,
            'publication_status' => $publicationStatus,
            'image' => $image,
            'hover_image' => $hoverImage,
            'gallery_image_1' => $galleryImage1,
            'gallery_image_2' => $galleryImage2,
            'amazon_url' => $amazonUrl,
            'flipkart_url' => $flipkartUrl,
            'meesho_url' => $meeshoUrl,
            'myntra_url' => $myntraUrl,
            'ajio_url' => $ajioUrl,
            'snapdeal_url' => $snapdealUrl,
            'jiomart_url' => $jiomartUrl,
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $found = false;
        foreach ($data['products'] as &$item) {
            if ((string)($item['sku'] ?? $item['id'] ?? '') === (string)$sku) {
                $item = array_merge($item, $record);
                $found = true;
                break;
            }
        }
        if (!$found) {
            $record['created_at'] = date('Y-m-d H:i:s');
            $data['products'][] = $record;
        }

        $this->saveJsonData($data);
        if ($triggerSync) {
            $this->syncPublicJson();
        }
        return true;
    }

    // ── Delete / Archive Product ──
    public function deleteProduct(string|int $id): bool {
        if (empty($id)) return false;

        if ($this->pdo) {
            $stmt = $this->pdo->prepare("DELETE FROM products WHERE id = ? OR sku = ?");
            $res = $stmt->execute([$id, (string)$id]);
            if ($res) $this->syncPublicJson();
            return (bool)$res;
        }

        $data = $this->getJsonData();
        $data['products'] = array_values(array_filter($data['products'], function($p) use ($id) {
            return (string)($p['id'] ?? '') !== (string)$id && (string)($p['sku'] ?? '') !== (string)$id;
        }));
        $this->saveJsonData($data);
        $this->syncPublicJson();
        return true;
    }

    // ── Dashboard Metrics ──
    public function getDashboardStats(): array {
        $all = $this->getAllProducts(['status' => 'all']);
        
        $total = count($all);
        $published = 0;
        $draft = 0;
        $featured = 0;
        $newArrival = 0;

        foreach ($all as $p) {
            $pubStatus = $p['publication_status'] ?? $p['status'] ?? 'draft';
            if ($pubStatus === 'published') $published++;
            elseif ($pubStatus === 'draft') $draft++;
            
            $ribbon = $p['sticker_ribbon'] ?? $p['sticker'] ?? 'none';
            if ($ribbon === 'bestseller' || $ribbon === 'hot' || $ribbon === 'best') $featured++;
            if ($ribbon === 'new_arrival' || $ribbon === 'new') $newArrival++;
        }

        return [
            'total' => $total,
            'published' => $published,
            'draft' => $draft,
            'featured' => $featured,
            'newArrival' => $newArrival
        ];
    }

    // ── Automatic Public JSON Sync ──
    public function syncPublicJson(): void {
        $allPublished = $this->getAllProducts(['publication_status' => 'published']);

        $categoryBuckets = [
            'men-shoes' => [],
            'men-slippers' => [],
            'women-shoes' => [],
            'women-slippers' => []
        ];

        foreach ($allPublished as $p) {
            $cat = $p['category'] ?? 'men-shoes';
            if (!isset($categoryBuckets[$cat])) {
                $categoryBuckets[$cat] = [];
            }

            // Parse colorways into array for swatches
            $colorwaysArray = [];
            if (!empty($p['colorways'])) {
                $decoded = json_decode($p['colorways'], true);
                if (is_array($decoded)) {
                    $colorwaysArray = $decoded;
                } else {
                    $rawItems = explode(',', $p['colorways']);
                    foreach ($rawItems as $c) {
                        $c = trim($c);
                        if (!empty($c)) $colorwaysArray[] = $c;
                    }
                }
            }

            // Convert canonical sticker_ribbon to short alias for frontend templates
            $stickerShort = 'none';
            $ribbon = $p['sticker_ribbon'] ?? 'none';
            if ($ribbon === 'new_arrival') $stickerShort = 'new';
            elseif ($ribbon === 'bestseller') $stickerShort = 'best';
            elseif ($ribbon === 'hot') $stickerShort = 'hot';

            $galleryList = [];
            if (!empty($p['gallery_image_1'])) $galleryList[] = $p['gallery_image_1'];
            if (!empty($p['gallery_image_2'])) $galleryList[] = $p['gallery_image_2'];

            $sku = !empty($p['sku']) ? $p['sku'] : (string)$p['id'];

            // Build public product representation with both canonical and alias fields
            $publicItem = [
                'id' => $sku,
                'db_id' => (int)$p['id'],
                'sku' => $sku,
                'name' => $p['name'],
                'slug' => $p['slug'] ?? '',
                'category' => $cat,
                'gender' => $p['gender'] ?? 'unisex',
                'silhouette' => $p['silhouette'] ?? 'sneaker',
                'shape' => $p['silhouette'] ?? 'sneaker', // compatibility alias for JS filters
                'description' => $p['description'] ?? '',
                'price' => floatval($p['price']),
                'mrp' => !empty($p['mrp']) ? floatval($p['mrp']) : null,
                'discount' => !empty($p['discount']) ? floatval($p['discount']) : null,
                'colorways' => $p['colorways'] ?? '',
                'colors' => $colorwaysArray,
                'sticker_ribbon' => $ribbon,
                'sticker' => $stickerShort,
                'publication_status' => $p['publication_status'] ?? 'published',
                'is_featured' => ($ribbon === 'bestseller' || $ribbon === 'hot'),
                'is_new_arrival' => ($ribbon === 'new_arrival'),
                'image' => $p['image'] ?? 'hero-shoe.png',
                'hoverImage' => !empty($p['hover_image']) ? $p['hover_image'] : ($p['image'] ?? 'hero-shoe.png'),
                'hover_image' => $p['hover_image'] ?? '',
                'gallery_image_1' => $p['gallery_image_1'] ?? '',
                'gallery_image_2' => $p['gallery_image_2'] ?? '',
                'gallery_images' => $galleryList,
                'amazon_url' => $p['amazon_url'] ?? null,
                'flipkart_url' => $p['flipkart_url'] ?? null,
                'meesho_url' => $p['meesho_url'] ?? null,
                'snapdeal_url' => $p['snapdeal_url'] ?? null,
                'jiomart_url' => $p['jiomart_url'] ?? null,
                'ajio_url' => $p['ajio_url'] ?? null,
                'myntra_url' => $p['myntra_url'] ?? null,
                'q' => $p['name']
            ];

            $categoryBuckets[$cat][] = $publicItem;
        }

        // Write to respective category JSON files
        foreach ($categoryBuckets as $catKey => $items) {
            $destFile = __DIR__ . '/data/' . $catKey . '.json';
            $payload = [
                'category' => $catKey,
                'updated_at' => date('c'),
                'products' => $items
            ];
            file_put_contents($destFile, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }
    }
}
