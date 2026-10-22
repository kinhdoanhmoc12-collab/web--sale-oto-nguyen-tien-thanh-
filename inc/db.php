<?php
/**
 * Database SQLite: một file duy nhất data/site.db. Tự tạo bảng và dữ liệu mẫu lần đầu chạy.
 */

function db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        if (!is_dir(DATA_DIR)) {
            @mkdir(DATA_DIR, 0755, true);
        }
        $pdo = new PDO('sqlite:' . DATA_DIR . '/site.db', null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        $pdo->exec('PRAGMA journal_mode = WAL; PRAGMA foreign_keys = ON;');
    }
    return $pdo;
}

function q(string $sql, array $params = []): PDOStatement
{
    $st = db()->prepare($sql);
    $st->execute($params);
    return $st;
}
function rows(string $sql, array $params = []): array { return q($sql, $params)->fetchAll(); }
function row(string $sql, array $params = []): ?array { $r = q($sql, $params)->fetch(); return $r === false ? null : $r; }
function val(string $sql, array $params = []) { return q($sql, $params)->fetchColumn(); }

/** Insert / update a row from an associative array. */
function db_save(string $table, array $data, ?int $id = null): int
{
    $cols = array_keys($data);
    if ($id) {
        $set = implode(', ', array_map(fn($c) => "$c = :$c", $cols));
        q("UPDATE $table SET $set WHERE id = :__id", $data + ['__id' => $id]);
        return $id;
    }
    q("INSERT INTO $table (" . implode(', ', $cols) . ') VALUES (:' . implode(', :', $cols) . ')', $data);
    return (int) db()->lastInsertId();
}

function db_init(): void
{
    $have = (int) val("SELECT COUNT(*) FROM sqlite_master WHERE type='table' AND name='settings'");
    if ($have) {
        db_migrate();
        return;
    }
    db()->exec(<<<SQL
CREATE TABLE settings (k TEXT PRIMARY KEY, v TEXT);
CREATE TABLE cars (
  id INTEGER PRIMARY KEY AUTOINCREMENT, slug TEXT UNIQUE, name TEXT, ord INTEGER DEFAULT 0, active INTEGER DEFAULT 1,
  segment TEXT, versions TEXT, seats TEXT, engine TEXT, origin TEXT, warranty TEXT, specs TEXT, fee_kind TEXT DEFAULT '5',
  promos TEXT, feat_exterior TEXT, feat_interior TEXT, feat_drive TEXT, feat_safety TEXT,
  image TEXT, content TEXT, excerpt TEXT, updated_at TEXT
);
CREATE TABLE banners (id INTEGER PRIMARY KEY AUTOINCREMENT, image TEXT, link TEXT, alt TEXT, ord INTEGER DEFAULT 0, active INTEGER DEFAULT 1);
CREATE TABLE deliveries (
  id INTEGER PRIMARY KEY AUTOINCREMENT, image TEXT, car TEXT, cust_type TEXT, msg_by TEXT DEFAULT 'consultant',
  message TEXT, cust_name TEXT, ord INTEGER DEFAULT 0, active INTEGER DEFAULT 1
);
CREATE TABLE posts (
  id INTEGER PRIMARY KEY AUTOINCREMENT, slug TEXT UNIQUE, title TEXT, excerpt TEXT, content TEXT, image TEXT,
  category TEXT, status TEXT DEFAULT 'publish', created_at TEXT, updated_at TEXT
);
CREATE TABLE pages (k TEXT PRIMARY KEY, title TEXT, subtitle TEXT, image TEXT, content TEXT, meta_desc TEXT, updated_at TEXT);
CREATE TABLE leads (
  id INTEGER PRIMARY KEY AUTOINCREMENT, created_at TEXT, name TEXT, phone TEXT, car TEXT, region TEXT, pay TEXT,
  source TEXT, extra TEXT, page TEXT, ip TEXT, status TEXT DEFAULT 'new', note TEXT
);
CREATE TABLE login_attempts (ip TEXT, at INTEGER);
CREATE INDEX leads_created ON leads (created_at);
SQL);
    seed_all();
    db_migrate();
}

/** Nâng cấp database cũ: thêm cột mới khi cần, không đụng dữ liệu đang có. */
function db_migrate(): void
{
    $cols = array_column(rows('PRAGMA table_info(cars)'), 'name');
    if (!in_array('detail', $cols, true)) {
        db()->exec("ALTER TABLE cars ADD COLUMN detail TEXT DEFAULT ''");
    }
    if (function_exists('seed_car_details')) {
        seed_car_details();
    }
}
