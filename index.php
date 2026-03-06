<?php
ob_start();

// serve images from /uploads/ before anything else loads
$uri = $_SERVER['REQUEST_URI'] ?? '';
if (preg_match('#/uploads/([a-zA-Z0-9_\-\.]+)$#', $uri, $m)) {
    $file = basename($m[1]);
    $path = __DIR__ . '/uploads/' . $file;
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    $mimes = ['jpg'=>'image/jpeg','jpeg'=>'image/jpeg','png'=>'image/png','gif'=>'image/gif','webp'=>'image/webp'];

    if (preg_match('/^[a-zA-Z0-9_\-\.]+$/', $file) && file_exists($path) && isset($mimes[$ext])) {
        header('Content-Type: ' . $mimes[$ext]);
        header('Content-Length: ' . filesize($path));
        header('Cache-Control: public, max-age=2592000');
        readfile($path);
        exit;
    }
    http_response_code(404); exit;
}

/**
 * Hazel+ — a single-file PHP social network
 *
 * I built this because I liked Google+ and i wished i used it more. It's not perfect but it works,
 * and now it's open source so you can make it better than I can.
 *
 * v2.0.0 — going open source!
 * License: MIT
 * GitHub: https://github.com/hazel-plus/hazelplus
 *
 * Needs PHP 7.4+ and either SQLite (default) or MySQL.
 */

// --- config ---
// change these before deploying, especially ADMIN_PASS
define('DB_TYPE',   'sqlite');
define('DB_FILE',   __DIR__ . '/hazelplus.db');
define('DB_HOST',   'localhost');
define('DB_NAME',   'hazelplus');
define('DB_USER',   'root');
define('DB_PASS',   '');
define('SITE_NAME', 'Hazel+');
define('SITE_DESC', 'Share what matters. Connect for real.');
define('ADMIN_EMAIL', 'admin@example.com');
define('ADMIN_PASS',  'admin123');   // <-- PLEASE change this
define('UPLOAD_DIR',  __DIR__ . '/uploads/');
define('UPLOAD_URL',  '/uploads/');
define('VERSION',     '2.0.0');
define('GITHUB_URL',  'https://github.com/hazelplus/hazelplus');

// legal stuff — edit these to match your actual site
define('LEGAL_SITE_URL',  'https://example.com');
define('LEGAL_CONTACT',   'legal@legal.com');
define('LEGAL_EFFECTIVE', 'January 1, 2025');


// --- legal page content ---
// these are just strings, edit them however you like

function getTosHtml(): string {
    $s = SITE_NAME; $e = LEGAL_CONTACT; $d = LEGAL_EFFECTIVE;
    return <<<HTML
<h3>1. Acceptance of Terms</h3>
<p>By creating an account or using <strong>$s</strong> ("the Service"), you agree to be bound by
these Terms of Service. If you do not agree, please don't use the Service.</p>
<h3>2. Eligibility</h3>
<p>You must be at least <strong>13 years old</strong> to use the Service.</p>
<h3>3. Your Account</h3>
<ul>
  <li>Keep your password safe — you're responsible for what happens on your account.</li>
  <li>Don't share or transfer your account to someone else.</li>
  <li>Please use accurate info when registering.</li>
  <li>Don't create accounts to abuse or impersonate people.</li>
</ul>
<h3>4. Acceptable Use</h3>
<p>Don't post content that:</p>
<ul>
  <li>Is unlawful, harmful, threatening, abusive, or harassing.</li>
  <li>Infringes someone's intellectual property.</li>
  <li>Is spam, malware, or intentionally misleading.</li>
  <li>Violates someone's privacy.</li>
</ul>
<h3>5. Content Ownership</h3>
<p>You own what you post. By posting it, you give $s permission to display and share it through the Service.</p>
<h3>6. Termination</h3>
<p>We can suspend or delete accounts that break these rules. We'll try to be fair about it.</p>
<h3>7. Disclaimers</h3>
<p>The Service is provided "as is" — we do our best but can't guarantee anything. We're not liable for indirect or consequential damages from your use of the Service.</p>
<h3>8. Changes</h3>
<p>We might update these terms sometimes. Continuing to use the Service after changes means you accept them.</p>
<h3>9. Questions?</h3>
<p>Email us at <a href="mailto:$e">$e</a>.</p>
<p><em>Effective: $d</em></p>
HTML;
}

function getPrivacyHtml(): string {
    $s = SITE_NAME; $e = LEGAL_CONTACT; $d = LEGAL_EFFECTIVE;
    return <<<HTML
<h3>1. What We Collect</h3>
<ul>
  <li><strong>Account data:</strong> username, email, display name, and a hashed password.</li>
  <li><strong>Profile data:</strong> bio, tagline, location, website, avatar and cover images — whatever you choose to share.</li>
  <li><strong>Content:</strong> your posts, comments, and messages.</li>
  <li><strong>Basic logs:</strong> pages visited, timestamps, standard server logs.</li>
</ul>
<h3>2. How We Use It</h3>
<ul>
  <li>To run the site and keep things working.</li>
  <li>To notify you about activity on your account.</li>
  <li>To enforce our rules and keep the site safe.</li>
  <li>We <strong>don't</strong> sell your data. Ever.</li>
  <li>We <strong>don't</strong> run third-party ads.</li>
</ul>
<h3>3. Sharing</h3>
<p>We don't share your info with third parties except when legally required or to protect the safety of the site and its users.</p>
<h3>4. Cookies</h3>
<p>Just one session cookie to keep you logged in. No tracking, no analytics cookies, no third-party cookies.</p>
<h3>5. Data Retention</h3>
<p>Your data sticks around as long as your account is active. Want it gone? Email us and we'll delete everything within 30 days.</p>
<h3>6. Security</h3>
<p>Passwords are hashed with bcrypt. We take reasonable precautions, though no internet service is 100% bulletproof.</p>
<h3>7. Kids</h3>
<p>The Service isn't for anyone under 13. If we find out we have data from a child under 13, we delete it immediately.</p>
<h3>8. Your Rights</h3>
<p>Depending on where you live, you may have the right to access, correct, or delete your data. Email <a href="mailto:$e">$e</a> and we'll help.</p>
<h3>9. Changes</h3>
<p>We'll let registered users know if anything major changes here.</p>
<h3>10. Contact</h3>
<p>Privacy questions? Email <a href="mailto:$e">$e</a>.</p>
<p><em>Effective: $d</em></p>
HTML;
}

function getGuidelinesHtml(): string {
    $s = SITE_NAME; $e = LEGAL_CONTACT;
    return <<<HTML
<h3>1. Be a decent human</h3>
<p>Disagree all you want — that's fine. But don't harass people, don't make personal attacks, and don't target people because of who they are.</p>
<h3>2. No hate speech</h3>
<p>Content that promotes hatred or discrimination against people based on race, ethnicity, religion, gender, sexuality, disability, or nationality isn't allowed here.</p>
<h3>3. No harassment</h3>
<p>Don't intimidate, threaten, or repeatedly go after another user. That includes dogpiling, sharing someone's private info to embarrass them, and unsolicited hostile messages.</p>
<h3>4. Keep it legal</h3>
<p>Don't post anything illegal where you or the recipient live. That includes copyrighted stuff you don't have rights to, defamatory content, impersonation, and — this one's absolute — any sexual content involving minors. That gets reported to law enforcement immediately, no exceptions.</p>
<h3>5. No spam</h3>
<p>Don't flood feeds, run undisclosed bots, or do anything that looks like coordinated fake activity. Clickbait and misleading links aren't welcome either.</p>
<h3>6. Don't promote self-harm</h3>
<p>Posts that encourage or glorify self-harm, suicide, eating disorders, or other dangerous health behaviors aren't allowed.</p>
<h3>7. Respect privacy</h3>
<p>Don't post someone's home address, phone number, private photos, or financial details without their explicit consent. That's doxxing and it results in an immediate ban.</p>
<h3>8. Adult content</h3>
<p>Explicit content is only allowed in communities clearly marked 18+. Everything else needs to be safe for a general audience. Artistic or educational nudity is up to moderator judgment.</p>
<h3>9. Community rules</h3>
<p>Individual communities can set extra rules on top of these. Mods can remove content or people who break those rules, as long as the community rules don't contradict these site-wide ones.</p>
<h3>10. Consequences</h3>
<p>Breaking the rules can mean a content removal, a suspension, or a permanent ban — depending on how bad it is and whether it's happened before. Serious stuff (CSAM, credible violence threats) goes straight to law enforcement.</p>
<h3>11. Reporting</h3>
<p>See something that shouldn't be here? Email <a href="mailto:$e">$e</a>. We review everything and try to respond within 48 hours.</p>
<h3>12. Appeals</h3>
<p>Think we got it wrong? Email <a href="mailto:$e">$e</a> with "Appeal" in the subject line and explain what happened. We'll look at it within 5 business days.</p>
HTML;
}


// --- database ---

function getDB(): PDO {
    static $pdo = null;
    if ($pdo) return $pdo;

    if (DB_TYPE === 'sqlite')
        $pdo = new PDO('sqlite:' . DB_FILE);
    else
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    if (DB_TYPE === 'sqlite') $pdo->exec('PRAGMA foreign_keys = ON;');

    return $pdo;
}

function setupDatabase(): void {
    $db = getDB();
    // sqlite uses AUTOINCREMENT, mysql uses AUTO_INCREMENT
    $ai  = DB_TYPE === 'sqlite' ? 'INTEGER PRIMARY KEY AUTOINCREMENT' : 'INT AUTO_INCREMENT PRIMARY KEY';
    $txt = DB_TYPE === 'sqlite' ? 'TEXT' : 'LONGTEXT';

    $db->exec("CREATE TABLE IF NOT EXISTS users (
        id $ai, username TEXT NOT NULL UNIQUE, email TEXT NOT NULL UNIQUE,
        password TEXT NOT NULL, display_name TEXT NOT NULL,
        bio TEXT DEFAULT '', avatar TEXT DEFAULT '', cover TEXT DEFAULT '',
        role TEXT DEFAULT 'user', tagline TEXT DEFAULT '',
        location TEXT DEFAULT '', website TEXT DEFAULT '',
        early_access INTEGER DEFAULT 0,
        tos_accepted INTEGER DEFAULT 0,
        suspended INTEGER DEFAULT 0,
        suspend_reason TEXT DEFAULT '',
        suspended_until DATETIME DEFAULT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP, last_login DATETIME)");

    $db->exec("CREATE TABLE IF NOT EXISTS posts (
        id $ai, user_id INTEGER NOT NULL, content $txt NOT NULL,
        image TEXT DEFAULT '', visibility TEXT DEFAULT 'public',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE)");

    $db->exec("CREATE TABLE IF NOT EXISTS comments (
        id $ai, post_id INTEGER NOT NULL, user_id INTEGER NOT NULL,
        content TEXT NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY(post_id) REFERENCES posts(id) ON DELETE CASCADE,
        FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE)");

    $db->exec("CREATE TABLE IF NOT EXISTS plusones (
        id $ai, post_id INTEGER NOT NULL, user_id INTEGER NOT NULL,
        UNIQUE(post_id, user_id),
        FOREIGN KEY(post_id) REFERENCES posts(id) ON DELETE CASCADE,
        FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE)");

    $db->exec("CREATE TABLE IF NOT EXISTS circles (
        id $ai, user_id INTEGER NOT NULL, name TEXT NOT NULL,
        color TEXT DEFAULT '#4285f4',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE)");

    $db->exec("CREATE TABLE IF NOT EXISTS circle_members (
        id $ai, circle_id INTEGER NOT NULL, member_id INTEGER NOT NULL,
        UNIQUE(circle_id, member_id),
        FOREIGN KEY(circle_id) REFERENCES circles(id) ON DELETE CASCADE,
        FOREIGN KEY(member_id) REFERENCES users(id) ON DELETE CASCADE)");

    $db->exec("CREATE TABLE IF NOT EXISTS follows (
        id $ai, follower_id INTEGER NOT NULL, following_id INTEGER NOT NULL,
        UNIQUE(follower_id, following_id),
        FOREIGN KEY(follower_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY(following_id) REFERENCES users(id) ON DELETE CASCADE)");

    $db->exec("CREATE TABLE IF NOT EXISTS notifications (
        id $ai, user_id INTEGER NOT NULL, from_user_id INTEGER NOT NULL,
        type TEXT NOT NULL, ref_id INTEGER DEFAULT 0, message TEXT DEFAULT '',
        is_read INTEGER DEFAULT 0, created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE)");

    $db->exec("CREATE TABLE IF NOT EXISTS communities (
        id $ai, owner_id INTEGER NOT NULL, name TEXT NOT NULL UNIQUE,
        description TEXT DEFAULT '', visibility TEXT DEFAULT 'public',
        color TEXT DEFAULT '#4285f4', icon TEXT DEFAULT '🌐', banner_color TEXT DEFAULT '',
        banner_image TEXT DEFAULT '',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY(owner_id) REFERENCES users(id) ON DELETE CASCADE)");

    $db->exec("CREATE TABLE IF NOT EXISTS community_members (
        id $ai, community_id INTEGER NOT NULL, user_id INTEGER NOT NULL,
        role TEXT DEFAULT 'member', UNIQUE(community_id, user_id),
        FOREIGN KEY(community_id) REFERENCES communities(id) ON DELETE CASCADE,
        FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE)");

    $db->exec("CREATE TABLE IF NOT EXISTS messages (
        id $ai, from_id INTEGER NOT NULL, to_id INTEGER NOT NULL,
        content TEXT NOT NULL, is_read INTEGER DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY(from_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY(to_id) REFERENCES users(id) ON DELETE CASCADE)");

    $db->exec("CREATE TABLE IF NOT EXISTS reshares (
        id $ai, post_id INTEGER NOT NULL, user_id INTEGER NOT NULL,
        comment TEXT DEFAULT '',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        UNIQUE(post_id, user_id),
        FOREIGN KEY(post_id) REFERENCES posts(id) ON DELETE CASCADE,
        FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE)");

    $db->exec("CREATE TABLE IF NOT EXISTS settings (key TEXT PRIMARY KEY, value TEXT DEFAULT '')");

    // migrations — just try adding columns, ignore if they already exist
    $migrations = [
        "ALTER TABLE communities ADD COLUMN color TEXT DEFAULT '#4285f4'",
        "ALTER TABLE communities ADD COLUMN icon TEXT DEFAULT '🌐'",
        "ALTER TABLE communities ADD COLUMN banner_color TEXT DEFAULT ''",
        "ALTER TABLE communities ADD COLUMN banner_image TEXT DEFAULT ''",
        "ALTER TABLE posts ADD COLUMN community_id INTEGER DEFAULT NULL",
        "ALTER TABLE users ADD COLUMN early_access INTEGER DEFAULT 0",
        "ALTER TABLE users ADD COLUMN tos_accepted INTEGER DEFAULT 0",
        "ALTER TABLE users ADD COLUMN verified INTEGER DEFAULT 0",
        "ALTER TABLE users ADD COLUMN suspended INTEGER DEFAULT 0",
        "ALTER TABLE users ADD COLUMN suspend_reason TEXT DEFAULT ''",
        "ALTER TABLE users ADD COLUMN suspended_until DATETIME DEFAULT NULL",
        "ALTER TABLE circles ADD COLUMN color TEXT DEFAULT '#4285f4'",
        "ALTER TABLE posts ADD COLUMN original_post_id INTEGER DEFAULT NULL",
        "ALTER TABLE posts ADD COLUMN reshare_comment TEXT DEFAULT ''",
        "ALTER TABLE communities ADD COLUMN icon_image TEXT DEFAULT ''",
    ];
    foreach ($migrations as $m) {
        try { $db->exec($m); } catch (Exception $e) { /* already exists, that's fine */ }
    }

    // default settings
    $db->exec("INSERT OR IGNORE INTO settings (key,value) VALUES ('early_access_mode','0')");
    $db->exec("INSERT OR IGNORE INTO settings (key,value) VALUES ('maintenance_mode','0')");

    // create default admin if none exists
    if (!$db->query("SELECT id FROM users WHERE role='admin' LIMIT 1")->fetch()) {
        $hash = password_hash(ADMIN_PASS, PASSWORD_DEFAULT);
        $db->prepare("INSERT INTO users (username,email,password,display_name,role,bio,tos_accepted) VALUES (?,?,?,?,?,?,1)")
           ->execute(['admin', ADMIN_EMAIL, $hash, 'Administrator', 'admin', 'Site admin of ' . SITE_NAME]);
        $aid = $db->lastInsertId();
        // give the admin default circles
        foreach (['Friends' => '#4285f4', 'Family' => '#0f9d58', 'Acquaintances' => '#f4b400', 'Following' => '#dd4b39'] as $name => $color)
            $db->prepare("INSERT INTO circles (user_id,name,color) VALUES (?,?,?)")->execute([$aid, $name, $color]);
    }
}

// settings helpers
function getSetting(string $key): string {
    try {
        $s = getDB()->prepare("SELECT value FROM settings WHERE key=?");
        $s->execute([$key]);
        $r = $s->fetch();
        return $r ? $r['value'] : '';
    } catch (Exception $e) { return ''; }
}
function setSetting(string $key, string $val): void {
    getDB()->prepare("INSERT OR REPLACE INTO settings (key,value) VALUES (?,?)")->execute([$key, $val]);
}
function isEarlyAccess(): bool  { return getSetting('early_access_mode') === '1'; }
function isMaintenanceMode(): bool { return getSetting('maintenance_mode') === '1'; }


// --- suspension helpers ---

function isUserSuspended(array $user): bool {
    if (!(int)($user['suspended'] ?? 0)) return false;

    $until = $user['suspended_until'] ?? null;
    if (!$until) return true; // permanent

    if (strtotime($until) > time()) return true;

    // suspension expired, lift it automatically
    getDB()->prepare("UPDATE users SET suspended=0, suspend_reason='', suspended_until=NULL WHERE id=?")
           ->execute([$user['id']]);
    return false;
}

function suspendUser(int $uid, string $reason, ?string $until = null): void {
    getDB()->prepare("UPDATE users SET suspended=1, suspend_reason=?, suspended_until=? WHERE id=?")
           ->execute([$reason, $until, $uid]);
}

function unsuspendUser(int $uid): void {
    getDB()->prepare("UPDATE users SET suspended=0, suspend_reason='', suspended_until=NULL WHERE id=?")
           ->execute([$uid]);
}

// --- session / auth helpers ---

session_start();

function currentUser(): ?array {
    if (!isset($_SESSION['user_id'])) return null;
    static $cu = null;
    if ($cu) return $cu;
    $s = getDB()->prepare("SELECT * FROM users WHERE id=?");
    $s->execute([$_SESSION['user_id']]);
    $cu = $s->fetch() ?: null;
    return $cu;
}

function requireLogin(): void  { if (!currentUser()) redirect('?page=login'); }
function requireAdmin(): void  { $u = currentUser(); if (!$u || $u['role'] !== 'admin') redirect('?page=home'); }
function redirect(string $url): void { header("Location: $url"); exit; }
function h(string $s): string  { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

function timeAgo(string $dt): string {
    $diff = time() - strtotime($dt);
    if ($diff < 60)     return 'just now';
    if ($diff < 3600)   return floor($diff / 60) . 'm';
    if ($diff < 86400)  return floor($diff / 3600) . 'h';
    if ($diff < 604800) return floor($diff / 86400) . 'd';
    return date('M j, Y', strtotime($dt));
}

function uploadFile(array $f, string $prefix = ''): string {
    $dir = __DIR__ . '/uploads/';
    if (!is_dir($dir)) mkdir($dir, 0755, true);

    $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if (!in_array($ext, $allowed) || $f['size'] > 5 * 1024 * 1024) return '';

    $name = $prefix . uniqid() . '.' . $ext;
    move_uploaded_file($f['tmp_name'], $dir . $name);
    return '/uploads/' . $name;
}

function addNotif(int $to, int $from, string $type, int $ref, string $msg): void {
    if ($to === $from) return; // don't notify yourself
    getDB()->prepare("INSERT INTO notifications (user_id,from_user_id,type,ref_id,message) VALUES (?,?,?,?,?)")
           ->execute([$to, $from, $type, $ref, $msg]);
}

function unreadNotifs(): int {
    $u = currentUser();
    if (!$u) return 0;
    $s = getDB()->prepare("SELECT COUNT(*) FROM notifications WHERE user_id=? AND is_read=0");
    $s->execute([$u['id']]);
    return (int)$s->fetchColumn();
}

function unreadMsgs(): int {
    $u = currentUser();
    if (!$u) return 0;
    $s = getDB()->prepare("SELECT COUNT(*) FROM messages WHERE to_id=? AND is_read=0");
    $s->execute([$u['id']]);
    return (int)$s->fetchColumn();
}

// generate an avatar — either the uploaded image or an SVG initial placeholder
function avatarSrc(?string $path, string $name = '?'): string {
    if ($path) return h($path);
    $l = strtoupper($name[0] ?? '?');
    $colors = ['c0392b','2980b9','27ae60','d35400','8e44ad','16a085','e67e22','2c3e50'];
    $c = $colors[ord($l) % count($colors)];
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="80" height="80">'
         . '<rect width="80" height="80" fill="#' . $c . '"/>'
         . '<text x="40" y="52" font-size="36" font-family="Arial,sans-serif" font-weight="bold" fill="#fff" text-anchor="middle">' . htmlspecialchars($l) . '</text>'
         . '</svg>';
    return 'data:image/svg+xml;base64,' . base64_encode($svg);
}

function getAdminId(): int {
    $r = getDB()->query("SELECT id FROM users WHERE role='admin' LIMIT 1")->fetch();
    return $r ? (int)$r['id'] : 0;
}

function userBadges(array $user): string {
    $out = '';
    if ($user['role'] === 'admin')
        $out .= '<span class="badge badge-admin">&#10004; Admin</span>';
    elseif ((int)($user['early_access'] ?? 0) === 1)
        $out .= '<span class="badge badge-ea">&#9889; Early Access</span>';
    if ((int)($user['suspended'] ?? 0) === 1)
        $out .= '<span class="badge badge-suspended">&#128683; Suspended</span>';
    return $out;
}

function circleColorPalette(): array {
    return ['#4285f4','#dd4b39','#0f9d58','#f4b400','#9b6a2f','#8e44ad','#16a085','#e67e22','#e91e63','#00bcd4','#795548','#607d8b'];
}

function reshareCount(int $postId): int {
    $s = getDB()->prepare("SELECT COUNT(*) FROM reshares WHERE post_id=?");
    $s->execute([$postId]);
    return (int)$s->fetchColumn();
}

function userReshared(int $postId, int $userId): bool {
    $s = getDB()->prepare("SELECT id FROM reshares WHERE post_id=? AND user_id=?");
    $s->execute([$postId, $userId]);
    return (bool)$s->fetch();
}

function renderReshareEmbed(array $orig): string {
    $av  = avatarSrc($orig['avatar'] ?? '', $orig['display_name'] ?? '?');
    $dn  = h($orig['display_name'] ?? 'Unknown');
    $un  = h($orig['username'] ?? '');
    $uid = (int)($orig['user_id'] ?? 0);
    $t   = timeAgo($orig['created_at'] ?? '');
    $body = h($orig['content'] ?? '');
    $img = $orig['image'] ?? '';

    $out  = '<div class="reshare-embed">';
    $out .= '<div class="reshare-embed-head">';
    $out .= '<a href="?page=profile&id=' . $uid . '"><img src="' . $av . '" style="width:32px;height:32px;border-radius:50%;object-fit:cover;flex-shrink:0"></a>';
    $out .= '<div style="flex:1;min-width:0">';
    $out .= '<div style="font-size:13px;font-weight:500"><a href="?page=profile&id=' . $uid . '" style="color:#212121">' . $dn . '</a></div>';
    $out .= '<div style="font-size:11px;color:var(--sub)">@' . $un . ' &middot; ' . $t . '</div>';
    $out .= '</div></div>';
    if ($img)  $out .= '<img src="' . h($img) . '" class="reshare-embed-img">';
    if ($body) $out .= '<div class="reshare-embed-body">' . $body . '</div>';
    $out .= '</div>';
    return $out;
}

// run db setup — if this blows up, show the error rather than a blank page
try { setupDatabase(); } catch (Exception $e) {
    die("<pre style='color:red;padding:20px'>Database error: " . $e->getMessage() . "\n\nMake sure the directory is writable.</pre>");
}

$page   = $_GET['page'] ?? 'home';
$action = $_POST['action'] ?? '';
$earlyAccess = isEarlyAccess();


// --- maintenance mode gate ---
// admins can still log in, everyone else sees a holding page

if (isMaintenanceMode()) {
    $_cu = currentUser();
    $isAdmin       = $_cu && $_cu['role'] === 'admin';
    $isLoginPage   = ($page === 'login');
    $isLoginAction = ($action === 'login');

    if (!$isAdmin && !$isLoginPage && !$isLoginAction && $action !== 'logout') {
        ob_start();
?>
<div class="guest-banner">
  <h2><?= SITE_NAME ?> is being worked on</h2>
  <p>We're making things better — check back soon.</p>
  <div class="guest-banner-btns">
    <a href="?page=login" class="gbtn gbtn-outline" style="color:#fff;border-color:rgba(255,255,255,.4)">Admin Sign In</a>
  </div>
</div>
<div class="gcard" style="overflow:hidden">
  <img src="https://files.catbox.moe/jnbj8r.png" alt="" style="width:100%;display:block" onerror="this.style.display='none'">
  <div style="padding:14px 16px;font-size:13px;color:#757575;border-top:1px solid #e0e0e0"><?= SITE_NAME ?> &middot; v<?= VERSION ?></div>
</div>
<?php
        $maintContent = ob_get_clean();
        $page = '__maintenance__';
        $_maintContent = $maintContent;
    }
}

// --- POST handlers ---

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($action) {

        case 'circles_add_member':
            requireLogin(); $u = currentUser();
            header('Content-Type: application/json');
            $cid = (int)($_POST['circle_id'] ?? 0);
            $mid = (int)($_POST['member_id'] ?? 0);
            $chk = getDB()->prepare("SELECT id FROM circles WHERE id=? AND user_id=?");
            $chk->execute([$cid, $u['id']]);
            if (!$chk->fetch()) { echo json_encode(['ok' => false, 'error' => 'not your circle']); exit; }
            try {
                getDB()->prepare("INSERT INTO circle_members (circle_id,member_id) VALUES (?,?)")->execute([$cid, $mid]);
                echo json_encode(['ok' => true]);
            } catch (Exception $e) {
                echo json_encode(['ok' => false, 'error' => 'already in circle']);
            }
            exit;

        case 'circles_remove_member':
            requireLogin(); $u = currentUser();
            header('Content-Type: application/json');
            $cid = (int)($_POST['circle_id'] ?? 0);
            $mid = (int)($_POST['member_id'] ?? 0);
            $chk = getDB()->prepare("SELECT id FROM circles WHERE id=? AND user_id=?");
            $chk->execute([$cid, $u['id']]);
            if (!$chk->fetch()) { echo json_encode(['ok' => false]); exit; }
            getDB()->prepare("DELETE FROM circle_members WHERE circle_id=? AND member_id=?")->execute([$cid, $mid]);
            echo json_encode(['ok' => true]); exit;

        case 'circles_create':
            requireLogin(); $u = currentUser();
            header('Content-Type: application/json');
            $name  = trim($_POST['name'] ?? '');
            $color = preg_match('/^#[0-9a-fA-F]{6}$/', $_POST['color'] ?? '') ? $_POST['color'] : '#4285f4';
            if (!$name) { echo json_encode(['ok' => false, 'error' => 'name required']); exit; }
            getDB()->prepare("INSERT INTO circles (user_id,name,color) VALUES (?,?,?)")->execute([$u['id'], $name, $color]);
            $newId = (int)getDB()->lastInsertId();
            echo json_encode(['ok' => true, 'id' => $newId, 'name' => $name, 'color' => $color]); exit;

        case 'circles_rename':
            requireLogin(); $u = currentUser();
            header('Content-Type: application/json');
            $cid   = (int)($_POST['circle_id'] ?? 0);
            $name  = trim($_POST['name'] ?? '');
            $color = preg_match('/^#[0-9a-fA-F]{6}$/', $_POST['color'] ?? '') ? $_POST['color'] : '';
            $chk   = getDB()->prepare("SELECT id FROM circles WHERE id=? AND user_id=?");
            $chk->execute([$cid, $u['id']]);
            if (!$chk->fetch()) { echo json_encode(['ok' => false]); exit; }
            if ($name)  getDB()->prepare("UPDATE circles SET name=? WHERE id=?")->execute([$name, $cid]);
            if ($color) getDB()->prepare("UPDATE circles SET color=? WHERE id=?")->execute([$color, $cid]);
            echo json_encode(['ok' => true]); exit;

        case 'circles_delete':
            requireLogin(); $u = currentUser();
            header('Content-Type: application/json');
            $cid = (int)($_POST['circle_id'] ?? 0);
            $chk = getDB()->prepare("SELECT id FROM circles WHERE id=? AND user_id=?");
            $chk->execute([$cid, $u['id']]);
            if (!$chk->fetch()) { echo json_encode(['ok' => false]); exit; }
            getDB()->prepare("DELETE FROM circles WHERE id=?")->execute([$cid]);
            echo json_encode(['ok' => true]); exit;

        case 'login':
            $em  = trim($_POST['email'] ?? '');
            $pw  = $_POST['password'] ?? '';
            $s   = getDB()->prepare("SELECT * FROM users WHERE email=? OR username=?");
            $s->execute([$em, $em]);
            $usr = $s->fetch();
            if ($usr && password_verify($pw, $usr['password'])) {
                if (isUserSuspended($usr)) {
                    $reason = $usr['suspend_reason'] ?: 'Violation of community guidelines.';
                    $until  = $usr['suspended_until']
                        ? ' Your suspension lifts on ' . date('F j, Y \a\t g:i A', strtotime($usr['suspended_until'])) . '.'
                        : ' This suspension is permanent.';
                    $_SESSION['error'] = 'Your account has been suspended. Reason: ' . $reason . $until;
                    redirect('?page=login');
                }
                $_SESSION['user_id'] = $usr['id'];
                getDB()->prepare("UPDATE users SET last_login=CURRENT_TIMESTAMP WHERE id=?")->execute([$usr['id']]);
                redirect('?page=home');
            }
            $_SESSION['error'] = 'Wrong email or password.';
            redirect('?page=login');
            break;

        case 'register':
            if (isEarlyAccess()) { $_SESSION['error'] = 'Registration is currently closed.'; redirect('?page=register'); break; }
            if (empty($_POST['agree_tos'])) { $_SESSION['error'] = 'You need to agree to the Terms of Service to create an account.'; redirect('?page=register&step=3'); break; }

            $pw = $_POST['password'] ?? '';
            $pc = $_POST['password_confirm'] ?? '';
            if ($pw !== $pc) { $_SESSION['error'] = 'Passwords do not match.'; redirect('?page=register&step=3'); }

            $un = preg_replace('/[^a-z0-9_]/', '', strtolower(trim($_POST['username'] ?? $_SESSION['reg_un'] ?? '')));
            $em = trim($_POST['email'] ?? $_SESSION['reg_em'] ?? '');
            $dn = trim($_POST['display_name'] ?? $_SESSION['reg_name'] ?? '');

            if (strlen($un) < 3 || strlen($pw) < 6 || !filter_var($em, FILTER_VALIDATE_EMAIL) || !$dn) {
                $_SESSION['error'] = 'Fill in all fields. Username needs at least 3 characters, password at least 6.';
                redirect('?page=register&step=1');
            }

            try {
                getDB()->prepare("INSERT INTO users (username,email,password,display_name,early_access,tos_accepted) VALUES (?,?,?,?,0,1)")
                       ->execute([$un, $em, password_hash($pw, PASSWORD_DEFAULT), $dn]);
                $nid = (int)getDB()->lastInsertId();

                foreach (['Friends' => '#4285f4', 'Family' => '#0f9d58', 'Acquaintances' => '#f4b400', 'Following' => '#dd4b39'] as $name => $color)
                    getDB()->prepare("INSERT INTO circles (user_id,name,color) VALUES (?,?,?)")->execute([$nid, $name, $color]);

                // auto-follow the admin so new users see something on first login
                $adminId = getAdminId();
                if ($adminId && $adminId !== $nid)
                    try { getDB()->prepare("INSERT INTO follows (follower_id,following_id) VALUES (?,?)")->execute([$nid, $adminId]); } catch (Exception $e) {}

                unset($_SESSION['reg_name'], $_SESSION['reg_un'], $_SESSION['reg_em']);
                $_SESSION['user_id'] = $nid;
                redirect('?page=home');
            } catch (Exception $e) {
                $_SESSION['error'] = 'That username or email is already taken.';
                redirect('?page=register&step=2');
            }
            break;

        case 'reg_step1':
            $fn = trim($_POST['first_name'] ?? '');
            $ln = trim($_POST['last_name'] ?? '');
            if (!$fn) { $_SESSION['error'] = 'Please enter your first name.'; redirect('?page=register&step=1'); }
            $_SESSION['reg_name'] = trim($fn . ($ln ? ' ' . $ln : ''));
            redirect('?page=register&step=2');
            break;

        case 'reg_step2':
            $un = preg_replace('/[^a-z0-9_]/', '', strtolower(trim($_POST['username'] ?? '')));
            $em = trim($_POST['email'] ?? '');
            if (strlen($un) < 3) { $_SESSION['error'] = 'Username must be at least 3 characters.'; redirect('?page=register&step=2'); }
            if (!filter_var($em, FILTER_VALIDATE_EMAIL)) { $_SESSION['error'] = 'That doesn\'t look like a valid email.'; redirect('?page=register&step=2'); }
            $chk = getDB()->prepare("SELECT id FROM users WHERE username=? OR email=?");
            $chk->execute([$un, $em]);
            if ($chk->fetch()) { $_SESSION['error'] = 'That username or email is already taken.'; redirect('?page=register&step=2'); }
            $_SESSION['reg_un'] = $un;
            $_SESSION['reg_em'] = $em;
            redirect('?page=register&step=3');
            break;

        case 'logout':
            session_destroy();
            redirect('?page=login');
            break;

        case 'post':
            requireLogin(); $u = currentUser();
            if (isUserSuspended($u)) { $_SESSION['error'] = 'Your account is suspended.'; redirect('?page=home'); break; }
            $content = trim($_POST['content'] ?? '');
            $vis     = $_POST['visibility'] ?? 'public';
            if (!$content && empty($_FILES['image']['name'])) { redirect('?page=home'); break; }
            $img = '';
            if (!empty($_FILES['image']['name'])) $img = uploadFile($_FILES['image'], 'post_');
            getDB()->prepare("INSERT INTO posts (user_id,content,image,visibility) VALUES (?,?,?,?)")
                   ->execute([$u['id'], $content, $img, $vis]);
            redirect('?page=home');
            break;

        case 'community_post':
            requireLogin(); $u = currentUser();
            if (isUserSuspended($u)) { redirect('?page=communities'); break; }
            $cid     = (int)($_POST['community_id'] ?? 0);
            $content = trim($_POST['content'] ?? '');
            if (!$content && empty($_FILES['image']['name'])) { redirect('?page=community&id=' . $cid); break; }

            $commChk = getDB()->prepare("SELECT owner_id FROM communities WHERE id=?");
            $commChk->execute([$cid]);
            $commRow = $commChk->fetch();
            if (!$commRow) { redirect('?page=communities'); break; }

            $isCommOwner = ($commRow['owner_id'] == $u['id'] || $u['role'] === 'admin');
            $memChk = getDB()->prepare("SELECT id FROM community_members WHERE community_id=? AND user_id=?");
            $memChk->execute([$cid, $u['id']]);
            $memRow = $memChk->fetch();

            // auto-join owners if they somehow aren't already a member
            if ($isCommOwner && !$memRow) {
                try { getDB()->prepare("INSERT INTO community_members (community_id,user_id,role) VALUES (?,?,?)")->execute([$cid, $u['id'], 'owner']); } catch (Exception $e) {}
            }
            if (!$isCommOwner && !$memRow) { redirect('?page=community&id=' . $cid); break; }

            $img = '';
            if (!empty($_FILES['image']['name'])) $img = uploadFile($_FILES['image'], 'post_');
            getDB()->prepare("INSERT INTO posts (user_id,content,image,visibility,community_id) VALUES (?,?,?,'public',?)")
                   ->execute([$u['id'], $content, $img, $cid]);
            redirect('?page=community&id=' . $cid);
            break;

        case 'comment':
            requireLogin(); $u = currentUser();
            if (isUserSuspended($u)) { redirect($_SERVER['HTTP_REFERER'] ?? '?page=home'); break; }
            $pid = (int)($_POST['post_id'] ?? 0);
            $ct  = trim($_POST['content'] ?? '');
            if (!$ct || !$pid) { redirect($_SERVER['HTTP_REFERER'] ?? '?page=home'); break; }
            getDB()->prepare("INSERT INTO comments (post_id,user_id,content) VALUES (?,?,?)")->execute([$pid, $u['id'], $ct]);
            $s = getDB()->prepare("SELECT user_id FROM posts WHERE id=?");
            $s->execute([$pid]);
            $pp = $s->fetch();
            if ($pp) addNotif($pp['user_id'], $u['id'], 'comment', $pid, h($u['display_name']) . ' commented on your post.');
            redirect($_SERVER['HTTP_REFERER'] ?? '?page=home');
            break;

        case 'reshare':
            requireLogin(); $u = currentUser();
            header('Content-Type: application/json');
            if (isUserSuspended($u)) { echo json_encode(['ok' => false, 'error' => 'Account suspended']); exit; }

            $pid = (int)($_POST['post_id'] ?? 0);
            $cmt = trim($_POST['comment'] ?? '');

            $origStmt = getDB()->prepare("SELECT * FROM posts WHERE id=?");
            $origStmt->execute([$pid]);
            $orig = $origStmt->fetch();
            if (!$orig) { echo json_encode(['ok' => false, 'error' => 'Post not found']); exit; }
            if ($orig['user_id'] == $u['id']) { echo json_encode(['ok' => false, 'error' => 'Can\'t reshare your own post']); exit; }

            // always reshare the original, not a reshare-of-reshare
            $rootId = $orig['original_post_id'] ? (int)$orig['original_post_id'] : $pid;

            try {
                getDB()->prepare("INSERT INTO reshares (post_id,user_id,comment) VALUES (?,?,?)")->execute([$rootId, $u['id'], $cmt]);
                getDB()->prepare("INSERT INTO posts (user_id,content,image,visibility,original_post_id,reshare_comment) VALUES (?,?,?,?,?,?)")
                       ->execute([$u['id'], '', '', $orig['visibility'], $rootId, $cmt]);
                $rootStmt = getDB()->prepare("SELECT user_id FROM posts WHERE id=?");
                $rootStmt->execute([$rootId]);
                $rootRow = $rootStmt->fetch();
                if ($rootRow) addNotif($rootRow['user_id'], $u['id'], 'reshare', $rootId, h($u['display_name']) . ' reshared your post.');
                $cnt = getDB()->prepare("SELECT COUNT(*) FROM reshares WHERE post_id=?");
                $cnt->execute([$rootId]);
                echo json_encode(['ok' => true, 'count' => (int)$cnt->fetchColumn()]);
            } catch (Exception $e) {
                // already reshared — toggle it off
                getDB()->prepare("DELETE FROM reshares WHERE post_id=? AND user_id=?")->execute([$rootId, $u['id']]);
                getDB()->prepare("DELETE FROM posts WHERE user_id=? AND original_post_id=?")->execute([$u['id'], $rootId]);
                $cnt = getDB()->prepare("SELECT COUNT(*) FROM reshares WHERE post_id=?");
                $cnt->execute([$rootId]);
                echo json_encode(['ok' => true, 'count' => (int)$cnt->fetchColumn(), 'undone' => true]);
            }
            exit;

        case 'ripples':
            header('Content-Type: application/json');
            $pid = (int)($_GET['post_id'] ?? 0);
            $rs2 = getDB()->prepare("SELECT r.*,u.display_name,u.username,u.avatar,u.tagline
                FROM reshares r JOIN users u ON u.id=r.user_id
                WHERE r.post_id=? ORDER BY r.created_at ASC");
            $rs2->execute([$pid]);
            $reshares = $rs2->fetchAll();
            $nodes = []; $edges = [];
            $rootPost = getDB()->prepare("SELECT p.*,u.display_name,u.username,u.avatar FROM posts p JOIN users u ON u.id=p.user_id WHERE p.id=?");
            $rootPost->execute([$pid]);
            $root = $rootPost->fetch();
            if ($root) $nodes[0] = ['id' => 0, 'uid' => $root['user_id'], 'name' => $root['display_name'], 'avatar' => $root['avatar'], 'username' => $root['username'], 'type' => 'root'];
            foreach ($reshares as $i => $r) {
                $nodes[] = ['id' => $i+1, 'uid' => $r['user_id'], 'name' => $r['display_name'], 'avatar' => $r['avatar'], 'username' => $r['username'], 'type' => 'reshare', 'comment' => $r['comment'], 'time' => $r['created_at']];
                $edges[]  = ['from' => 0, 'to' => $i+1];
            }
            echo json_encode(['nodes' => $nodes, 'edges' => $edges, 'total' => count($reshares)]);
            exit;

        case 'plusone':
            requireLogin(); $u = currentUser();
            if (isUserSuspended($u)) {
                header('Content-Type: application/json');
                $c = getDB()->prepare("SELECT COUNT(*) FROM plusones WHERE post_id=?");
                $c->execute([$_POST['post_id'] ?? 0]);
                echo json_encode(['count' => (int)$c->fetchColumn()]); exit;
            }
            $pid = (int)($_POST['post_id'] ?? 0);
            try {
                getDB()->prepare("INSERT INTO plusones (post_id,user_id) VALUES (?,?)")->execute([$pid, $u['id']]);
                $s = getDB()->prepare("SELECT user_id FROM posts WHERE id=?");
                $s->execute([$pid]);
                $pp = $s->fetch();
                if ($pp) addNotif($pp['user_id'], $u['id'], 'plusone', $pid, h($u['display_name']) . " +1'd your post.");
            } catch (Exception $e) {
                // already +1'd, remove it (toggle)
                getDB()->prepare("DELETE FROM plusones WHERE post_id=? AND user_id=?")->execute([$pid, $u['id']]);
            }
            header('Content-Type: application/json');
            $c = getDB()->prepare("SELECT COUNT(*) FROM plusones WHERE post_id=?");
            $c->execute([$pid]);
            echo json_encode(['count' => (int)$c->fetchColumn()]); exit;

        case 'follow':
            requireLogin(); $u = currentUser();
            if (isUserSuspended($u)) { redirect($_SERVER['HTTP_REFERER'] ?? '?page=home'); break; }
            $tid = (int)($_POST['target_id'] ?? 0);
            if ($tid === $u['id']) break;
            try {
                getDB()->prepare("INSERT INTO follows (follower_id,following_id) VALUES (?,?)")->execute([$u['id'], $tid]);
                addNotif($tid, $u['id'], 'follow', 0, h($u['display_name']) . ' started following you.');
            } catch (Exception $e) {
                getDB()->prepare("DELETE FROM follows WHERE follower_id=? AND following_id=?")->execute([$u['id'], $tid]);
            }
            redirect($_SERVER['HTTP_REFERER'] ?? '?page=home');
            break;

        case 'toggle_follow':
            header('Content-Type: application/json');
            requireLogin(); $u = currentUser();
            if (isUserSuspended($u)) { echo json_encode(['ok' => false, 'error' => 'Account suspended']); exit; }
            $tid = (int)($_POST['followee_id'] ?? 0);
            if (!$tid || $tid === $u['id']) { echo json_encode(['ok' => false, 'error' => 'Invalid user']); exit; }
            $existing = getDB()->prepare("SELECT 1 FROM follows WHERE follower_id=? AND following_id=?");
            $existing->execute([$u['id'], $tid]);
            if ($existing->fetch()) {
                getDB()->prepare("DELETE FROM follows WHERE follower_id=? AND following_id=?")->execute([$u['id'], $tid]);
                echo json_encode(['ok' => true, 'following' => false]);
            } else {
                getDB()->prepare("INSERT INTO follows (follower_id,following_id) VALUES (?,?)")->execute([$u['id'], $tid]);
                addNotif($tid, $u['id'], 'follow', 0, h($u['display_name']) . ' started following you.');
                echo json_encode(['ok' => true, 'following' => true]);
            }
            exit;

        case 'update_profile':
            requireLogin(); $u = currentUser();
            if (isUserSuspended($u)) { redirect('?page=home'); break; }
            $dn  = trim($_POST['display_name'] ?? $u['display_name']);
            $bio = trim($_POST['bio'] ?? '');
            $tl  = trim($_POST['tagline'] ?? '');
            $loc = trim($_POST['location'] ?? '');
            $web = trim($_POST['website'] ?? '');
            $av  = $u['avatar'];
            $cv  = $u['cover'];
            if (!empty($_FILES['avatar']['name'])) $av = uploadFile($_FILES['avatar'], 'av_');
            if (!empty($_FILES['cover']['name']))  $cv = uploadFile($_FILES['cover'], 'cv_');
            getDB()->prepare("UPDATE users SET display_name=?,bio=?,tagline=?,location=?,website=?,avatar=?,cover=? WHERE id=?")
                   ->execute([$dn, $bio, $tl, $loc, $web, $av, $cv, $u['id']]);
            redirect('?page=profile&id=' . $u['id']);
            break;

        case 'send_message':
            requireLogin(); $u = currentUser();
            if (isUserSuspended($u)) { redirect('?page=messages'); break; }
            $tid = (int)($_POST['to_id'] ?? 0);
            $ct  = trim($_POST['content'] ?? '');
            if (!$ct || !$tid) break;
            getDB()->prepare("INSERT INTO messages (from_id,to_id,content) VALUES (?,?,?)")->execute([$u['id'], $tid, $ct]);
            addNotif($tid, $u['id'], 'message', 0, h($u['display_name']) . ' sent you a message.');
            redirect('?page=messages&with=' . $tid);
            break;

        case 'create_community':
            requireLogin(); $u = currentUser();
            if (isUserSuspended($u)) { redirect('?page=communities'); break; }
            $nm    = trim($_POST['name'] ?? '');
            $ds    = trim($_POST['description'] ?? '');
            $vi    = $_POST['visibility'] ?? 'public';
            $color = preg_match('/^#[0-9a-fA-F]{6}$/', $_POST['color'] ?? '') ? $_POST['color'] : '#4285f4';
            $icon  = trim($_POST['icon'] ?? '') ?: '🌐';
            $banner = preg_match('/^#[0-9a-fA-F]{6}$/', $_POST['banner_color'] ?? '') ? $_POST['banner_color'] : '';
            $bannerImg = !empty($_FILES['banner_image']['name']) ? uploadFile($_FILES['banner_image'], 'comm_banner_') : '';
            $iconImg   = !empty($_FILES['icon_image']['name'])   ? uploadFile($_FILES['icon_image'], 'comm_icon_')   : '';
            if (!$nm) { redirect('?page=communities'); break; }
            try {
                getDB()->prepare("INSERT INTO communities (owner_id,name,description,visibility,color,icon,banner_color,banner_image,icon_image) VALUES (?,?,?,?,?,?,?,?,?)")
                       ->execute([$u['id'], $nm, $ds, $vi, $color, $icon, $banner, $bannerImg, $iconImg]);
                $cid = getDB()->lastInsertId();
                getDB()->prepare("INSERT INTO community_members (community_id,user_id,role) VALUES (?,?,?)")->execute([$cid, $u['id'], 'owner']);
            } catch (Exception $e) {}
            redirect('?page=communities');
            break;

        case 'edit_community':
            requireLogin(); $u = currentUser();
            $cid = (int)($_POST['community_id'] ?? 0);
            $chk = getDB()->prepare("SELECT * FROM communities WHERE id=?");
            $chk->execute([$cid]);
            $row = $chk->fetch();
            if (!$row || ($row['owner_id'] != $u['id'] && $u['role'] !== 'admin')) { redirect('?page=communities'); break; }
            $nm    = trim($_POST['name'] ?? '');
            $ds    = trim($_POST['description'] ?? '');
            $vi    = $_POST['visibility'] ?? 'public';
            $color = preg_match('/^#[0-9a-fA-F]{6}$/', $_POST['color'] ?? '') ? $_POST['color'] : '#4285f4';
            $icon  = trim($_POST['icon'] ?? '') ?: '🌐';
            $banner = preg_match('/^#[0-9a-fA-F]{6}$/', $_POST['banner_color'] ?? '') ? $_POST['banner_color'] : '';
            $bannerImg = $row['banner_image'] ?? '';
            if (!empty($_FILES['banner_image']['name'])) $bannerImg = uploadFile($_FILES['banner_image'], 'comm_banner_');
            if (!empty($_POST['clear_banner_image'])) $bannerImg = '';
            $iconImg = $row['icon_image'] ?? '';
            if (!empty($_FILES['icon_image']['name'])) $iconImg = uploadFile($_FILES['icon_image'], 'comm_icon_');
            if (!empty($_POST['clear_icon_image'])) $iconImg = '';
            if (!$nm) { redirect('?page=community&id=' . $cid); break; }
            try {
                getDB()->prepare("UPDATE communities SET name=?,description=?,visibility=?,color=?,icon=?,banner_color=?,banner_image=?,icon_image=? WHERE id=?")
                       ->execute([$nm, $ds, $vi, $color, $icon, $banner, $bannerImg, $iconImg, $cid]);
            } catch (Exception $e) {}
            redirect('?page=community&id=' . $cid . '&edited=1');
            break;

        case 'join_community':
            requireLogin(); $u = currentUser();
            if (isUserSuspended($u)) { redirect('?page=community&id=' . ($_POST['community_id'] ?? 0)); break; }
            $cid = (int)($_POST['community_id'] ?? 0);
            try {
                getDB()->prepare("INSERT INTO community_members (community_id,user_id) VALUES (?,?)")->execute([$cid, $u['id']]);
            } catch (Exception $e) {
                getDB()->prepare("DELETE FROM community_members WHERE community_id=? AND user_id=?")->execute([$cid, $u['id']]);
            }
            redirect('?page=community&id=' . $cid);
            break;

        case 'mark_notifs':
            requireLogin(); $u = currentUser();
            getDB()->prepare("UPDATE notifications SET is_read=1 WHERE user_id=?")->execute([$u['id']]);
            redirect('?page=notifications');
            break;

        case 'delete_post':
            requireLogin(); $u = currentUser();
            $pid = (int)($_POST['post_id'] ?? 0);
            $s   = getDB()->prepare("SELECT user_id FROM posts WHERE id=?");
            $s->execute([$pid]);
            $pp = $s->fetch();
            if ($pp && ($pp['user_id'] == $u['id'] || $u['role'] === 'admin'))
                getDB()->prepare("DELETE FROM posts WHERE id=?")->execute([$pid]);
            redirect($_SERVER['HTTP_REFERER'] ?? '?page=home');
            break;

        case 'admin_delete_user':
            requireAdmin();
            $uid = (int)($_POST['user_id'] ?? 0);
            if ($uid !== (int)currentUser()['id'])
                getDB()->prepare("DELETE FROM users WHERE id=?")->execute([$uid]);
            redirect('?page=admin');
            break;

        case 'admin_toggle_role':
            requireAdmin();
            $uid = (int)($_POST['user_id'] ?? 0);
            $s   = getDB()->prepare("SELECT role FROM users WHERE id=?");
            $s->execute([$uid]);
            $r = $s->fetch();
            if ($r && $uid !== (int)currentUser()['id'])
                getDB()->prepare("UPDATE users SET role=? WHERE id=?")->execute([$r['role'] === 'admin' ? 'user' : 'admin', $uid]);
            redirect('?page=admin');
            break;

        case 'admin_toggle_early_access':
            requireAdmin();
            setSetting('early_access_mode', isEarlyAccess() ? '0' : '1');
            redirect('?page=admin');
            break;

        case 'admin_verify':
            header('Content-Type: application/json');
            requireAdmin();
            $uid = (int)($_POST['user_id'] ?? 0);
            $v   = (int)($_POST['verified'] ?? 0);
            if (!$uid) { echo json_encode(['ok' => false]); exit; }
            getDB()->prepare("UPDATE users SET verified=? WHERE id=?")->execute([$v ? 1 : 0, $uid]);
            echo json_encode(['ok' => true, 'verified' => (bool)$v]); exit;

        case 'admin_toggle_maintenance':
            requireAdmin();
            setSetting('maintenance_mode', isMaintenanceMode() ? '0' : '1');
            redirect('?page=admin&tab=settings');
            break;

        case 'admin_save_settings':
            requireAdmin();
            foreach (['site_name', 'site_description'] as $k)
                if (isset($_POST[$k])) setSetting($k, trim($_POST[$k]));
            $_SESSION['success'] = 'Settings saved.';
            redirect('?page=admin&tab=settings');
            break;

        case 'admin_clear_base64':
            requireAdmin();
            getDB()->exec("UPDATE users SET avatar='' WHERE avatar LIKE 'data:%'");
            getDB()->exec("UPDATE users SET cover='' WHERE cover LIKE 'data:%'");
            getDB()->exec("UPDATE posts SET image='' WHERE image LIKE 'data:%'");
            getDB()->exec("UPDATE communities SET banner_image='' WHERE banner_image LIKE 'data:%'");
            getDB()->exec("UPDATE communities SET icon_image='' WHERE icon_image LIKE 'data:%'");
            $_SESSION['success'] = 'Cleared all base64 images. You\'ll need to re-upload profile photos.';
            redirect('?page=admin');
            break;

        case 'admin_suspend_user':
            requireAdmin();
            $uid    = (int)($_POST['user_id'] ?? 0);
            $reason = trim($_POST['suspend_reason'] ?? '');
            $type   = $_POST['suspend_type'] ?? 'permanent';
            $days   = max(1, (int)($_POST['suspend_days'] ?? 1));
            if (!$reason) { $_SESSION['error'] = 'Please provide a reason for the suspension.'; redirect('?page=admin'); break; }
            $until = $type === 'temporary' ? date('Y-m-d H:i:s', strtotime("+{$days} days")) : null;
            suspendUser($uid, $reason, $until);
            $uRow = getDB()->prepare("SELECT display_name FROM users WHERE id=?");
            $uRow->execute([$uid]);
            $uRow = $uRow->fetch();
            $_SESSION['success'] = 'User "' . h($uRow['display_name'] ?? '') . '" has been suspended.';
            redirect('?page=admin');
            break;

        case 'admin_unsuspend_user':
            requireAdmin();
            $uid = (int)($_POST['user_id'] ?? 0);
            unsuspendUser($uid);
            $uRow = getDB()->prepare("SELECT display_name FROM users WHERE id=?");
            $uRow->execute([$uid]);
            $uRow = $uRow->fetch();
            $_SESSION['success'] = 'Lifted suspension for "' . h($uRow['display_name'] ?? '') . '".';
            redirect('?page=admin');
            break;

        case 'admin_create_user':
            requireAdmin();
            $un = preg_replace('/[^a-z0-9_]/', '', strtolower(trim($_POST['username'] ?? '')));
            $em = trim($_POST['email'] ?? '');
            $pw = $_POST['password'] ?? '';
            $dn = trim($_POST['display_name'] ?? '');
            $ea = isEarlyAccess() ? 1 : 0;
            if (strlen($un) < 3 || strlen($pw) < 6 || !filter_var($em, FILTER_VALIDATE_EMAIL) || !$dn) {
                $_SESSION['error'] = 'Fill in all fields. Username needs 3+ chars, password 6+.';
                redirect('?page=admin'); break;
            }
            try {
                getDB()->prepare("INSERT INTO users (username,email,password,display_name,early_access,tos_accepted) VALUES (?,?,?,?,?,1)")
                       ->execute([$un, $em, password_hash($pw, PASSWORD_DEFAULT), $dn, $ea]);
                $nid = (int)getDB()->lastInsertId();
                foreach (['Friends' => '#4285f4', 'Family' => '#0f9d58', 'Acquaintances' => '#f4b400', 'Following' => '#dd4b39'] as $name => $color)
                    getDB()->prepare("INSERT INTO circles (user_id,name,color) VALUES (?,?,?)")->execute([$nid, $name, $color]);
                $_SESSION['success'] = 'Created user "' . $dn . '".';
            } catch (Exception $e) {
                $_SESSION['error'] = 'That username or email is already taken.';
            }
            redirect('?page=admin');
            break;
    }
}


$u           = currentUser();
$notifCount  = unreadNotifs();
$msgCount    = unreadMsgs();
$error       = $_SESSION['error'] ?? '';
$success     = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);

// bounce suspended users to the suspended page
if ($u && isUserSuspended($u) && $action !== 'logout' && !in_array($page, ['login', 'suspended'])) {
    $page = 'suspended';
}

$COMM_ICONS  = ['🌐','🎮','🎨','📸','🎵','🏆','💡','🔬','📚','🌱','🏠','✈️','🍕','⚽','🎬','💻','🐾','🌸','🎯','🔥'];
$COMM_COLORS = ['#4285f4','#dd4b39','#0f9d58','#f4b400','#9b6a2f','#8e44ad','#16a085','#2c3e50','#e67e22','#e91e63'];
$maintenanceMode = isMaintenanceMode();
$isLegalPage = in_array($page, ['tos','privacy','guidelines','about']);

?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= SITE_NAME ?><?php if ($isLegalPage): ?> — <?php
  if ($page==='tos') echo 'Terms of Service';
  elseif ($page==='privacy') echo 'Privacy Policy';
  elseif ($page==='guidelines') echo 'Community Guidelines';
  else echo 'About';
endif; ?></title>
<link href="https://fonts.googleapis.com/css2?family=Product+Sans:wght@400;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0}html{font-size:14px}
body{font-family:'Roboto',sans-serif;background:#e8e8e8;color:#212121;min-height:100vh}
a{color:#4285f4;text-decoration:none}a:hover{text-decoration:underline}img{display:block}
:root{--gred:#dd4b39;--gblue:#4285f4;--ggreen:#0f9d58;--gyellow:#f4b400;--topbar:#2d2d2d;--topbar-hover:#3d3d3d;--card:#fff;--border:#e0e0e0;--text:#212121;--sub:#757575;--radius:3px;--hazel:#9b6a2f;--hazel-light:#c8934a;--success:#0f9d58;--danger:#dd4b39;--bg:#e8e8e8}
#topbar{background:var(--topbar);height:59px;display:flex;align-items:center;padding:0 16px;position:sticky;top:0;z-index:200;box-shadow:0 2px 4px rgba(0,0,0,.4)}
.topbar-logo{display:flex;align-items:center;text-decoration:none;margin-right:8px;font-family:'Product Sans','Roboto',sans-serif;font-size:22px;font-weight:700;letter-spacing:-.5px;color:#fff;white-space:nowrap}
.topbar-logo .hz{color:var(--hazel-light)}.topbar-logo .plus{color:#fff;font-size:26px;line-height:1;position:relative;top:-1px}
.ea-indicator{font-size:10px;background:#f4b400;color:#000;font-weight:700;padding:2px 7px;border-radius:8px;margin-left:8px;letter-spacing:.3px;font-family:'Roboto',sans-serif}
.maint-indicator{font-size:10px;background:#dd4b39;color:#fff;font-weight:700;padding:2px 7px;border-radius:8px;margin-left:8px;letter-spacing:.3px;font-family:'Roboto',sans-serif}
.topbar-search{flex:1;max-width:680px;margin:0 16px;position:relative}
.topbar-search input{width:100%;height:32px;padding:0 12px 0 36px;background:rgba(255,255,255,.15);border:none;border-radius:2px;color:#fff;font-size:13px;outline:none}
.topbar-search input::placeholder{color:rgba(255,255,255,.6)}.topbar-search .si{position:absolute;left:10px;top:50%;transform:translateY(-50%);width:16px;height:16px;opacity:.7;pointer-events:none;fill:#fff}
.topbar-spacer{flex:1}
.topbar-btn{display:flex;align-items:center;justify-content:center;width:40px;height:40px;border-radius:50%;background:transparent;border:none;cursor:pointer;color:#fff;position:relative;transition:background .15s;text-decoration:none}
.topbar-btn:hover{background:var(--topbar-hover);text-decoration:none;color:#fff}.topbar-btn svg{fill:#fff;width:22px;height:22px}
.tnb{position:absolute;top:4px;right:4px;background:var(--gred);color:#fff;font-size:10px;font-weight:700;border-radius:8px;padding:1px 4px;min-width:16px;text-align:center;line-height:14px}
.topbar-avatar{width:32px;height:32px;border-radius:50%;object-fit:cover;border:2px solid rgba(255,255,255,.3);margin:0 6px}
.topbar-signout{color:rgba(255,255,255,.7);font-size:12px;padding:6px 12px;border:1px solid rgba(255,255,255,.3);border-radius:2px;background:transparent;cursor:pointer;margin-left:8px;transition:.15s;white-space:nowrap}
.topbar-signout:hover{background:rgba(255,255,255,.1);color:#fff}
#subnav{background:#3d3d3d;display:flex;align-items:center;padding:0 16px;border-bottom:1px solid #222}
.subnav-tab{color:rgba(255,255,255,.7);font-size:13px;font-weight:500;padding:0 16px;height:40px;display:flex;align-items:center;gap:6px;border-bottom:3px solid transparent;transition:color .15s;cursor:pointer;text-decoration:none;white-space:nowrap}
.subnav-tab:hover{color:#fff;text-decoration:none}.subnav-tab.active{color:#fff;border-bottom-color:var(--gred)}.subnav-tab svg{width:16px;height:16px;fill:currentColor;opacity:.8}
#wrap{max-width:1110px;margin:0 auto;padding:16px 12px}
#wrap.stream-layout{display:flex;gap:16px;align-items:flex-start}
#stream{flex:1;min-width:0}#sidebar-left{width:195px;flex-shrink:0}#sidebar-right{width:235px;flex-shrink:0}
@media(max-width:960px){#sidebar-right{display:none}}@media(max-width:680px){#sidebar-left{display:none}}
.gcard{background:var(--card);border:1px solid var(--border);border-radius:var(--radius);margin-bottom:12px;overflow:hidden}
.sleft-profile{padding:16px;text-align:center;border-bottom:1px solid var(--border)}
.sleft-nav a{display:flex;align-items:center;gap:10px;padding:10px 16px;font-size:13px;color:#333;transition:background .15s}
.sleft-nav a:hover{background:#f5f5f5;text-decoration:none}.sleft-nav a.active{background:#e8f0fe;color:var(--gblue)}.sleft-nav a svg{width:18px;height:18px;fill:#666}.sleft-nav a.active svg{fill:var(--gblue)}
.sleft-section{font-size:11px;font-weight:700;color:#aaa;text-transform:uppercase;letter-spacing:.8px;padding:10px 16px 4px}
.sleft-circle-link{display:flex;align-items:center;gap:9px;padding:8px 16px;font-size:13px;color:#444;cursor:pointer;transition:background .12s;text-decoration:none;border-left:3px solid transparent}
.sleft-circle-link:hover{background:#f5f5f5;text-decoration:none;color:#333}
.sleft-circle-link.active{background:#e8f0fe;color:var(--gblue);border-left-color:var(--circle-color,var(--gblue));font-weight:500}
.sleft-circle-dot{width:11px;height:11px;border-radius:50%;flex-shrink:0}
.sleft-circle-count{margin-left:auto;font-size:11px;color:#bbb;font-weight:400}
.sleft-circle-link.active .sleft-circle-count{color:var(--gblue);opacity:.7}
.stream-filter-header{display:flex;align-items:center;gap:10px;padding:11px 16px;margin-bottom:0;background:#fff;border:1px solid var(--border);border-radius:3px 3px 0 0;border-bottom:2px solid var(--filter-color,var(--gblue));margin-bottom:2px}
.stream-filter-header .filter-dot{width:13px;height:13px;border-radius:50%;flex-shrink:0}
.stream-filter-header .filter-name{font-size:14px;font-weight:500;flex:1}
.stream-filter-header .filter-clear{font-size:12px;color:var(--sub);text-decoration:none;padding:3px 8px;border-radius:2px;transition:background .1s}
.stream-filter-header .filter-clear:hover{background:#f5f5f5;color:var(--text)}
.composer-top{display:flex;gap:12px;padding:16px 16px 0}
.composer-top textarea{flex:1;border:1px solid var(--border);border-radius:2px;padding:10px 12px;font-size:14px;font-family:inherit;resize:vertical;min-height:70px;outline:none;transition:border-color .2s}
.composer-top textarea:focus{border-color:#bbb}
.composer-actions{display:flex;align-items:center;justify-content:space-between;padding:10px 16px 12px;border-top:1px solid var(--border);margin-top:10px}
.composer-tools{display:flex;align-items:center;gap:4px}
.composer-tool{display:flex;align-items:center;gap:4px;padding:6px 10px;border-radius:2px;border:none;background:transparent;cursor:pointer;color:var(--sub);font-size:12px;transition:.15s}
.composer-tool:hover{background:#f5f5f5;color:var(--text)}.composer-tool svg{width:18px;height:18px;fill:currentColor}
.vis-select{font-size:12px;border:1px solid var(--border);border-radius:2px;padding:5px 8px;color:var(--sub);background:#fff;cursor:pointer;outline:none}
.gbtn{display:inline-flex;align-items:center;justify-content:center;gap:6px;padding:8px 18px;border:none;border-radius:2px;font-size:13px;font-weight:500;font-family:inherit;cursor:pointer;transition:box-shadow .15s,background .15s;text-decoration:none;white-space:nowrap}
.gbtn:hover{text-decoration:none}
.gbtn-primary{background:var(--gblue);color:#fff;box-shadow:0 1px 2px rgba(0,0,0,.2)}.gbtn-primary:hover{background:#3367d6;color:#fff}
.gbtn-blue{background:var(--gblue);color:#fff;box-shadow:0 1px 2px rgba(0,0,0,.2)}.gbtn-blue:hover{background:#3367d6;color:#fff}
.gbtn-red{background:var(--gred);color:#fff}.gbtn-red:hover{background:#c53929;color:#fff}
.gbtn-outline{background:#f5f5f5;color:#444;border:1px solid #ddd}.gbtn-outline:hover{background:#e8e8e8;color:#212121}
.gbtn-gold{background:linear-gradient(135deg,#f4b400,#e09000);color:#000;font-weight:700}.gbtn-gold:hover{background:linear-gradient(135deg,#e09000,#bf7800);color:#000}
.gbtn-sm{padding:5px 12px;font-size:12px}
.badge{display:inline-flex;align-items:center;font-size:11px;font-weight:700;padding:2px 7px;border-radius:10px;margin-left:5px;vertical-align:middle;letter-spacing:.2px;line-height:1.4}
.badge-admin{background:linear-gradient(135deg,#4285f4,#1a73e8);color:#fff}
.badge-ea{background:linear-gradient(135deg,#f4b400,#e09000);color:#000}
.badge-suspended{background:linear-gradient(135deg,#dd4b39,#c53929);color:#fff}
.post-head{display:flex;align-items:flex-start;gap:10px;padding:14px 16px 6px}.post-av{width:46px;height:46px;border-radius:50%;object-fit:cover;flex-shrink:0}
.post-meta{flex:1}.post-author{font-size:14px;font-weight:500}.post-author a{color:#212121}.post-author a:hover{color:var(--gblue);text-decoration:underline}
.post-time{font-size:12px;color:var(--sub);margin-top:1px;display:flex;align-items:center;gap:6px}.vis-chip{font-size:11px;color:var(--sub)}
.post-body{padding:4px 16px 10px;font-size:14px;line-height:1.65;color:#333;white-space:pre-wrap;word-break:break-word}
.post-img{width:100%;max-height:520px;object-fit:cover}
.post-bar{display:flex;align-items:center;padding:4px 10px;border-top:1px solid var(--border)}
.pbar-btn{display:flex;align-items:center;gap:5px;padding:8px 10px;border-radius:2px;border:none;background:transparent;cursor:pointer;font-size:13px;color:var(--sub);transition:background .15s,color .15s}
.pbar-btn:hover{background:#f5f5f5;color:var(--text)}.pbar-btn.plusoned{color:var(--gred)}.pbar-btn.reshared{color:var(--gblue)}.pbar-btn svg{width:18px;height:18px;fill:currentColor}
.reshare-popover{position:absolute;z-index:300;background:#fff;border:1px solid #ddd;border-radius:3px;box-shadow:0 4px 16px rgba(0,0,0,.15);padding:14px 16px;width:300px;top:calc(100% + 6px);left:0}
.reshare-popover textarea{width:100%;border:1px solid #ddd;border-radius:2px;padding:8px 10px;font-size:13px;font-family:inherit;resize:none;outline:none;min-height:70px}
.reshare-popover textarea:focus{border-color:var(--gblue)}
.reshare-popover-actions{display:flex;justify-content:flex-end;gap:7px;margin-top:9px}
.reshare-embed{margin:0 16px 12px;border:1px solid #e8e8e8;border-radius:3px;overflow:hidden;background:#fafafa}
.reshare-embed-head{display:flex;align-items:center;gap:8px;padding:10px 12px;border-bottom:1px solid #e8e8e8;background:#fff}
.reshare-embed-body{padding:8px 12px;font-size:13px;color:#444;line-height:1.6;white-space:pre-wrap;word-break:break-word}
.reshare-embed-img{width:100%;max-height:280px;object-fit:cover;display:block}
.reshare-comment{padding:4px 16px 10px;font-size:14px;line-height:1.65;color:#333;white-space:pre-wrap;word-break:break-word;font-style:italic}
.reshare-comment:before{content:'\201C';font-style:normal;color:var(--sub);margin-right:2px}
.reshare-comment:after{content:'\201D';font-style:normal;color:var(--sub);margin-left:2px}
.ripples-modal-bg{position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:900;display:flex;align-items:center;justify-content:center;opacity:0;pointer-events:none;transition:opacity .2s}
.ripples-modal-bg.open{opacity:1;pointer-events:auto}
.ripples-modal{background:#1a1a2e;color:#fff;border-radius:6px;width:680px;max-width:95vw;max-height:88vh;display:flex;flex-direction:column;overflow:hidden;box-shadow:0 12px 48px rgba(0,0,0,.6)}
.ripples-modal-header{padding:16px 20px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid rgba(255,255,255,.08)}
.ripples-modal-header h3{font-size:16px;font-weight:400;color:#fff;display:flex;align-items:center;gap:8px}
.ripples-close{background:none;border:none;cursor:pointer;color:rgba(255,255,255,.5);font-size:24px;line-height:1;padding:0 2px}
.ripples-close:hover{color:#fff}
.ripples-canvas-wrap{flex:1;overflow:hidden;position:relative;min-height:360px}
#ripplesCanvas{width:100%;height:100%;display:block}
.ripples-footer{padding:14px 20px;border-top:1px solid rgba(255,255,255,.08);font-size:12px;color:rgba(255,255,255,.4);display:flex;align-items:center;justify-content:space-between}
.ripples-stat{font-size:13px;color:rgba(255,255,255,.7)}
.pbar-sep{width:1px;height:20px;background:var(--border);margin:0 4px}
.post-delete{margin-left:auto;background:none;border:none;cursor:pointer;color:#bbb;font-size:20px;padding:4px 8px;line-height:1;border-radius:50%;transition:.15s}.post-delete:hover{background:#f5f5f5;color:#888}
.comments-area{background:#fafafa;border-top:1px solid var(--border);padding:12px 16px}
.cmt-item{display:flex;gap:8px;margin-bottom:10px}
.cmt-av{width:34px;height:34px;min-width:34px;max-width:34px;border-radius:50%;object-fit:cover;flex-shrink:0}
.cmt-bubble{flex:1;min-width:0}.cmt-name{font-size:13px;font-weight:500}.cmt-name a{color:#212121}.cmt-text{font-size:13px;color:#333;margin-top:2px;line-height:1.5}.cmt-time{font-size:11px;color:var(--sub);margin-top:2px}
.cmt-form{display:flex;gap:8px;align-items:center;margin-top:8px}.cmt-form input{flex:1;padding:8px 12px;border:1px solid var(--border);border-radius:20px;font-size:13px;outline:none}
.av32{width:32px;height:32px;border-radius:50%;object-fit:cover}.av40{width:40px;height:40px;border-radius:50%;object-fit:cover}
.av80{width:80px;height:80px;border-radius:50%;object-fit:cover}.av96{width:96px;height:96px;border-radius:50%;object-fit:cover;border:3px solid #fff;box-shadow:0 1px 4px rgba(0,0,0,.3)}
.prof-cover{height:210px;background:linear-gradient(135deg,#3a3a3a,#5a5a5a);position:relative;overflow:hidden}.prof-cover img{width:100%;height:100%;object-fit:cover}
.prof-cover-overlay{position:absolute;inset:0;background:linear-gradient(to bottom,transparent 60%,rgba(0,0,0,.4))}
.prof-head{display:flex;align-items:flex-end;gap:16px;padding:0 24px;position:relative;margin-top:-48px;z-index:2}
.prof-info{padding-bottom:12px;flex:1}.prof-info h2{font-size:24px;font-weight:400}.prof-info .tagline{font-size:13px;color:var(--sub);margin-top:2px}
.prof-actions{padding-bottom:14px;display:flex;gap:8px}
.prof-stats{display:flex;border-top:1px solid var(--border)}.prof-stat{flex:1;text-align:center;padding:12px 8px;border-right:1px solid var(--border)}.prof-stat:last-child{border-right:none}
.prof-stat .n{font-size:20px;font-weight:500}.prof-stat .l{font-size:12px;color:var(--sub)}
.prof-tabs{display:flex;border-bottom:1px solid var(--border);padding:0 16px}
.prof-tab{padding:13px 16px;font-size:13px;font-weight:500;color:var(--sub);border-bottom:3px solid transparent;cursor:pointer;transition:.15s;text-decoration:none;display:block}
.prof-tab:hover{color:#212121;text-decoration:none}.prof-tab.active{color:var(--gblue);border-color:var(--gblue)}
.rsb-section{padding:12px 14px}.rsb-title{font-size:11px;font-weight:700;color:#555;margin-bottom:10px;text-transform:uppercase;letter-spacing:.5px}
.rsb-person{display:flex;align-items:center;gap:8px;margin-bottom:10px}.rsb-person-info{flex:1;min-width:0}
.rsb-person-info .n{font-size:13px;font-weight:500;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.rsb-person-info .t{font-size:11px;color:var(--sub);overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.people-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(148px,1fr));gap:12px}
.people-card{display:flex;flex-direction:column;align-items:center;text-align:center;padding:16px 12px;gap:8px}
.people-av{width:64px;height:64px;border-radius:50%;object-fit:cover;display:block}
.people-info{width:100%;font-size:14px}
.profile-card{padding:0;overflow:hidden}
.profile-cover{height:120px;background:linear-gradient(135deg,var(--gblue),#4ecdc4);position:relative}
.profile-av-wrap{position:relative;padding:0 16px;margin-top:-36px;display:inline-block}
.profile-av{width:72px;height:72px;border-radius:50%;object-fit:cover;border:3px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,.15);display:block}
.profile-info{padding:8px 16px 16px}
.profile-handle{font-size:13px;color:var(--sub);margin-top:2px}
.profile-bio{font-size:13px;color:#444;margin-top:6px;line-height:1.5}
.profile-stats{display:flex;gap:16px;margin-top:10px;font-size:13px;color:var(--sub)}
.profile-stats strong{color:var(--text)}
.profile-actions{margin-top:12px;display:flex;gap:8px}
.pcard{background:#fff;border:1px solid var(--border);border-radius:var(--radius);text-align:center;padding:16px 10px;transition:box-shadow .2s}.pcard:hover{box-shadow:0 2px 8px rgba(0,0,0,.12)}
.pcard .pname{font-size:13px;font-weight:500;margin:8px 0 2px}.pcard .ptag{font-size:11px;color:var(--sub);margin-bottom:10px}
.comm-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:12px}
.comm-card{background:#fff;border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;transition:box-shadow .2s}.comm-card:hover{box-shadow:0 2px 8px rgba(0,0,0,.12)}
.comm-cover-strip{height:72px;display:flex;align-items:center;justify-content:center;font-size:32px;position:relative;overflow:hidden}
.comm-cover-strip img.comm-banner-img{position:absolute;top:0;left:0;right:0;bottom:0;width:100%;height:100%;object-fit:cover;z-index:0}
.comm-icon-overlay{position:relative;z-index:1;text-shadow:0 1px 4px rgba(0,0,0,.3)}
.comm-icon-img-wrap{position:absolute;bottom:-18px;left:50%;transform:translateX(-50%);width:48px;height:48px;z-index:2}
.comm-icon-img-wrap img{width:48px;height:48px;border-radius:50%;object-fit:cover;border:3px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,.22);display:block}
.comm-card-has-icon .comm-body{padding-top:26px}
.comm-detail-icon-wrap{position:absolute;bottom:-32px;left:28px;z-index:3}
.comm-detail-icon-wrap img{width:80px;height:80px;border-radius:50%;object-fit:cover;border:4px solid #fff;box-shadow:0 3px 14px rgba(0,0,0,.25);display:block;background:#fff}
.comm-detail-icon-wrap .comm-icon-fallback{width:80px;height:80px;border-radius:50%;border:4px solid #fff;box-shadow:0 3px 14px rgba(0,0,0,.25);display:flex;align-items:center;justify-content:center;font-size:38px;background:rgba(0,0,0,.18);backdrop-filter:blur(2px)}
.icon-image-upload-area{display:flex;align-items:center;gap:14px;margin-top:8px}
.icon-img-preview{width:60px;height:60px;border-radius:50%;overflow:hidden;border:2px solid var(--border);background:#f5f5f5;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:26px;position:relative}
.icon-img-preview img{width:100%;height:100%;object-fit:cover;display:block}
.icon-upload-btn{display:inline-flex;align-items:center;gap:6px;padding:7px 13px;border:1px solid var(--border);border-radius:3px;background:#fff;font-size:12px;cursor:pointer;transition:.15s;color:#555}
.icon-upload-btn:hover{background:#f5f5f5;border-color:#bbb}
.icon-upload-btn input{display:none}
.comm-body{padding:10px 12px}.comm-name{font-size:14px;font-weight:500;margin-bottom:3px}.comm-members{font-size:12px;color:var(--sub)}
.icon-picker{display:flex;flex-wrap:wrap;gap:5px;margin-top:6px}
.icon-opt{width:34px;height:34px;font-size:18px;display:flex;align-items:center;justify-content:center;border:2px solid transparent;border-radius:6px;cursor:pointer;transition:.15s;background:#f5f5f5}
.icon-opt:hover{background:#e8e8e8}.icon-opt.sel{border-color:var(--gblue);background:#e8f0fe}
.cswatch{width:26px;height:26px;border-radius:50%;cursor:pointer;border:3px solid transparent;transition:.15s;display:inline-block}
.msg-layout{display:grid;grid-template-columns:240px 1fr;gap:16px}
.conv-list-header{padding:14px 16px;font-size:14px;font-weight:500;border-bottom:1px solid var(--border);background:#fafafa}
.conv-item{display:flex;align-items:center;gap:10px;padding:11px 14px;border-bottom:1px solid var(--border);cursor:pointer;transition:background .15s;text-decoration:none}
.conv-item:hover{background:#f5f5f5;text-decoration:none}.conv-item.active{background:#e8f0fe}.conv-name{font-size:13px;font-weight:500;color:#212121}
.chat-header{display:flex;align-items:center;gap:10px;padding:12px 16px;border-bottom:1px solid var(--border);background:#fafafa}
.chat-window{height:400px;overflow-y:auto;padding:16px;display:flex;flex-direction:column;gap:8px}
.bubble{max-width:72%;padding:9px 13px;border-radius:18px;font-size:13px;line-height:1.5}
.bubble-me{align-self:flex-end;background:var(--gblue);color:#fff;border-bottom-right-radius:4px}
.bubble-them{align-self:flex-start;background:#f1f3f4;color:#212121;border-bottom-left-radius:4px}
.bubble-time{font-size:10px;opacity:.7;margin-top:3px}
.chat-input-row{padding:10px;border-top:1px solid var(--border);display:flex;gap:8px}
.chat-input-row input{flex:1;border:1px solid var(--border);border-radius:20px;padding:8px 14px;font-size:13px;outline:none}
.notif-item{display:flex;align-items:center;gap:12px;padding:12px 16px;border-bottom:1px solid var(--border);transition:background .15s}
.notif-item:hover{background:#fafafa}.notif-item.unread{background:#e8f0fe}
.notif-msg{flex:1;font-size:13px;color:#333}.notif-time{font-size:11px;color:var(--sub);white-space:nowrap}
.notif-av{width:40px;height:40px;border-radius:50%;object-fit:cover;flex-shrink:0}
.conv-av{width:36px;height:36px;border-radius:50%;object-fit:cover;flex-shrink:0}
.auth-page{min-height:100vh;background:#f8f9fa;display:flex;align-items:center;justify-content:center;padding:20px}
.hz-card{background:#fff;border-radius:8px;box-shadow:0 2px 10px rgba(0,0,0,.15),0 0 0 1px rgba(0,0,0,.05);width:100%;max-width:448px;padding:48px 48px 36px}
@media(max-width:520px){.hz-card{padding:32px 24px 28px;border-radius:0;min-height:100vh;box-shadow:none}}
.hz-logo-row{margin-bottom:8px}
.hz-logo-text{font-family:'Product Sans','Roboto',sans-serif;font-size:26px;font-weight:700;letter-spacing:-.5px;color:#202124}
.hz-logo-text .hzg{color:#dd4b39}.hz-logo-text .hzz{color:#f4b400}.hz-logo-text .hzo{color:#4285f4}.hz-logo-text .hzo2{color:#0f9d58}.hz-logo-text .hzg2{color:#dd4b39}.hz-logo-text .hzl{color:#4285f4}.hz-logo-text .hze{color:#dd4b39}
.hz-title{font-size:24px;font-weight:400;color:#202124;margin-bottom:8px;line-height:1.33}
.hz-subtitle{font-size:14px;color:#5f6368;margin-bottom:28px}
.hz-input{width:100%;padding:13px 14px;border:1px solid #dadce0;border-radius:4px;font-size:16px;font-family:inherit;outline:none;transition:border-color .15s,box-shadow .15s;color:#202124;background:#fff}
.hz-input:focus{border-color:#1a73e8;box-shadow:0 0 0 2px rgba(26,115,232,.2)}
.hz-input-label{display:block;font-size:11px;font-weight:500;color:#5f6368;text-transform:uppercase;letter-spacing:.6px;margin-bottom:6px}
.hz-form-row{margin-bottom:20px}
.hz-row2{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.hz-next{width:100%;padding:13px;background:#1a73e8;color:#fff;border:none;border-radius:4px;font-size:15px;font-weight:500;font-family:inherit;cursor:pointer;transition:background .15s,box-shadow .15s;margin-top:8px}
.hz-next:hover{background:#1557b0;box-shadow:0 1px 4px rgba(0,0,0,.3)}
.hz-step-footer{display:flex;justify-content:space-between;align-items:center;margin-top:12px}
.hz-err{background:#fce8e6;color:#c5221f;border-radius:4px;padding:10px 12px;font-size:13px;margin-bottom:16px;border-left:3px solid #ea4335}
.hz-notice{background:#e8f0fe;color:#1a73e8;border-radius:4px;padding:14px 16px;font-size:13px;margin-bottom:16px;border-left:3px solid #1a73e8;line-height:1.6}
.hz-name-preview{text-align:center;padding:16px 0 8px}
.hz-avatar-preview{width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg,#c8934a,#9b6a2f);display:flex;align-items:center;justify-content:center;font-size:32px;color:#fff;font-weight:700;font-family:'Product Sans',sans-serif;margin:0 auto 12px}
.hz-name-shown{font-size:18px;font-weight:400;color:#202124}
.hz-email-shown{font-size:13px;color:#5f6368;margin-top:2px}
.hz-progress{height:3px;background:#dadce0;border-radius:2px;margin-bottom:32px;overflow:hidden}
.hz-progress-fill{height:100%;background:#1a73e8;border-radius:2px;transition:width .4s ease}
.tos-agree-box{background:#f8f9fa;border:1px solid #dadce0;border-radius:6px;padding:14px 16px;margin-bottom:20px}
.tos-agree-box .tos-scroll{max-height:160px;overflow-y:auto;font-size:12px;color:#5f6368;line-height:1.65;margin-bottom:12px;padding-right:4px}
.tos-agree-box .tos-scroll::-webkit-scrollbar{width:4px}.tos-agree-box .tos-scroll::-webkit-scrollbar-thumb{background:#dadce0;border-radius:2px}
.tos-agree-label{display:flex;align-items:flex-start;gap:9px;font-size:13px;color:#202124;cursor:pointer;line-height:1.5}
.tos-agree-label input[type=checkbox]{margin-top:2px;accent-color:#1a73e8;width:15px;height:15px;flex-shrink:0;cursor:pointer}
.legal-wrap{max-width:800px;margin:24px auto;padding:0 16px}
.legal-card{background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:40px 48px}
@media(max-width:600px){.legal-card{padding:24px 20px}}
.legal-card h1{font-size:26px;font-weight:400;margin-bottom:6px;color:#202124}
.legal-card .legal-eff{font-size:13px;color:var(--sub);margin-bottom:32px}
.legal-card h3{font-size:15px;font-weight:600;color:#202124;margin:24px 0 8px}
.legal-card p{font-size:14px;line-height:1.75;color:#444;margin-bottom:12px}
.legal-card ul{padding-left:20px;margin-bottom:12px}
.legal-card li{font-size:14px;line-height:1.75;color:#444;margin-bottom:4px}
.legal-card a{color:var(--gblue)}
.legal-tabs{display:flex;gap:0;border-bottom:2px solid var(--border);margin-bottom:32px}
.legal-tab{padding:10px 20px;font-size:14px;font-weight:500;color:var(--sub);border-bottom:3px solid transparent;margin-bottom:-2px;text-decoration:none;transition:color .15s}
.legal-tab:hover{color:var(--text);text-decoration:none}.legal-tab.active{color:var(--gblue);border-color:var(--gblue)}
#site-footer{text-align:center;padding:24px 16px;font-size:12px;color:#aaa;line-height:2}
#site-footer a{color:#aaa}#site-footer a:hover{color:var(--gblue)}
.oss-badge{display:inline-flex;align-items:center;gap:5px;background:#24292e;color:#fff;border-radius:4px;padding:3px 9px;font-size:11px;font-weight:600;text-decoration:none;vertical-align:middle}
.oss-badge:hover{background:#444;text-decoration:none;color:#fff}
.about-hero{background:linear-gradient(135deg,#2d2d2d,#1a1a1a);color:#fff;text-align:center;padding:52px 32px 44px;border-radius:var(--radius) var(--radius) 0 0}
.about-hero .site-logo-big{font-family:'Product Sans','Roboto',sans-serif;font-size:48px;font-weight:700;letter-spacing:-1px;margin-bottom:10px}
.about-hero .site-logo-big .hz{color:var(--hazel-light)}.about-hero .site-logo-big .plus{color:#fff}
.about-hero p{font-size:15px;color:rgba(255,255,255,.65);max-width:480px;margin:0 auto;line-height:1.7}
.about-faq{padding:32px 40px}
@media(max-width:600px){.about-faq{padding:24px 20px}}
.faq-item{margin-bottom:28px;padding-bottom:28px;border-bottom:1px solid var(--border)}
.faq-item:last-child{border-bottom:none;margin-bottom:0;padding-bottom:0}
.faq-q{font-size:16px;font-weight:600;color:#202124;margin-bottom:8px;display:flex;align-items:flex-start;gap:10px}
.faq-q::before{content:'Q';display:inline-flex;align-items:center;justify-content:center;width:22px;height:22px;min-width:22px;background:var(--gblue);color:#fff;border-radius:50%;font-size:11px;font-weight:700;margin-top:1px}
.faq-a{font-size:14px;color:#444;line-height:1.75;padding-left:32px}
.about-meta{display:flex;justify-content:center;gap:8px;flex-wrap:wrap;padding:0 40px 32px}
@media(max-width:600px){.about-meta{padding:0 20px 24px}}
.stat-row{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:16px}
.sbox{background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:20px 16px;text-align:center}
.sbox .n{font-size:28px;font-weight:500;color:var(--gblue)}.sbox .l{font-size:12px;color:var(--sub);margin-top:2px}
.gtable{width:100%;border-collapse:collapse}.gtable th{background:#f5f5f5;padding:10px 12px;text-align:left;font-size:12px;font-weight:700;color:var(--sub);text-transform:uppercase;letter-spacing:.4px;border-bottom:2px solid var(--border)}
.gtable td{padding:10px 12px;border-bottom:1px solid #f0f0f0;font-size:13px}.gtable tr:hover td{background:#fafafa}
.role-badge{display:inline-block;padding:2px 8px;border-radius:10px;font-size:11px;font-weight:700}.rb-admin{background:#fce8e6;color:var(--gred)}.rb-user{background:#e8f0fe;color:var(--gblue)}
.rb-suspended{background:#fce8e6;color:#b71c1c;font-size:10px;padding:2px 7px;border-radius:10px;display:inline-block;font-weight:700;margin-left:4px}
.ea-panel{background:linear-gradient(135deg,#fffde7,#fff8e1);border:1px solid #f4b400;border-radius:var(--radius);padding:18px 20px;margin-bottom:16px}
.ea-panel-on{background:linear-gradient(135deg,#fff8e1,#fff3cd);border-color:#e09000}
.maint-panel{background:linear-gradient(135deg,#fce8e6,#fff0ee);border:1px solid #dd4b39;border-radius:var(--radius);padding:18px 20px;margin-bottom:16px}
.maint-panel-on{background:linear-gradient(135deg,#fce8e6,#ffe0dc);border-color:#c53929}
.ea-header{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap}
.ea-title{font-size:15px;font-weight:600;color:#7a5c00;display:flex;align-items:center;gap:8px}
.maint-title{font-size:15px;font-weight:600;color:#7a1a0a;display:flex;align-items:center;gap:8px}
.ea-desc{font-size:12px;color:#9a7000;margin-top:6px;line-height:1.6}
.maint-desc{font-size:12px;color:#9a2a1a;margin-top:6px;line-height:1.6}
.ea-status{display:inline-flex;align-items:center;font-size:12px;font-weight:700;padding:3px 10px;border-radius:10px}
.ea-on{background:#f4b400;color:#000}.ea-off{background:#e0e0e0;color:#555}
.maint-on{background:#dd4b39;color:#fff}.maint-off{background:#e0e0e0;color:#555}
.admin-success{background:#e6f4ea;color:#137333;border-radius:2px;padding:10px 12px;font-size:13px;margin-bottom:14px;border-left:3px solid var(--ggreen)}
.admin-err{background:#fce8e6;color:#c5221f;border-radius:2px;padding:10px 12px;font-size:13px;margin-bottom:14px;border-left:3px solid var(--gred)}
.admin-tabs{display:flex;gap:4px;margin-bottom:16px;border-bottom:2px solid var(--border);padding-bottom:0}
.admin-tab{padding:8px 18px;font-size:13px;font-weight:500;color:var(--sub);text-decoration:none;border-bottom:2px solid transparent;margin-bottom:-2px;transition:.15s}
.admin-tab:hover{color:var(--text)}.admin-tab.active{color:var(--gblue);border-bottom-color:var(--gblue)}
.admin-table{width:100%;border-collapse:collapse;font-size:13px}
.admin-table th{text-align:left;padding:8px 10px;font-size:11px;font-weight:600;color:var(--sub);text-transform:uppercase;letter-spacing:.4px;border-bottom:2px solid var(--border)}
.admin-table td{padding:10px 10px;border-bottom:1px solid var(--border);vertical-align:middle}
.admin-table tr:last-child td{border-bottom:none}
.admin-table tr:hover td{background:#fafafa}
.comm-header{display:flex;align-items:flex-start;gap:14px;padding:16px}
.comm-icon{width:48px;height:48px;border-radius:50%;background:var(--gblue);color:#fff;display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:700;flex-shrink:0}
.comm-icon-lg{width:64px;height:64px;font-size:26px;flex-shrink:0}
.form-group{margin-bottom:16px}.form-group label{display:block;font-size:12px;font-weight:500;color:var(--sub);margin-bottom:5px;text-transform:uppercase;letter-spacing:.4px}
.form-group input,.form-group textarea,.form-group select{width:100%;padding:10px 12px;border:1px solid #ddd;border-radius:2px;font-size:14px;outline:none;transition:.2s;font-family:inherit;box-sizing:border-box;background:#fff;color:#212121}
.form-group textarea{resize:vertical}.form-group input:focus,.form-group textarea:focus{border-color:var(--gblue);box-shadow:0 0 0 2px rgba(66,133,244,.15)}
.empty-state{text-align:center;padding:40px 20px;color:var(--sub)}.empty-state svg{width:52px;height:52px;fill:var(--border);margin:0 auto 12px}.empty-state p{font-size:14px}
.page-head{display:flex;justify-content:space-between;align-items:center;margin-bottom:14px}.page-head h2{font-size:20px;font-weight:400;color:#333}
.circle-chip{border:1px solid var(--border);border-radius:20px;padding:5px 12px;font-size:12px;display:inline-flex;align-items:center;gap:6px;background:#f5f5f5;text-decoration:none;color:#333;transition:.15s}
.circle-chip:hover{background:#e8e8e8;text-decoration:none}
.guest-banner{background:linear-gradient(135deg,#2d2d2d,#3d3d3d);color:#fff;text-align:center;padding:40px 20px;margin-bottom:16px;border-radius:var(--radius)}
.guest-banner h2{font-size:26px;font-weight:300;margin-bottom:8px}.guest-banner p{font-size:14px;color:rgba(255,255,255,.7);margin-bottom:20px}
.guest-banner-btns{display:flex;gap:10px;justify-content:center}
.cfgrid{display:grid;grid-template-columns:1fr 1fr;gap:20px}
@media(max-width:600px){.cfgrid{grid-template-columns:1fr}}
.form-row{margin-bottom:18px}.form-row label{display:block;font-size:12px;font-weight:500;color:var(--sub);margin-bottom:5px;text-transform:uppercase;letter-spacing:.4px}
.form-row input,.form-row textarea,.form-row select{width:100%;padding:10px 12px;border:1px solid #ddd;border-radius:2px;font-size:14px;outline:none;transition:.2s;font-family:inherit}
.form-row textarea{resize:vertical}.form-row input:focus,.form-row textarea:focus,.form-row select:focus{border-color:var(--gblue);box-shadow:0 0 0 2px rgba(66,133,244,.15)}
.banner-preview-box{position:relative;height:130px;border-radius:4px;overflow:hidden;margin-bottom:8px;border:2px dashed var(--border);background:#f5f5f5}
.banner-preview-box img{width:100%;height:100%;object-fit:cover}
.banner-preview-bg{width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:40px;transition:background .2s}
.banner-upload-label{display:inline-flex;align-items:center;gap:6px;padding:7px 14px;background:#f5f5f5;border:1px solid #ddd;border-radius:2px;font-size:12px;font-weight:500;color:#444;cursor:pointer;transition:.15s}
.banner-upload-label:hover{background:#e8e8e8;color:#212121}
.alert-success{background:#e6f4ea;color:#137333;border-radius:2px;padding:12px 16px;font-size:13px;margin-bottom:14px;border-left:3px solid var(--ggreen);display:flex;align-items:center;gap:8px}
.suspend-form-wrap{background:#fff8f8;border:1px solid #fecaca;border-radius:4px;padding:14px 16px;margin-top:8px;display:none}
.suspend-form-wrap.open{display:block}
.susp-banner{background:linear-gradient(135deg,#fce8e6,#fff0ee);border:1px solid #dd4b39;border-radius:4px;padding:10px 14px;margin-bottom:14px;font-size:13px;color:#c5221f;display:flex;align-items:flex-start;gap:8px}
.susp-banner svg{flex-shrink:0;margin-top:1px;fill:#dd4b39}
.suspended-page{min-height:100vh;background:#f8f9fa;display:flex;align-items:center;justify-content:center;padding:20px}
.suspended-card{background:#fff;border-radius:8px;box-shadow:0 2px 10px rgba(0,0,0,.15);width:100%;max-width:480px;padding:48px;text-align:center}
.suspended-icon{font-size:64px;margin-bottom:20px}
.suspended-card h1{font-size:24px;font-weight:400;color:#202124;margin-bottom:10px}
.suspended-card p{font-size:14px;color:#5f6368;line-height:1.7;margin-bottom:8px}
.suspended-reason{background:#fce8e6;border:1px solid #f5c6c0;border-radius:4px;padding:14px 16px;font-size:13px;color:#c5221f;margin:16px 0;text-align:left;line-height:1.6}
.suspended-until{background:#e8f0fe;border-radius:4px;padding:10px 14px;font-size:13px;color:#1a73e8;margin-bottom:20px}
#circles-page{display:flex;height:calc(100vh - 99px);overflow:hidden;background:#fff}
#circles-people-tray{width:270px;flex-shrink:0;background:#f9f9f9;border-right:1px solid #e8e8e8;display:flex;flex-direction:column;overflow:hidden}
#circles-people-tray .tray-header{padding:13px 15px 10px;border-bottom:1px solid #e8e8e8;background:#f1f1f1}
#circles-people-tray .tray-header h3{font-size:12px;font-weight:700;color:#666;text-transform:uppercase;letter-spacing:.5px}
#circles-people-tray .tray-header p{font-size:11px;color:#aaa;margin-top:3px;line-height:1.5}
#circles-people-search{padding:8px 10px;border-bottom:1px solid #e8e8e8;background:#f5f5f5}
#circles-people-search input{width:100%;padding:6px 10px 6px 28px;border:1px solid #ddd;border-radius:12px;font-size:12px;outline:none;background:#fff;color:#333;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='13' height='13' viewBox='0 0 24 24' fill='%23bbb'%3E%3Cpath d='M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:8px center}
#circles-people-list{flex:1;overflow-y:auto;padding:4px 0}
#circles-people-list::-webkit-scrollbar{width:5px}
#circles-people-list::-webkit-scrollbar-thumb{background:#ddd;border-radius:3px}
.circle-person-item{display:flex;align-items:center;gap:9px;padding:8px 12px;cursor:grab;user-select:none;border-bottom:1px solid #ede9e2;transition:background .1s}
.circle-person-item:hover{background:#e8e4dc}
.circle-person-item.dragging{opacity:.35}
.circle-person-item .person-av{width:36px;height:36px;border-radius:50%;object-fit:cover;flex-shrink:0;pointer-events:none;border:2px solid #fff;box-shadow:0 1px 3px rgba(0,0,0,.15)}
.circle-person-item .person-info{flex:1;min-width:0;pointer-events:none}
.circle-person-item .person-name{font-size:12px;font-weight:500;color:#333;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.circle-person-item .person-circles{font-size:10px;color:#aaa;margin-top:1px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.circle-person-item .drag-hint{font-size:14px;color:#bbb;pointer-events:none;flex-shrink:0}
#circles-canvas-wrap{flex:1;overflow:auto;display:flex;flex-direction:column;background:#fff}
#circles-canvas-toolbar{padding:9px 18px;background:#f5f5f5;border-bottom:1px solid #e8e8e8;display:flex;align-items:center;gap:10px;flex-wrap:wrap;flex-shrink:0}
#circles-canvas-toolbar h2{font-size:12px;font-weight:700;color:#999;text-transform:uppercase;letter-spacing:.6px;flex:1}
#circles-canvas{flex:1;position:relative;padding:30px 20px 60px;min-height:520px;background:#fff}
.circle-bubble-wrap{position:absolute;cursor:default}
.circle-outer-ring{position:absolute;border-radius:50%;border:1.5px solid #c9c9c9;background:#eeebe5;pointer-events:none;transition:border-color .18s;box-sizing:border-box}
.circle-bubble-wrap.drag-over .circle-outer-ring{border-color:var(--circ-color,#7baee8);border-width:3px}
.circle-inner{position:absolute;border-radius:50%;display:flex;flex-direction:column;align-items:center;justify-content:center;cursor:pointer;transition:transform .15s;box-shadow:inset 0 -3px 10px rgba(0,0,0,.07)}
.circle-inner:hover{transform:scale(1.035)}
.circle-bubble-wrap.drag-over .circle-inner{transform:scale(1.05)}
.circle-name-label{font-size:18px;font-weight:300;color:rgba(255,255,255,.97);text-align:center;line-height:1.3;padding:0 16px;pointer-events:none;font-family:'Roboto',sans-serif;letter-spacing:-.2px;word-break:break-word}
.circle-count-label{font-size:36px;font-weight:300;color:rgba(255,255,255,.9);pointer-events:none;line-height:1;margin-top:6px;font-family:'Roboto',sans-serif}
.circle-drop-hint{position:absolute;bottom:14px;font-size:10px;font-weight:600;letter-spacing:.4px;text-transform:uppercase;color:rgba(255,255,255,.95);background:rgba(0,0,0,.28);border-radius:8px;padding:2px 9px;opacity:0;pointer-events:none;transition:opacity .15s}
.circle-bubble-wrap.drag-over .circle-drop-hint{opacity:1}
.orbit-av{position:absolute;border-radius:50%;object-fit:cover;border:3px solid #fff;box-shadow:0 1px 5px rgba(0,0,0,.22);transition:transform .15s,box-shadow .15s;pointer-events:auto;cursor:pointer}
.orbit-av:hover{transform:scale(1.22) !important;box-shadow:0 3px 10px rgba(0,0,0,.32);z-index:10}
.new-circle-zone{border-radius:50%;border:2px dashed #ccc;background:transparent;display:flex;flex-direction:column;align-items:center;justify-content:center;cursor:pointer;transition:border-color .18s,background .18s;color:#bbb;text-align:center}
.new-circle-zone:hover,.new-circle-zone.drag-over{border-color:#7baee8;background:rgba(123,174,232,.07);color:#5a90c8}
.new-circle-zone .plus-icon{font-size:40px;line-height:1;margin-bottom:6px}
.new-circle-zone .ncz-label{font-size:12px;font-weight:500;color:inherit;line-height:1.4}
#circle-detail-panel{position:fixed;top:0;right:-390px;bottom:0;width:370px;background:#fff;border-left:1px solid #ddd;box-shadow:-3px 0 18px rgba(0,0,0,.13);z-index:600;transition:right .28s cubic-bezier(.4,0,.2,1);display:flex;flex-direction:column;overflow:hidden}
#circle-detail-panel.open{right:0}
.cdp-header{padding:15px 17px;border-bottom:1px solid #eee;display:flex;align-items:center;gap:11px;background:#fafafa}
.cdp-color-dot{width:13px;height:13px;border-radius:50%;flex-shrink:0;box-shadow:0 1px 3px rgba(0,0,0,.2)}
.cdp-title{font-size:15px;font-weight:500;flex:1;color:#333}
.cdp-close{background:none;border:none;cursor:pointer;color:#bbb;font-size:22px;line-height:1;padding:0 2px}
.cdp-close:hover{color:#555}
.cdp-members-list{flex:1;overflow-y:auto;padding:6px 0}
.cdp-member-row{display:flex;align-items:center;gap:10px;padding:9px 16px;transition:background .1s}
.cdp-member-row:hover{background:#fafafa}
.cdp-member-info{flex:1;min-width:0}
.cdp-member-name{font-size:13px;font-weight:500;color:#333}
.cdp-member-tag{font-size:11px;color:#999;margin-top:1px}
.cdp-remove-btn{background:none;border:1px solid #e0e0e0;border-radius:2px;color:#bbb;font-size:11px;padding:3px 9px;cursor:pointer;transition:.12s;white-space:nowrap}
.cdp-remove-btn:hover{background:#fce8e6;border-color:#dd4b39;color:#dd4b39}
.cdp-footer{padding:13px 16px;border-top:1px solid #eee;background:#fafafa}
.cdp-edit-row{display:flex;gap:7px;margin-bottom:10px}
.cdp-edit-row input[type=text]{flex:1;border:1px solid #ddd;border-radius:2px;padding:7px 10px;font-size:13px;outline:none}
.cdp-edit-row input[type=text]:focus{border-color:#7baee8}
.cdp-color-row{display:flex;flex-wrap:wrap;gap:6px;margin-bottom:11px}
.cdp-cswatch{width:22px;height:22px;border-radius:50%;cursor:pointer;border:2px solid transparent;transition:.12s}
.cdp-cswatch.active{border-color:#fff;box-shadow:0 0 0 2px #555}
.cdp-danger-row{display:flex;justify-content:flex-end}
.hz-toast{position:fixed;bottom:26px;left:50%;transform:translateX(-50%) translateY(70px);background:#3c3c3c;color:#fff;border-radius:3px;padding:10px 22px;font-size:13px;font-weight:500;box-shadow:0 4px 14px rgba(0,0,0,.28);z-index:9999;transition:transform .28s cubic-bezier(.4,0,.2,1),opacity .28s;opacity:0;white-space:nowrap;pointer-events:none}
.hz-toast.show{transform:translateX(-50%) translateY(0);opacity:1}
.hz-modal-bg{position:fixed;inset:0;background:rgba(0,0,0,.42);z-index:800;display:flex;align-items:center;justify-content:center;opacity:0;pointer-events:none;transition:opacity .2s}
.hz-modal-bg.open{opacity:1;pointer-events:auto}
.hz-modal{background:#fff;border-radius:5px;box-shadow:0 8px 30px rgba(0,0,0,.22);padding:28px 28px 22px;width:360px;transform:translateY(14px) scale(.97);transition:transform .2s}
.hz-modal-bg.open .hz-modal{transform:none}
.hz-modal h3{font-size:17px;font-weight:400;margin-bottom:18px;color:#333}
.hz-modal .form-row{margin-bottom:14px}
.hz-modal .form-row label{font-size:11px;font-weight:600;color:#999;text-transform:uppercase;letter-spacing:.5px;display:block;margin-bottom:5px}
.hz-modal .form-row input{width:100%;padding:9px 11px;border:1px solid #ddd;border-radius:3px;font-size:14px;outline:none}
.hz-modal .form-row input:focus{border-color:#7baee8}
.hz-modal-footer{display:flex;justify-content:flex-end;gap:8px;margin-top:18px}
</style>
</head>
<body>
<?php if (!in_array($page, ['login','register','suspended'])): ?>
<div id="topbar">
  <a href="?page=home" class="topbar-logo">
    <span class="hz">Hazel</span><span class="plus">+</span>
    <?php if ($maintenanceMode): ?>
      <span class="maint-indicator">&#128737; MAINTENANCE</span>
    <?php elseif ($earlyAccess): ?>
      <span class="ea-indicator">&#9889; EARLY ACCESS</span>
    <?php endif; ?>
  </a>
  <div class="topbar-search">
    <svg class="si" viewBox="0 0 24 24"><path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
    <input type="text" placeholder="Search Hazel+">
  </div>
  <div class="topbar-spacer"></div>
  <?php if ($u): ?>
    <a href="?page=home" class="topbar-btn"><svg viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg></a>
    <a href="?page=notifications" class="topbar-btn">
      <svg viewBox="0 0 24 24"><path d="M12 22c1.1 0 2-.9 2-2h-4a2 2 0 0 0 2 2zm6-6V11c0-3.07-1.63-5.64-4.5-6.32V4a1.5 1.5 0 1 0-3 0v.68C7.64 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/></svg>
      <?php if ($notifCount > 0): ?><span class="tnb"><?= $notifCount ?></span><?php endif; ?>
    </a>
    <a href="?page=messages" class="topbar-btn">
      <svg viewBox="0 0 24 24"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg>
      <?php if ($msgCount > 0): ?><span class="tnb"><?= $msgCount ?></span><?php endif; ?>
    </a>
    <?php if ($u['role'] === 'admin'): ?>
      <a href="?page=admin" class="topbar-btn"><svg viewBox="0 0 24 24"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/></svg></a>
    <?php endif; ?>
    <a href="?page=profile&id=<?= $u['id'] ?>" style="text-decoration:none;display:flex;align-items:center;margin-left:4px">
      <img src="<?= avatarSrc($u['avatar'], $u['display_name']) ?>" class="topbar-avatar">
    </a>
    <form method="post" style="margin:0">
      <input type="hidden" name="action" value="logout">
      <button class="topbar-signout">Sign out</button>
    </form>
  <?php else: ?>
    <a href="?page=login" class="topbar-signout" style="display:inline-block;text-decoration:none">Sign in</a>
    <?php if (!$earlyAccess): ?>
      <a href="?page=register" class="gbtn gbtn-red gbtn-sm" style="margin-left:6px">Join</a>
    <?php endif; ?>
  <?php endif; ?>
</div>

<?php if ($u && !$isLegalPage): ?>
<div id="subnav">
  <a href="?page=home"        class="subnav-tab <?= $page==='home'?'active':'' ?>"><svg viewBox="0 0 24 24"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>Stream</a>
  <a href="?page=explore"     class="subnav-tab <?= $page==='explore'?'active':'' ?>"><svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/></svg>Explore</a>
  <a href="?page=people"      class="subnav-tab <?= $page==='people'?'active':'' ?>"><svg viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>People</a>
  <a href="?page=communities" class="subnav-tab <?= ($page==='communities'||$page==='community')?'active':'' ?>"><svg viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/></svg>Communities</a>
  <a href="?page=circles"     class="subnav-tab <?= $page==='circles'?'active':'' ?>"><svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm0-14c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4z"/></svg>Circles</a>
</div>
<?php endif; ?>
<?php endif; ?>
<?php
// =================== PAGE ROUTING ===================

if ($page === 'suspended'):
?>
<div class="suspended-page">
  <div class="suspended-card">
    <div class="suspended-icon">🚫</div>
    <h1>Your account has been suspended</h1>
    <p>You can't access <?= SITE_NAME ?> right now.</p>
    <?php if ($u && !empty($u['suspend_reason'])): ?>
    <div class="suspended-reason"><strong>Reason:</strong><br><?= h($u['suspend_reason']) ?></div>
    <?php endif; ?>
    <?php if ($u && !empty($u['suspended_until'])): ?>
    <div class="suspended-until">&#128336; Suspension lifts: <strong><?= date('F j, Y \a\t g:i A', strtotime($u['suspended_until'])) ?></strong></div>
    <?php else: ?>
    <p style="color:#c5221f;font-size:13px;margin-bottom:20px">This suspension is <strong>permanent</strong>.</p>
    <?php endif; ?>
    <p style="font-size:13px;color:#999;margin-bottom:24px">
      Think this is a mistake? Email <a href="mailto:<?= LEGAL_CONTACT ?>"><?= LEGAL_CONTACT ?></a>.
    </p>
    <form method="post" style="margin:0">
      <input type="hidden" name="action" value="logout">
      <button class="gbtn gbtn-outline" style="margin:0 auto">Sign out</button>
    </form>
  </div>
</div>

<?php elseif ($page === 'about'): ?>
<div class="legal-wrap">
  <div class="legal-card" style="padding:0;overflow:hidden">
    <div class="about-hero">
      <div class="site-logo-big"><span class="hz">Hazel</span><span class="plus">+</span></div>
      <p>A revival of the best parts of Google+, built by one teenager who wouldn't let it die.</p>
    </div>
    <div class="about-faq">
      <div class="faq-item">
        <div class="faq-q">What even is Hazel+?</div>
        <div class="faq-a">Hazel+ is a revival of Google+, the social network Google killed in 2019. I was 14 when I started building it because I missed everything that made Google+ great — the Circles, the Communities, the clean design. So I rebuilt it from scratch in a single PHP file.</div>
      </div>
      <div class="faq-item">
        <div class="faq-q">Is it open source?</div>
        <div class="faq-a">Yes! As of v2.0.0, Hazel+ is open source under the MIT license. You can find the source, report bugs, or submit a pull request on <a href="<?= GITHUB_URL ?>" target="_blank">GitHub</a>. Contributions welcome.</div>
      </div>
      <div class="faq-item">
        <div class="faq-q">Does Hazel+ have ads?</div>
        <div class="faq-a">No. There are no ads, no trackers, no analytics. The whole point is a clean social experience — ads were never part of that.</div>
      </div>
      <div class="faq-item">
        <div class="faq-q">How can I support the project?</div>
        <div class="faq-a">Star the repo on GitHub, tell your friends, or submit a pull request if you find a bug or have a feature idea. That's genuinely all I need.</div>
      </div>
    </div>
    <div class="about-meta">
      <a href="<?= GITHUB_URL ?>" target="_blank" class="oss-badge">&#128196; View on GitHub</a>
      <a href="?page=tos" class="circle-chip">Terms of Service</a>
      <a href="?page=privacy" class="circle-chip">Privacy Policy</a>
      <a href="?page=guidelines" class="circle-chip">Community Guidelines</a>
      <span class="circle-chip" style="cursor:default;color:var(--sub)"><?= SITE_NAME ?> v<?= VERSION ?> &middot; MIT License</span>
    </div>
  </div>
</div>

<?php elseif ($page === 'tos'): ?>
<div class="legal-wrap"><div class="legal-card">
  <h1>Terms of Service</h1>
  <p class="legal-eff">Effective: <?= LEGAL_EFFECTIVE ?></p>
  <div class="legal-tabs">
    <a href="?page=tos"        class="legal-tab active">Terms of Service</a>
    <a href="?page=privacy"    class="legal-tab">Privacy Policy</a>
    <a href="?page=guidelines" class="legal-tab">Community Guidelines</a>
  </div>
  <?= getTosHtml() ?>
</div></div>

<?php elseif ($page === 'privacy'): ?>
<div class="legal-wrap"><div class="legal-card">
  <h1>Privacy Policy</h1>
  <p class="legal-eff">Effective: <?= LEGAL_EFFECTIVE ?></p>
  <div class="legal-tabs">
    <a href="?page=tos"        class="legal-tab">Terms of Service</a>
    <a href="?page=privacy"    class="legal-tab active">Privacy Policy</a>
    <a href="?page=guidelines" class="legal-tab">Community Guidelines</a>
  </div>
  <?= getPrivacyHtml() ?>
</div></div>

<?php elseif ($page === 'guidelines'): ?>
<div class="legal-wrap"><div class="legal-card">
  <h1>Community Guidelines</h1>
  <p class="legal-eff">Effective: <?= LEGAL_EFFECTIVE ?></p>
  <div class="legal-tabs">
    <a href="?page=tos"        class="legal-tab">Terms of Service</a>
    <a href="?page=privacy"    class="legal-tab">Privacy Policy</a>
    <a href="?page=guidelines" class="legal-tab active">Community Guidelines</a>
  </div>
  <?= getGuidelinesHtml() ?>
</div></div>

<?php elseif ($page === 'login'): ?>
<div class="auth-page"><div class="hz-card">
  <div class="hz-logo-row"><div class="hz-logo-text"><span class="hzg">H</span><span class="hzz">a</span><span class="hzo">z</span><span class="hzo2">o</span><span class="hzg2">o</span><span class="hzl">g</span><span class="hze">l</span><span style="color:#0f9d58">e</span></div></div>
  <h1 class="hz-title">Sign in</h1>
  <p class="hz-subtitle">to continue to <strong><?= SITE_NAME ?></strong></p>
  <?php if ($error): ?><div class="hz-err"><?= h($error) ?></div><?php endif; ?>
  <?php if ($maintenanceMode): ?><div class="hz-notice" style="background:#fce8e6;color:#c5221f;border-color:#dd4b39">&#128737; <strong>Maintenance mode is on.</strong><br>Only admins can sign in right now.</div><?php endif; ?>
  <?php if ($earlyAccess && !$maintenanceMode): ?><div class="hz-notice">&#9889; <strong>Early Access Mode.</strong><br>New registrations are closed for now.</div><?php endif; ?>
  <form method="post">
    <input type="hidden" name="action" value="login">
    <div class="hz-form-row"><label class="hz-input-label">Email or Username</label><input class="hz-input" type="text" name="email" autofocus required></div>
    <div class="hz-form-row"><label class="hz-input-label">Password</label><input class="hz-input" type="password" name="password" required></div>
    <div style="display:flex;justify-content:space-between;align-items:center;margin-top:4px">
      <?php if (!$earlyAccess && !$maintenanceMode): ?>
        <a href="?page=register" style="color:#1a73e8;font-size:14px;font-weight:500">Create account</a>
      <?php else: ?>
        <span style="font-size:13px;color:#9a7000"><?= $maintenanceMode ? 'Maintenance mode' : 'Invite-only' ?></span>
      <?php endif; ?>
      <button class="hz-next" style="width:auto;padding:10px 24px;font-size:14px">Next</button>
    </div>
  </form>
</div></div>

<?php elseif ($page === 'register'):
  if ($earlyAccess): ?>
<div class="auth-page"><div class="hz-card">
  <div style="text-align:center;padding:10px 0 20px">
    <div style="font-size:48px;margin-bottom:14px">&#9889;</div>
    <h2 style="font-size:20px;font-weight:400;margin-bottom:10px">Registrations are closed</h2>
    <p style="font-size:14px;color:#5f6368;line-height:1.6">This site is in <strong>Early Access Mode</strong>. New accounts are created by the admin only.</p>
  </div>
  <a href="?page=login" class="hz-next" style="display:block;text-align:center;text-decoration:none">Back to Sign In</a>
</div></div>
<?php else:
  $step = (int)($_GET['step'] ?? 1);
  if ($_SERVER['REQUEST_METHOD'] === 'GET' && $step === 1) unset($_SESSION['reg_name'], $_SESSION['reg_un'], $_SESSION['reg_em']);
  $progPct = ['1' => '16%', '2' => '50%', '3' => '83%'][$step] ?? '16%';
  $regName = h($_SESSION['reg_name'] ?? '');
  $regUn   = h($_SESSION['reg_un']   ?? '');
  $regEm   = h($_SESSION['reg_em']   ?? '');
?>
<div class="auth-page"><div class="hz-card">
  <div class="hz-logo-row"><div class="hz-logo-text"><span class="hzg">H</span><span class="hzz">a</span><span class="hzo">z</span><span class="hzo2">o</span><span class="hzg2">o</span><span class="hzl">g</span><span class="hze">l</span><span style="color:#0f9d58">e</span></div></div>
  <div class="hz-progress"><div class="hz-progress-fill" style="width:<?= $progPct ?>"></div></div>
  <?php if ($error): ?><div class="hz-err"><?= h($error) ?></div><?php endif; ?>

  <?php if ($step === 1): ?>
  <h1 class="hz-title">Create your<br>Hazel+ account</h1>
  <p class="hz-subtitle">What should we call you?</p>
  <form method="post" action="?page=register&step=1_save">
    <input type="hidden" name="action" value="reg_step1">
    <div class="hz-row2">
      <div class="hz-form-row"><label class="hz-input-label">First name</label><input class="hz-input" type="text" name="first_name" value="<?= $regName ? explode(' ', $regName)[0] : '' ?>" autofocus required></div>
      <div class="hz-form-row"><label class="hz-input-label">Last name <span style="font-weight:400;text-transform:none;letter-spacing:0">(optional)</span></label><input class="hz-input" type="text" name="last_name" value="<?= ($regName && strpos($regName, ' ') !== false) ? substr($regName, strpos($regName, ' ') + 1) : '' ?>"></div>
    </div>
    <button class="hz-next">Next</button>
    <div style="text-align:center;margin-top:20px;font-size:14px;color:#5f6368">Already have an account? <a href="?page=login" style="color:#1a73e8;font-weight:500">Sign in</a></div>
  </form>

  <?php elseif ($step === 2):
    $parts = explode(' ', trim($_SESSION['reg_name'] ?? ''));
    $initials = strtoupper(substr($parts[0] ?? '', 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : ''));
  ?>
  <div class="hz-name-preview"><div class="hz-avatar-preview"><?= $initials ?: '?' ?></div><div class="hz-name-shown"><?= $regName ?></div></div>
  <h1 class="hz-title" style="font-size:20px;margin-top:16px">Pick a username &amp; email</h1>
  <form method="post" action="?page=register&step=2_save">
    <input type="hidden" name="action" value="reg_step2">
    <div class="hz-form-row"><label class="hz-input-label">Username</label><input class="hz-input" type="text" name="username" value="<?= $regUn ?>" placeholder="letters, numbers, underscores" pattern="[a-zA-Z0-9_]{3,}" required autofocus></div>
    <div class="hz-form-row"><label class="hz-input-label">Email address</label><input class="hz-input" type="email" name="email" value="<?= $regEm ?>" required></div>
    <div class="hz-step-footer"><a href="?page=register&step=1" style="color:#1a73e8;font-size:14px;font-weight:500">&larr; Back</a><button class="hz-next" style="width:auto;padding:10px 28px">Next</button></div>
  </form>

  <?php elseif ($step === 3):
    $parts = explode(' ', trim($_SESSION['reg_name'] ?? ''));
    $initials = strtoupper(substr($parts[0] ?? '', 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : ''));
  ?>
  <div class="hz-name-preview">
    <div class="hz-avatar-preview"><?= $initials ?: '?' ?></div>
    <div class="hz-name-shown"><?= $regName ?></div>
    <div class="hz-email-shown"><?= $regEm ?></div>
  </div>
  <h1 class="hz-title" style="font-size:20px;margin-top:16px">Set a password</h1>
  <p class="hz-subtitle">At least 6 characters. Mix letters and numbers for something stronger.</p>
  <form method="post">
    <input type="hidden" name="action" value="register">
    <input type="hidden" name="display_name" value="<?= $regName ?>">
    <input type="hidden" name="username"     value="<?= $regUn ?>">
    <input type="hidden" name="email"        value="<?= $regEm ?>">
    <div class="hz-form-row"><label class="hz-input-label">Password</label><input class="hz-input" type="password" name="password" minlength="6" required autofocus></div>
    <div class="hz-form-row"><label class="hz-input-label">Confirm password</label><input class="hz-input" type="password" name="password_confirm" minlength="6" required></div>
    <div class="tos-agree-box">
      <div class="tos-scroll">
        <strong style="font-size:12px;color:#202124;display:block;margin-bottom:6px">Quick summary of what you're agreeing to:</strong>
        Use <?= SITE_NAME ?> lawfully and respectfully. You must be at least 13. You keep ownership of content you post but let us display it.
        We don't sell your data or show ads. Accounts that break the rules can be suspended.<br><br>
        <a href="?page=tos"        target="_blank" style="color:#1a73e8">Terms of Service ↗</a> &nbsp;&middot;&nbsp;
        <a href="?page=privacy"    target="_blank" style="color:#1a73e8">Privacy Policy ↗</a> &nbsp;&middot;&nbsp;
        <a href="?page=guidelines" target="_blank" style="color:#1a73e8">Community Guidelines ↗</a>
      </div>
      <label class="tos-agree-label">
        <input type="checkbox" name="agree_tos" value="1" id="agreeChk" onchange="document.getElementById('createBtn').disabled=!this.checked">
        I'm at least 13 and I agree to the
        <a href="?page=tos" target="_blank" style="color:#1a73e8">Terms</a>,
        <a href="?page=privacy" target="_blank" style="color:#1a73e8">Privacy Policy</a>,
        and <a href="?page=guidelines" target="_blank" style="color:#1a73e8">Guidelines</a>.
      </label>
    </div>
    <div class="hz-step-footer">
      <a href="?page=register&step=2" style="color:#1a73e8;font-size:14px;font-weight:500">&larr; Back</a>
      <button id="createBtn" class="hz-next" style="width:auto;padding:10px 28px" disabled>Create Account</button>
    </div>
  </form>
  <?php endif; ?>
</div></div>
<?php endif; ?>

<?php elseif ($page === '__maintenance__'): ?>
<div id="wrap"><div id="stream" style="max-width:620px;margin:0 auto">
  <?= $_maintContent ?? '' ?>
</div></div>

<?php
// ---- CIRCLES PAGE ----
elseif ($page === 'circles'):
  requireLogin();
  $circlesRaw = getDB()->prepare(
      "SELECT c.*, (SELECT COUNT(*) FROM circle_members WHERE circle_id=c.id) AS mc
       FROM circles c WHERE c.user_id=? ORDER BY c.created_at ASC"
  );
  $circlesRaw->execute([$u['id']]);
  $circlesData = $circlesRaw->fetchAll();

  $circleMemberMap = [];
  foreach ($circlesData as $circ) {
      $ms = getDB()->prepare("SELECT member_id FROM circle_members WHERE circle_id=?");
      $ms->execute([$circ['id']]);
      $circleMemberMap[$circ['id']] = array_column($ms->fetchAll(), 'member_id');
  }

  $allPeople = getDB()->prepare("SELECT id,display_name,username,avatar,tagline FROM users WHERE id!=? ORDER BY display_name ASC");
  $allPeople->execute([$u['id']]);
  $allPeople = $allPeople->fetchAll();

  $jsCircles   = json_encode($circlesData);
  $jsMemberMap = json_encode($circleMemberMap);
  $jsPeople    = json_encode($allPeople);
  $jsColors    = json_encode(circleColorPalette());
  $jsMe        = json_encode(['id' => $u['id'], 'display_name' => $u['display_name']]);
?>
<div id="circles-page">
  <div id="circles-people-tray">
    <div class="tray-header">
      <h3>People</h3>
      <p>Drag someone onto a circle to add them, or drop onto the <strong>+</strong> to create a new circle.</p>
    </div>
    <div id="circles-people-search">
      <input type="text" id="peopleSearchInput" placeholder="Search people…" oninput="filterPeople(this.value)">
    </div>
    <div id="circles-people-list">
      <?php foreach ($allPeople as $p):
        $initials = strtoupper(substr($p['display_name'], 0, 1));
        $colors = ['c0392b','2980b9','27ae60','d35400','8e44ad','16a085','e67e22','2c3e50'];
        $col = $colors[ord($initials) % count($colors)];
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="80" height="80"><rect width="80" height="80" fill="#'.$col.'"/><text x="40" y="52" font-size="36" font-family="Arial,sans-serif" font-weight="bold" fill="#fff" text-anchor="middle">'.$initials.'</text></svg>';
        $avSrc = $p['avatar'] ? h($p['avatar']) : 'data:image/svg+xml;base64,' . base64_encode($svg);
      ?>
      <div class="circle-person-item" draggable="true"
           data-uid="<?= $p['id'] ?>" data-name="<?= h($p['display_name']) ?>" data-tag="<?= h($p['tagline'] ?? '') ?>"
           id="person-item-<?= $p['id'] ?>"
           ondragstart="personDragStart(event,<?= $p['id'] ?>)"
           ondragend="personDragEnd(event)">
        <img class="person-av" src="<?= $avSrc ?>" alt="">
        <div class="person-info">
          <div class="person-name"><?= h($p['display_name']) ?></div>
          <div class="person-circles" id="person-circles-label-<?= $p['id'] ?>">Loading…</div>
        </div>
        <span class="drag-hint">&#8942;</span>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <div id="circles-canvas-wrap">
    <div id="circles-canvas-toolbar">
      <h2>&#9711; Your Circles</h2>
      <button class="gbtn gbtn-outline gbtn-sm" onclick="openCreateModal(null)">+ New Circle</button>
      <a href="?page=profile&id=<?= $u['id'] ?>&tab=circles" class="gbtn gbtn-outline gbtn-sm">View as list</a>
    </div>
    <div id="circles-canvas">
      <div class="new-circle-zone" id="new-circle-zone"
           ondragover="newZoneDragOver(event)" ondragleave="newZoneDragLeave(event)" ondrop="newZoneDrop(event)"
           onclick="openCreateModal(null)">
        <div class="plus-icon">+</div>
        <div>Create circle<br><small style="font-weight:400;color:inherit;opacity:.75">or drop a person here</small></div>
      </div>
    </div>
  </div>
</div>

<div id="circle-detail-panel">
  <div class="cdp-header">
    <div class="cdp-color-dot" id="cdp-swatch"></div>
    <div class="cdp-title" id="cdp-title">Circle</div>
    <button class="cdp-close" onclick="closeDetailPanel()">&#215;</button>
  </div>
  <div class="cdp-members-list" id="cdp-members-list"></div>
  <div class="cdp-footer">
    <div class="cdp-edit-row">
      <input type="text" id="cdp-rename-input" placeholder="Circle name…">
      <button class="gbtn gbtn-blue gbtn-sm" onclick="saveRename()">Rename</button>
    </div>
    <div class="cdp-color-row" id="cdp-color-row"></div>
    <div class="cdp-danger-row">
      <button class="gbtn gbtn-sm" style="background:#fce8e6;color:#c5221f;border:1px solid #dd4b39" onclick="deleteCurrentCircle()">Delete circle</button>
    </div>
  </div>
</div>

<div class="hz-modal-bg" id="createModal" onclick="if(event.target===this)closeCreateModal()">
  <div class="hz-modal">
    <h3>Create a new circle</h3>
    <div class="form-row"><label>Circle name</label><input type="text" id="newCircleName" placeholder="e.g. Coworkers" autofocus></div>
    <div class="form-row"><label>Color</label><div style="display:flex;flex-wrap:wrap;gap:7px;margin-top:4px" id="newCircleColors"></div></div>
    <div class="hz-modal-footer">
      <button class="gbtn gbtn-outline" onclick="closeCreateModal()">Cancel</button>
      <button class="gbtn gbtn-blue" onclick="submitCreateCircle()">Create</button>
    </div>
  </div>
</div>
<div class="hz-toast" id="hz-toast"></div>

<script>
var CIRCLES=<?= $jsCircles ?>;
var MEMBER_MAP=<?= $jsMemberMap ?>;
var ALL_PEOPLE=<?= $jsPeople ?>;
var COLORS=<?= $jsColors ?>;
var ME=<?= $jsMe ?>;
var SELF_URL='<?= h($_SERVER['PHP_SELF'] ?? 'index.php') ?>';
var dragPersonId=null, openCircleId=null, newCircleColor=COLORS[0], pendingDropUid=null;

function apiPost(action, params, cb) {
  var body = new URLSearchParams(Object.assign({action: action}, params));
  fetch(SELF_URL, {method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: body.toString()})
    .then(r => r.json()).then(cb).catch(e => console.error(e));
}

function toast(msg) {
  var t = document.getElementById('hz-toast');
  t.textContent = msg; t.classList.add('show');
  clearTimeout(t._timer); t._timer = setTimeout(() => t.classList.remove('show'), 2800);
}

function avatarUrl(person) {
  if (person.avatar) return person.avatar;
  var initials = ((person.display_name||'?')[0]||'?').toUpperCase();
  var colors = ['c0392b','2980b9','27ae60','d35400','8e44ad','16a085','e67e22','2c3e50'];
  var col = colors[initials.charCodeAt(0) % colors.length];
  var svg = '<svg xmlns="http://www.w3.org/2000/svg" width="80" height="80"><rect width="80" height="80" fill="#'+col+'"/><text x="40" y="52" font-size="36" font-family="Arial,sans-serif" font-weight="bold" fill="#fff" text-anchor="middle">'+initials+'</text></svg>';
  return 'data:image/svg+xml;base64,' + btoa(svg);
}
function personById(id)     { return ALL_PEOPLE.find(p => p.id == id) || null; }
function circleById(id)     { return CIRCLES.find(c => c.id == id) || null; }
function circleIndex(id)    { return CIRCLES.findIndex(c => c.id == id); }
function escHtml(str)       { return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
function hexToRgb(hex) {
  var r = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
  return r ? {r:parseInt(r[1],16), g:parseInt(r[2],16), b:parseInt(r[3],16)} : null;
}

var OUTER_D=300, INNER_D=174, AV_D=56, ORBIT_R=300/2, CELL_W=360, CELL_H=360;

function computePositions() {
  var canvas = document.getElementById('circles-canvas');
  var W = Math.max(canvas.offsetWidth || 900, 400);
  var cols = Math.max(2, Math.floor(W / CELL_W));
  var positions = [];
  for (var i = 0; i < CIRCLES.length + 1; i++) {
    var row = Math.floor(i / cols), col = i % cols;
    positions.push({cx: col*CELL_W + CELL_W/2, cy: row*CELL_H + CELL_H/2});
  }
  return positions;
}

function render() {
  var canvas = document.getElementById('circles-canvas');
  var newZone = document.getElementById('new-circle-zone');
  canvas.querySelectorAll('.circle-bubble-wrap').forEach(el => el.remove());
  var positions = computePositions();
  var maxBottom = 0;

  CIRCLES.forEach(function(circ, i) {
    var pos = positions[i];
    var memberIds = MEMBER_MAP[circ.id] || [];
    var members = memberIds.map(mid => personById(mid)).filter(Boolean);
    var pad = AV_D/2 + 4, wrapD = OUTER_D + pad*2;
    var wrapL = pos.cx - wrapD/2, wrapT = pos.cy - wrapD/2;

    var wrap = document.createElement('div');
    wrap.className = 'circle-bubble-wrap';
    wrap.id = 'circle-bubble-' + circ.id;
    wrap.style.cssText = 'left:'+Math.round(wrapL)+'px;top:'+Math.round(wrapT)+'px;width:'+wrapD+'px;height:'+wrapD+'px;';
    wrap.setAttribute('data-cid', circ.id);
    var rgb = hexToRgb(circ.color);
    wrap.style.setProperty('--circ-color', circ.color);
    if (rgb) wrap.style.setProperty('--circ-rgb', rgb.r+','+rgb.g+','+rgb.b);

    wrap.addEventListener('dragover', e => { e.preventDefault(); wrap.classList.add('drag-over'); });
    wrap.addEventListener('dragleave', e => { if (!wrap.contains(e.relatedTarget)) wrap.classList.remove('drag-over'); });
    wrap.addEventListener('drop', e => { e.preventDefault(); wrap.classList.remove('drag-over'); handleDropOnCircle(circ.id); });

    var outer = document.createElement('div');
    outer.className = 'circle-outer-ring';
    outer.style.cssText = 'width:'+OUTER_D+'px;height:'+OUTER_D+'px;left:'+pad+'px;top:'+pad+'px;';
    wrap.appendChild(outer);

    var innerOff = pad + (OUTER_D - INNER_D)/2;
    var inner = document.createElement('div');
    inner.className = 'circle-inner';
    inner.style.cssText = 'width:'+INNER_D+'px;height:'+INNER_D+'px;left:'+Math.round(innerOff)+'px;top:'+Math.round(innerOff)+'px;background:'+circ.color+';';
    inner.innerHTML = '<div class="circle-name-label">'+escHtml(circ.name)+'</div><div class="circle-count-label">'+members.length+'</div><div class="circle-drop-hint">Drop here</div>';
    inner.onclick = e => { if (!e.defaultPrevented) openDetailPanel(circ.id); };
    wrap.appendChild(inner);

    if (members.length > 0) {
      var show = members.slice(0, 8);
      var extra = members.length - show.length;
      var cx = pad + OUTER_D/2, cy = pad + OUTER_D/2;
      show.forEach(function(person, mi) {
        var angle = (2*Math.PI * mi/members.length) - Math.PI/2;
        var ax = cx + ORBIT_R*Math.cos(angle) - AV_D/2;
        var ay = cy + ORBIT_R*Math.sin(angle) - AV_D/2;
        var av = document.createElement('img');
        av.className = 'orbit-av';
        av.src = avatarUrl(person); av.title = person.display_name;
        av.style.cssText = 'width:'+AV_D+'px;height:'+AV_D+'px;left:'+Math.round(ax)+'px;top:'+Math.round(ay)+'px;';
        av.onclick = e => { e.preventDefault(); openDetailPanel(circ.id); };
        wrap.appendChild(av);
      });
      if (extra > 0) {
        var lastAngle = (2*Math.PI * show.length/members.length) - Math.PI/2;
        var bx = cx + ORBIT_R*Math.cos(lastAngle) - AV_D/2;
        var by = cy + ORBIT_R*Math.sin(lastAngle) - AV_D/2;
        var badge = document.createElement('div');
        badge.style.cssText = 'position:absolute;width:'+AV_D+'px;height:'+AV_D+'px;left:'+Math.round(bx)+'px;top:'+Math.round(by)+'px;border-radius:50%;background:rgba(0,0,0,.45);border:2px solid #fff;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:#fff;cursor:pointer;';
        badge.textContent = '+' + extra;
        badge.onclick = e => { e.preventDefault(); openDetailPanel(circ.id); };
        wrap.appendChild(badge);
      }
    }

    canvas.insertBefore(wrap, newZone);
    var bottom = wrapT + wrapD;
    if (bottom > maxBottom) maxBottom = bottom;
  });

  var nzPos = positions[CIRCLES.length];
  var nzHalf = OUTER_D/2;
  if (nzPos) {
    newZone.style.cssText = 'position:absolute;width:'+OUTER_D+'px;height:'+OUTER_D+'px;left:'+Math.round(nzPos.cx-nzHalf)+'px;top:'+Math.round(nzPos.cy-nzHalf)+'px;';
    var nzBottom = nzPos.cy + nzHalf;
    if (nzBottom > maxBottom) maxBottom = nzBottom;
  }
  canvas.style.minHeight = Math.max(520, maxBottom + 60) + 'px';
  updatePersonLabels();
}

function updatePersonLabels() {
  ALL_PEOPLE.forEach(function(person) {
    var el = document.getElementById('person-circles-label-' + person.id);
    if (!el) return;
    var inCircles = CIRCLES.filter(c => (MEMBER_MAP[c.id]||[]).indexOf(person.id) !== -1).map(c => c.name);
    el.textContent = inCircles.length ? inCircles.join(', ') : 'Not in any circle';
  });
}

function personDragStart(e, uid) {
  dragPersonId = uid; e.dataTransfer.effectAllowed = 'copy';
  var el = document.getElementById('person-item-' + uid);
  if (el) el.classList.add('dragging');
}
function personDragEnd(e) {
  dragPersonId = null;
  document.querySelectorAll('.circle-person-item').forEach(el => el.classList.remove('dragging'));
  document.querySelectorAll('.circle-bubble-wrap').forEach(el => el.classList.remove('drag-over'));
  document.getElementById('new-circle-zone').classList.remove('drag-over');
}

function handleDropOnCircle(circleId) {
  if (!dragPersonId) return;
  var uid = dragPersonId, circ = circleById(circleId);
  if (!circ) return;
  if ((MEMBER_MAP[circleId]||[]).indexOf(uid) !== -1) { toast(personById(uid).display_name + ' is already in ' + circ.name); return; }
  apiPost('circles_add_member', {circle_id: circleId, member_id: uid}, function(res) {
    if (res.ok) {
      if (!MEMBER_MAP[circleId]) MEMBER_MAP[circleId] = [];
      MEMBER_MAP[circleId].push(uid);
      var idx = circleIndex(circleId);
      if (idx !== -1) CIRCLES[idx].mc = MEMBER_MAP[circleId].length;
      render();
      if (openCircleId == circleId) openDetailPanel(circleId);
      toast((personById(uid)||{display_name:'Person'}).display_name + ' added to ' + circ.name);
    } else { toast('Could not add: ' + (res.error || 'unknown error')); }
  });
}

function newZoneDragOver(e)  { e.preventDefault(); document.getElementById('new-circle-zone').classList.add('drag-over'); }
function newZoneDragLeave(e) { document.getElementById('new-circle-zone').classList.remove('drag-over'); }
function newZoneDrop(e)      { e.preventDefault(); document.getElementById('new-circle-zone').classList.remove('drag-over'); pendingDropUid = dragPersonId; openCreateModal(dragPersonId); }

function openDetailPanel(circleId) {
  openCircleId = circleId;
  var circ = circleById(circleId);
  if (!circ) return;
  var memberIds = MEMBER_MAP[circleId] || [];
  var members = memberIds.map(mid => personById(mid)).filter(Boolean);
  document.getElementById('cdp-title').textContent = circ.name;
  document.getElementById('cdp-swatch').style.background = circ.color;
  document.getElementById('cdp-rename-input').value = circ.name;
  var list = document.getElementById('cdp-members-list');
  list.innerHTML = '';
  if (members.length === 0) {
    list.innerHTML = '<div style="padding:24px 16px;text-align:center;color:#aaa;font-size:13px">No members yet.<br>Drag someone onto this circle.</div>';
  } else {
    members.forEach(function(person) {
      var row = document.createElement('div');
      row.className = 'cdp-member-row';
      row.innerHTML = '<img src="'+escHtml(avatarUrl(person))+'" class="av40" style="border-radius:50%;flex-shrink:0"><div class="cdp-member-info"><div class="cdp-member-name">'+escHtml(person.display_name)+'</div><div class="cdp-member-tag">'+escHtml(person.tagline||('@'+person.username))+'</div></div><button class="cdp-remove-btn" onclick="removeMemberFromCircle('+circleId+','+person.id+')">Remove</button>';
      list.appendChild(row);
    });
  }
  var colorRow = document.getElementById('cdp-color-row');
  colorRow.innerHTML = '';
  COLORS.forEach(function(col) {
    var sw = document.createElement('div');
    sw.className = 'cdp-cswatch' + (col === circ.color ? ' active' : '');
    sw.style.background = col; sw.title = col;
    sw.onclick = function() {
      colorRow.querySelectorAll('.cdp-cswatch').forEach(s => s.classList.remove('active'));
      sw.classList.add('active');
      saveCircleColor(circleId, col);
    };
    colorRow.appendChild(sw);
  });
  document.getElementById('circle-detail-panel').classList.add('open');
}

function closeDetailPanel() { document.getElementById('circle-detail-panel').classList.remove('open'); openCircleId = null; }

function removeMemberFromCircle(circleId, memberId) {
  var circ = circleById(circleId), person = personById(memberId);
  apiPost('circles_remove_member', {circle_id: circleId, member_id: memberId}, function(res) {
    if (res.ok) {
      MEMBER_MAP[circleId] = (MEMBER_MAP[circleId]||[]).filter(id => id != memberId);
      var idx = circleIndex(circleId);
      if (idx !== -1) CIRCLES[idx].mc = MEMBER_MAP[circleId].length;
      render();
      if (openCircleId == circleId) openDetailPanel(circleId);
      toast((person||{display_name:'Person'}).display_name + ' removed from ' + (circ||{name:'circle'}).name);
    }
  });
}

function saveRename() {
  if (!openCircleId) return;
  var name = document.getElementById('cdp-rename-input').value.trim();
  if (!name) return;
  apiPost('circles_rename', {circle_id: openCircleId, name: name}, function(res) {
    if (res.ok) {
      var idx = circleIndex(openCircleId);
      if (idx !== -1) CIRCLES[idx].name = name;
      document.getElementById('cdp-title').textContent = name;
      render(); toast('Circle renamed to "' + name + '"');
    }
  });
}

function saveCircleColor(circleId, color) {
  apiPost('circles_rename', {circle_id: circleId, color: color}, function(res) {
    if (res.ok) {
      var idx = circleIndex(circleId);
      if (idx !== -1) CIRCLES[idx].color = color;
      document.getElementById('cdp-swatch').style.background = color;
      render();
    }
  });
}

function deleteCurrentCircle() {
  if (!openCircleId) return;
  var circ = circleById(openCircleId);
  if (!circ) return;
  if (!confirm('Delete circle "' + circ.name + '"? Members won\'t be deleted.')) return;
  apiPost('circles_delete', {circle_id: openCircleId}, function(res) {
    if (res.ok) {
      var idx = circleIndex(openCircleId);
      if (idx !== -1) CIRCLES.splice(idx, 1);
      delete MEMBER_MAP[openCircleId];
      closeDetailPanel(); render(); toast('Circle deleted');
    }
  });
}

function openCreateModal(forPersonId) {
  pendingDropUid = forPersonId; newCircleColor = COLORS[0];
  document.getElementById('newCircleName').value = '';
  var row = document.getElementById('newCircleColors');
  row.innerHTML = '';
  COLORS.forEach(function(col, i) {
    var sw = document.createElement('div');
    sw.className = 'cdp-cswatch' + (i === 0 ? ' active' : '');
    sw.style.background = col;
    sw.onclick = function() { row.querySelectorAll('.cdp-cswatch').forEach(s => s.classList.remove('active')); sw.classList.add('active'); newCircleColor = col; };
    row.appendChild(sw);
  });
  document.getElementById('createModal').classList.add('open');
  setTimeout(() => document.getElementById('newCircleName').focus(), 150);
}

function closeCreateModal() { document.getElementById('createModal').classList.remove('open'); pendingDropUid = null; }

function submitCreateCircle() {
  var name = document.getElementById('newCircleName').value.trim();
  if (!name) { document.getElementById('newCircleName').focus(); return; }
  apiPost('circles_create', {name: name, color: newCircleColor}, function(res) {
    if (res.ok) {
      CIRCLES.push({id: res.id, user_id: ME.id, name: res.name, color: res.color, mc: 0});
      MEMBER_MAP[res.id] = [];
      if (pendingDropUid) {
        var uid = pendingDropUid;
        apiPost('circles_add_member', {circle_id: res.id, member_id: uid}, function(r2) {
          if (r2.ok) { MEMBER_MAP[res.id] = [uid]; CIRCLES[CIRCLES.length-1].mc = 1; }
          render(); closeCreateModal(); openDetailPanel(res.id);
          toast('Circle "' + name + '" created!');
        });
      } else {
        render(); closeCreateModal(); openDetailPanel(res.id);
        toast('Circle "' + name + '" created!');
      }
    } else { toast('Error: ' + (res.error || 'could not create circle')); }
  });
}

function filterPeople(q) {
  q = q.toLowerCase();
  document.querySelectorAll('.circle-person-item').forEach(function(el) {
    var name = (el.getAttribute('data-name')||'').toLowerCase();
    var tag  = (el.getAttribute('data-tag')||'').toLowerCase();
    el.style.display = (!q || name.includes(q) || tag.includes(q)) ? '' : 'none';
  });
}

window.addEventListener('resize', render);
document.addEventListener('keydown', e => { if (e.key === 'Escape') { closeDetailPanel(); closeCreateModal(); } });
document.getElementById('newCircleName').addEventListener('keydown', e => { if (e.key === 'Enter') submitCreateCircle(); });

render();
</script>
<?php
// ---- HOME / STREAM ----
elseif ($page === 'home'):
  if ($u) {
    $filterCircleId = (isset($_GET['circle']) && $_GET['circle'] !== 'all') ? (int)$_GET['circle'] : 0;
    // make sure the circle belongs to this user
    if ($filterCircleId) {
      $cOwn = getDB()->prepare("SELECT id FROM circles WHERE id=? AND user_id=?");
      $cOwn->execute([$filterCircleId, $u['id']]);
      if (!$cOwn->fetch()) $filterCircleId = 0;
    }

    $myCircles = getDB()->prepare("SELECT c.id, c.name, c.color, (SELECT COUNT(*) FROM circle_members WHERE circle_id=c.id) AS mc FROM circles c WHERE c.user_id=? ORDER BY c.name ASC");
    $myCircles->execute([$u['id']]);
    $myCircles = $myCircles->fetchAll();

    $postCols = "p.*,u.display_name,u.username,u.avatar,u.role,u.early_access,u.suspended,
      (SELECT COUNT(*) FROM plusones WHERE post_id=COALESCE(p.original_post_id,p.id)) AS po_count,
      (SELECT COUNT(*) FROM comments WHERE post_id=COALESCE(p.original_post_id,p.id)) AS cm_count,
      (SELECT COUNT(*) FROM plusones WHERE post_id=COALESCE(p.original_post_id,p.id) AND user_id=?) AS user_po,
      (SELECT COUNT(*) FROM reshares WHERE post_id=COALESCE(p.original_post_id,p.id)) AS reshare_count";

    if ($filterCircleId) {
      $feedStmt = getDB()->prepare("SELECT $postCols FROM posts p JOIN users u ON u.id=p.user_id WHERE p.user_id IN (SELECT member_id FROM circle_members WHERE circle_id=?) AND p.visibility='public' AND p.community_id IS NULL ORDER BY p.created_at DESC LIMIT 50");
      $feedStmt->execute([$u['id'], $filterCircleId]);
    } else {
      $feedStmt = getDB()->prepare("SELECT $postCols FROM posts p JOIN users u ON u.id=p.user_id WHERE (p.user_id=? OR p.user_id IN (SELECT following_id FROM follows WHERE follower_id=?)) AND p.visibility='public' AND p.community_id IS NULL ORDER BY p.created_at DESC LIMIT 50");
      $feedStmt->execute([$u['id'], $u['id'], $u['id']]);
    }
    $posts = $feedStmt->fetchAll();

    $sugStmt = getDB()->prepare("SELECT id,display_name,username,avatar,tagline FROM users WHERE id!=? AND id NOT IN (SELECT following_id FROM follows WHERE follower_id=?) AND suspended=0 ORDER BY RANDOM() LIMIT 6");
    $sugStmt->execute([$u['id'], $u['id']]);
    $suggestions = $sugStmt->fetchAll();
  } else {
    $filterCircleId = 0; $myCircles = [];
    $posts = getDB()->query("SELECT p.*,u.display_name,u.username,u.avatar,u.role,u.early_access,u.suspended,(SELECT COUNT(*) FROM plusones WHERE post_id=p.id) AS po_count,(SELECT COUNT(*) FROM comments WHERE post_id=p.id) AS cm_count,0 AS user_po FROM posts p JOIN users u ON u.id=p.user_id WHERE p.visibility='public' ORDER BY p.created_at DESC LIMIT 30")->fetchAll();
    $suggestions = [];
  }

  $isSuspended = $u && isUserSuspended($u);
?>
<div id="wrap" class="stream-layout">
<?php if ($u): ?>
<div id="sidebar-left">
  <div class="gcard sleft-profile">
    <a href="?page=profile&id=<?= $u['id'] ?>"><img src="<?= avatarSrc($u['avatar'], $u['display_name']) ?>" style="width:64px;height:64px;border-radius:50%;object-fit:cover;margin:0 auto 8px"></a>
    <div style="font-size:14px;font-weight:500"><?= h($u['display_name']) ?><?= userBadges($u) ?></div>
    <div style="font-size:12px;color:var(--sub)">@<?= h($u['username']) ?></div>
  </div>
  <div class="gcard" style="overflow:hidden"><div class="sleft-nav">
    <div class="sleft-section">Menu</div>
    <a href="?page=home" class="<?= ($page==='home'&&!$filterCircleId)?'active':'' ?>"><svg viewBox="0 0 24 24"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>Stream</a>
    <a href="?page=explore"><svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/></svg>Explore</a>
    <a href="?page=people"><svg viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>People</a>
    <a href="?page=communities"><svg viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/></svg>Communities</a>
    <a href="?page=notifications"><svg viewBox="0 0 24 24"><path d="M12 22c1.1 0 2-.9 2-2h-4a2 2 0 0 0 2 2zm6-6V11c0-3.07-1.63-5.64-4.5-6.32V4a1.5 1.5 0 1 0-3 0v.68C7.64 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/></svg>Notifications<?php if ($notifCount>0): ?><span style="background:var(--gred);color:#fff;border-radius:8px;font-size:10px;padding:1px 5px;margin-left:auto"><?= $notifCount ?></span><?php endif; ?></a>
    <a href="?page=messages"><svg viewBox="0 0 24 24"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg>Messages<?php if ($msgCount>0): ?><span style="background:var(--gblue);color:#fff;border-radius:8px;font-size:10px;padding:1px 5px;margin-left:auto"><?= $msgCount ?></span><?php endif; ?></a>
    <?php if ($u['role']==='admin'): ?><a href="?page=admin"><svg viewBox="0 0 24 24"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/></svg>Admin Panel</a><?php endif; ?>
    <div style="height:4px"></div>
  </div></div>

  <?php if (!empty($myCircles)): ?>
  <div class="gcard" style="overflow:hidden"><div class="sleft-nav">
    <div class="sleft-section" style="display:flex;align-items:center;justify-content:space-between;padding-right:12px">
      <span>Circles</span>
      <a href="?page=circles" style="font-size:10px;color:var(--gblue);font-weight:600;text-transform:none;letter-spacing:0">Manage</a>
    </div>
    <?php foreach ($myCircles as $mc): $isActive = ($filterCircleId === (int)$mc['id']); ?>
    <a href="?page=home&circle=<?= $mc['id'] ?>" class="sleft-circle-link <?= $isActive ? 'active' : '' ?>" style="--circle-color:<?= h($mc['color']) ?>">
      <span class="sleft-circle-dot" style="background:<?= h($mc['color']) ?>"></span>
      <span style="flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= h($mc['name']) ?></span>
      <span class="sleft-circle-count"><?= (int)$mc['mc'] ?></span>
    </a>
    <?php endforeach; ?>
    <div style="height:6px"></div>
  </div></div>
  <?php endif; ?>
</div>
<?php endif; ?>

<div id="stream">
  <?php if (!$u): ?>
  <div class="guest-banner">
    <h2>Welcome to <?= SITE_NAME ?></h2>
    <p>Share what matters. Connect with people who care about the same things you do.</p>
    <div class="guest-banner-btns">
      <?php if (!$earlyAccess): ?><a href="?page=register" class="gbtn gbtn-red">Join <?= SITE_NAME ?></a><?php endif; ?>
      <a href="?page=login" class="gbtn gbtn-outline" style="color:#fff;border-color:rgba(255,255,255,.4)">Sign In</a>
    </div>
  </div>
  <?php else: ?>

  <?php if (!$isSuspended): ?>
  <div class="gcard">
    <form method="post" enctype="multipart/form-data">
      <input type="hidden" name="action" value="post">
      <div class="composer-top">
        <img src="<?= avatarSrc($u['avatar'], $u['display_name']) ?>" class="av40">
        <textarea name="content" id="composerText" placeholder="Share what's on your mind…" rows="3"></textarea>
      </div>
      <div class="composer-actions">
        <div class="composer-tools">
          <label class="composer-tool" title="Add photo">
            <svg viewBox="0 0 24 24"><path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/></svg>
            Photo<input type="file" name="image" accept="image/*" style="display:none" id="imgPick">
          </label>
          <span id="imgLabel" style="font-size:12px;color:var(--sub);max-width:100px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"></span>
          <select name="visibility" class="vis-select">
            <option value="public">&#127760; Public</option>
            <option value="circles">&#9711; Circles</option>
          </select>
        </div>
        <button class="gbtn gbtn-blue gbtn-sm" type="submit">Share</button>
      </div>
    </form>
  </div>
  <?php else: ?>
  <div class="susp-banner gcard" style="margin-bottom:12px;padding:14px 18px;display:flex;align-items:flex-start;gap:10px;border-left:4px solid #dd4b39">
    <svg viewBox="0 0 24 24" style="width:20px;height:20px;flex-shrink:0;margin-top:1px;fill:#dd4b39"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
    <div>
      <div style="font-size:13px;font-weight:600;color:#c5221f;margin-bottom:3px">Your account is suspended</div>
      <div style="font-size:12px;color:#c5221f;opacity:.85"><?= h($u['suspend_reason'] ?? 'Violation of community guidelines.') ?><?php if ($u['suspended_until']): ?> Lifts <?= date('M j, Y', strtotime($u['suspended_until'])) ?>.<?php endif; ?></div>
    </div>
  </div>
  <?php endif; ?>
  <?php endif; ?>

  <?php if ($filterCircleId):
    $activeCircle = null;
    foreach ($myCircles as $mc) { if ((int)$mc['id'] === $filterCircleId) { $activeCircle = $mc; break; } }
    if ($activeCircle): ?>
  <div class="stream-filter-header" style="--filter-color:<?= h($activeCircle['color']) ?>">
    <span class="filter-dot" style="background:<?= h($activeCircle['color']) ?>"></span>
    <span class="filter-name"><?= h($activeCircle['name']) ?></span>
    <span style="font-size:12px;color:var(--sub);margin-right:12px"><?= (int)$activeCircle['mc'] ?> <?= (int)$activeCircle['mc'] === 1 ? 'person' : 'people' ?></span>
    <a href="?page=home" class="filter-clear">&#10005; All posts</a>
  </div>
  <?php endif; endif; ?>

  <?php if (empty($posts)): ?>
    <?php if ($filterCircleId && isset($activeCircle)): ?>
    <div class="gcard empty-state">
      <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm0-14c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4z"/></svg>
      <p>No posts from your <strong><?= h($activeCircle['name'] ?? '') ?></strong> circle yet.</p>
      <a href="?page=circles" class="gbtn gbtn-blue gbtn-sm" style="margin-top:12px;display:inline-flex">Manage Circles</a>
    </div>
    <?php elseif ($u): ?>
    <div class="gcard empty-state"><svg viewBox="0 0 24 24"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg><p>Your stream is empty. Follow people to see their posts here.</p><a href="?page=people" class="gbtn gbtn-blue gbtn-sm" style="margin-top:12px;display:inline-flex">Find People</a></div>
    <?php else: ?>
    <div class="gcard empty-state"><p>No public posts yet.</p></div>
    <?php endif; ?>
  <?php endif; ?>

  <?php foreach ($posts as $post):
    $rootPostId = $post['original_post_id'] ? (int)$post['original_post_id'] : (int)$post['id'];
    $origPost   = null;
    if ($post['original_post_id']) {
      $os = getDB()->prepare("SELECT p.*,u.display_name,u.username,u.avatar FROM posts p JOIN users u ON u.id=p.user_id WHERE p.id=?");
      $os->execute([$rootPostId]);
      $origPost = $os->fetch();
    }
    $userReshared = $u ? userReshared($rootPostId, $u['id']) : false;
  ?>
  <div class="gcard" id="post-card-<?= $post['id'] ?>">
    <?php if ($origPost): ?>
    <div style="display:flex;align-items:center;gap:8px;padding:9px 16px 0;color:var(--sub);font-size:12px">
      <svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:var(--gblue);flex-shrink:0"><path d="M18 16.08c-.76 0-1.44.3-1.96.77L8.91 12.7c.05-.23.09-.46.09-.7s-.04-.47-.09-.7l7.05-4.11c.54.5 1.25.81 2.04.81 1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3c0 .24.04.47.09.7L8.04 9.81C7.5 9.31 6.79 9 6 9c-1.66 0-3 1.34-3 3s1.34 3 3 3c.79 0 1.5-.31 2.04-.81l7.12 4.16c-.05.21-.08.43-.08.65 0 1.61 1.31 2.92 2.92 2.92s2.92-1.31 2.92-2.92-1.31-2.92-2.92-2.92z"/></svg>
      <a href="?page=profile&id=<?= $post['user_id'] ?>" style="color:var(--gblue);font-weight:500"><?= h($post['display_name']) ?></a>
      <span>reshared this</span>
    </div>
    <?php if ($post['reshare_comment']): ?><div class="reshare-comment"><?= h($post['reshare_comment']) ?></div><?php endif; ?>
    <?= renderReshareEmbed($origPost) ?>
    <?php else: ?>
    <div class="post-head">
      <a href="?page=profile&id=<?= $post['user_id'] ?>"><img src="<?= avatarSrc($post['avatar'], $post['display_name']) ?>" class="post-av"></a>
      <div class="post-meta">
        <div class="post-author"><a href="?page=profile&id=<?= $post['user_id'] ?>"><?= h($post['display_name']) ?></a><?= userBadges($post) ?></div>
        <div class="post-time"><?= timeAgo($post['created_at']) ?><span class="vis-chip"> &middot; &#127760; Public</span></div>
      </div>
      <?php if ($u && ($post['user_id'] == $u['id'] || $u['role'] === 'admin')): ?>
      <form method="post" style="margin:0" onsubmit="return confirm('Delete this post?')">
        <input type="hidden" name="action" value="delete_post">
        <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
        <button class="post-delete">&times;</button>
      </form>
      <?php endif; ?>
    </div>
    <?php if ($post['content']): ?><div class="post-body"><?= h($post['content']) ?></div><?php endif; ?>
    <?php if ($post['image']): ?><img src="<?= h($post['image']) ?>" class="post-img"><div style="height:4px"></div><?php endif; ?>
    <?php endif; ?>

    <div class="post-bar">
      <?php if ($u && !$isSuspended): ?>
        <button class="pbar-btn <?= $post['user_po'] ? 'plusoned' : '' ?>" onclick="plusOne(<?= $rootPostId ?>,this)">
          <svg viewBox="0 0 24 24"><path d="M1 21h4V9H1v12zm22-11c0-1.1-.9-2-2-2h-6.31l.95-4.57.03-.32c0-.41-.17-.79-.44-1.06L14.17 1 7.59 7.59C7.22 7.95 7 8.45 7 9v10c0 1.1.9 2 2 2h9c.83 0 1.54-.5 1.84-1.22l3.02-7.05c.09-.23.14-.47.14-.73v-2z"/></svg>
          +1 <span class="poc"><?= $post['po_count'] ?></span>
        </button>
        <div class="pbar-sep"></div>
        <button class="pbar-btn" onclick="toggleCmts(<?= $rootPostId ?>)">
          <svg viewBox="0 0 24 24"><path d="M21.99 4c0-1.1-.89-2-1.99-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h14l4 4-.01-18z"/></svg>
          <?= $post['cm_count'] ?> Comment<?= $post['cm_count'] != 1 ? 's' : '' ?>
        </button>
        <div class="pbar-sep"></div>
        <?php if ($post['user_id'] != $u['id'] && !$origPost): ?>
        <div style="position:relative">
          <button class="pbar-btn <?= $userReshared ? 'reshared' : '' ?>" onclick="toggleResharePopover(<?= $rootPostId ?>,this)" id="rsBtn-<?= $rootPostId ?>">
            <svg viewBox="0 0 24 24"><path d="M18 16.08c-.76 0-1.44.3-1.96.77L8.91 12.7c.05-.23.09-.46.09-.7s-.04-.47-.09-.7l7.05-4.11c.54.5 1.25.81 2.04.81 1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3c0 .24.04.47.09.7L8.04 9.81C7.5 9.31 6.79 9 6 9c-1.66 0-3 1.34-3 3s1.34 3 3 3c.79 0 1.5-.31 2.04-.81l7.12 4.16c-.05.21-.08.43-.08.65 0 1.61 1.31 2.92 2.92 2.92s2.92-1.31 2.92-2.92-1.31-2.92-2.92-2.92z"/></svg>
            Reshare <span class="rsc" id="rsc-<?= $rootPostId ?>"><?= $post['reshare_count'] > 0 ? $post['reshare_count'] : '' ?></span>
          </button>
        </div>
        <?php elseif (!$origPost): ?>
        <button class="pbar-btn" style="cursor:default;opacity:.5">
          <svg viewBox="0 0 24 24"><path d="M18 16.08c-.76 0-1.44.3-1.96.77L8.91 12.7c.05-.23.09-.46.09-.7s-.04-.47-.09-.7l7.05-4.11c.54.5 1.25.81 2.04.81 1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3c0 .24.04.47.09.7L8.04 9.81C7.5 9.31 6.79 9 6 9c-1.66 0-3 1.34-3 3s1.34 3 3 3c.79 0 1.5-.31 2.04-.81l7.12 4.16c-.05.21-.08.43-.08.65 0 1.61 1.31 2.92 2.92 2.92s2.92-1.31 2.92-2.92-1.31-2.92-2.92-2.92z"/></svg>
          <span class="rsc" id="rsc-<?= $rootPostId ?>"><?= $post['reshare_count'] > 0 ? $post['reshare_count'] : '' ?></span>
        </button>
        <?php endif; ?>
        <?php if ($post['reshare_count'] > 0): ?>
        <div class="pbar-sep"></div>
        <button class="pbar-btn" onclick="openRipples(<?= $rootPostId ?>)" title="See who reshared">
          <svg viewBox="0 0 24 24" style="width:16px;height:16px"><circle cx="12" cy="12" r="3" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="12" r="6" fill="none" stroke="currentColor" stroke-width="1.5" opacity=".6"/><circle cx="12" cy="12" r="10" fill="none" stroke="currentColor" stroke-width="1" opacity=".3"/></svg>
          Ripples
        </button>
        <?php endif; ?>
        <?php if ($post['user_id'] != $u['id'] && !$origPost): ?>
        <div class="pbar-sep"></div>
        <form method="post" style="margin:0"><input type="hidden" name="action" value="follow"><input type="hidden" name="target_id" value="<?= $post['user_id'] ?>"><button class="pbar-btn" type="submit"><svg viewBox="0 0 24 24"><path d="M15 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm-9-2V7H4v3H1v2h3v3h2v-3h3v-2H6zm9 4c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>Follow</button></form>
        <?php endif; ?>
      <?php elseif ($u): ?>
        <span style="font-size:13px;color:var(--sub);padding:8px 10px"><?= $post['po_count'] ?> +1s</span>
      <?php else: ?>
        <span style="font-size:13px;color:var(--sub);padding:8px 10px"><?= $post['po_count'] ?> +1s</span>
        <a href="?page=login" class="gbtn gbtn-outline gbtn-sm" style="margin-left:auto">Sign in</a>
      <?php endif; ?>
    </div>

    <?php if ($u && !$isSuspended): ?>
    <div class="comments-area" id="cmts-<?= $rootPostId ?>" style="display:none">
      <?php
        $cs = getDB()->prepare("SELECT c.*,u.display_name,u.avatar FROM comments c JOIN users u ON u.id=c.user_id WHERE c.post_id=? ORDER BY c.created_at ASC");
        $cs->execute([$rootPostId]);
        foreach ($cs->fetchAll() as $cm):
      ?>
      <div class="cmt-item">
        <img src="<?= avatarSrc($cm['avatar'], $cm['display_name']) ?>" class="cmt-av">
        <div class="cmt-bubble">
          <div class="cmt-name"><a href="?page=profile&id=<?= $cm['user_id'] ?>"><?= h($cm['display_name']) ?></a></div>
          <div class="cmt-text"><?= h($cm['content']) ?></div>
          <div class="cmt-time"><?= timeAgo($cm['created_at']) ?></div>
        </div>
      </div>
      <?php endforeach; ?>
      <form method="post" class="cmt-form">
        <input type="hidden" name="action" value="comment">
        <input type="hidden" name="post_id" value="<?= $rootPostId ?>">
        <img src="<?= avatarSrc($u['avatar'], $u['display_name']) ?>" class="cmt-av">
        <input type="text" name="content" placeholder="Add a comment…" required>
        <button class="gbtn gbtn-blue gbtn-sm">Post</button>
      </form>
    </div>
    <?php endif; ?>
  </div>
  <?php endforeach; ?>
</div>

<div id="sidebar-right">
  <?php if ($u && !empty($suggestions)): ?>
  <div class="gcard"><div class="rsb-section">
    <div class="rsb-title">People you may know</div>
    <?php foreach ($suggestions as $sg): ?>
    <div class="rsb-person">
      <a href="?page=profile&id=<?= $sg['id'] ?>"><img src="<?= avatarSrc($sg['avatar'], $sg['display_name']) ?>" class="av40"></a>
      <div class="rsb-person-info">
        <div class="n"><a href="?page=profile&id=<?= $sg['id'] ?>" style="color:#212121"><?= h($sg['display_name']) ?></a></div>
        <?php if ($sg['tagline']): ?><div class="t"><?= h($sg['tagline']) ?></div><?php endif; ?>
      </div>
      <form method="post" style="margin:0"><input type="hidden" name="action" value="follow"><input type="hidden" name="target_id" value="<?= $sg['id'] ?>"><button class="gbtn gbtn-outline gbtn-sm" style="padding:4px 10px;font-size:11px">+Follow</button></form>
    </div>
    <?php endforeach; ?>
    <a href="?page=people" style="font-size:12px;color:var(--gblue)">View all people &rarr;</a>
  </div></div>
  <?php endif; ?>
  <?php if (!$u): ?>
  <div class="gcard"><div class="rsb-section">
    <div class="rsb-title">Get Started</div>
    <?php if (!$earlyAccess): ?><a href="?page=register" class="gbtn gbtn-blue" style="width:100%;justify-content:center;margin-bottom:6px">Create Account</a><?php endif; ?>
    <a href="?page=login" class="gbtn gbtn-outline" style="width:100%;justify-content:center">Sign In</a>
  </div></div>
  <?php endif; ?>
  <div class="gcard"><div class="rsb-section" style="font-size:11px;color:#aaa;line-height:1.8">
    <?= SITE_NAME ?> v<?= VERSION ?> &middot; <a href="<?= GITHUB_URL ?>" target="_blank" style="color:#aaa">Open Source</a>
  </div></div>
</div>
</div>
<?php
elseif ($page === 'explore'):
  $stmt = getDB()->prepare("SELECT p.*,u.display_name,u.username,u.avatar,u.role,u.early_access,(SELECT COUNT(*) FROM plusones WHERE post_id=p.id) AS po_count,(SELECT COUNT(*) FROM comments WHERE post_id=p.id) AS cm_count,(SELECT COUNT(*) FROM plusones WHERE post_id=p.id AND user_id=?) AS user_po FROM posts p JOIN users u ON u.id=p.user_id WHERE p.visibility='public' AND p.community_id IS NULL ORDER BY p.created_at DESC LIMIT 60");
  $stmt->execute([$u ? $u['id'] : 0]);
  $posts = $stmt->fetchAll();
?>
<div id="wrap"><div id="stream" style="max-width:620px;margin:0 auto">
  <div class="page-head"><h2>&#127760; Public Stream</h2></div>
  <?php foreach ($posts as $post): ?>
  <div class="gcard">
    <div class="post-head">
      <a href="?page=profile&id=<?= $post['user_id'] ?>"><img src="<?= avatarSrc($post['avatar'], $post['display_name']) ?>" class="post-av"></a>
      <div class="post-meta">
        <div class="post-author"><a href="?page=profile&id=<?= $post['user_id'] ?>"><?= h($post['display_name']) ?></a><?= userBadges($post) ?></div>
        <div class="post-time"><?= timeAgo($post['created_at']) ?></div>
      </div>
    </div>
    <?php if ($post['content']): ?><div class="post-body"><?= h($post['content']) ?></div><?php endif; ?>
    <?php if ($post['image']): ?><img src="<?= h($post['image']) ?>" class="post-img"><?php endif; ?>
    <div class="post-bar">
      <?php if ($u): ?>
      <button class="pbar-btn <?= $post['user_po'] ? 'plusoned' : '' ?>" onclick="plusOne(<?= $post['id'] ?>,this)"><svg viewBox="0 0 24 24"><path d="M1 21h4V9H1v12zm22-11c0-1.1-.9-2-2-2h-6.31l.95-4.57.03-.32c0-.41-.17-.79-.44-1.06L14.17 1 7.59 7.59C7.22 7.95 7 8.45 7 9v10c0 1.1.9 2 2 2h9c.83 0 1.54-.5 1.84-1.22l3.02-7.05c.09-.23.14-.47.14-.73v-2z"/></svg>+1 <span class="poc"><?= $post['po_count'] ?></span></button>
      <?php else: ?>
      <span style="font-size:13px;color:var(--sub);padding:8px 10px"><?= $post['po_count'] ?> +1s</span>
      <a href="?page=login" class="gbtn gbtn-outline gbtn-sm" style="margin-left:auto">Sign in</a>
      <?php endif; ?>
    </div>
  </div>
  <?php endforeach; ?>
  <?php if (empty($posts)): ?><div class="gcard empty-state"><p>No public posts yet.</p></div><?php endif; ?>
</div></div>

<?php elseif ($page === 'profile'):
  $pid   = isset($_GET['id']) ? (int)$_GET['id'] : ($u ? $u['id'] : 0);
  $puser = $pid ? getDB()->query("SELECT * FROM users WHERE id=$pid")->fetch() : null;
  if (!$puser) { echo '<div class="gcard" style="margin:32px auto;max-width:600px;padding:24px"><p>User not found.</p></div>'; }
  else {
    $isOwn          = $u && $u['id'] == $puser['id'];
    $isFollowing    = $u ? (bool)getDB()->query("SELECT 1 FROM follows WHERE follower_id={$u['id']} AND following_id={$puser['id']}")->fetch() : false;
    $followerCount  = getDB()->query("SELECT COUNT(*) FROM follows WHERE following_id={$puser['id']}")->fetchColumn();
    $followingCount = getDB()->query("SELECT COUNT(*) FROM follows WHERE follower_id={$puser['id']}")->fetchColumn();
    $postCount      = getDB()->query("SELECT COUNT(*) FROM posts WHERE user_id={$puser['id']}")->fetchColumn();
    $userPoSql      = $u ? ",EXISTS(SELECT 1 FROM plusones WHERE post_id=p.id AND user_id={$u['id']}) user_po" : ",0 user_po";
    $profilePosts   = getDB()->query("SELECT p.*,u.display_name,u.avatar,u.role,u.early_access,u.suspended,(SELECT COUNT(*) FROM plusones WHERE post_id=p.id) po_count{$userPoSql} FROM posts p JOIN users u ON p.user_id=u.id WHERE p.user_id={$puser['id']} AND p.community_id IS NULL ORDER BY p.created_at DESC LIMIT 30")->fetchAll();
    $pSuspended     = isUserSuspended($puser);
?>
<div id="wrap" style="max-width:700px;margin:0 auto">
  <?php if ($pSuspended && ($isOwn || ($u && $u['role']==='admin'))): ?>
  <div style="background:#fce8e6;border:1px solid #dd4b39;border-radius:3px;padding:10px 14px;margin-bottom:14px;font-size:13px;color:#c5221f">
    &#9888; <?= $isOwn ? 'Your account is currently suspended.' : 'This account is suspended.' ?>
  </div>
  <?php endif; ?>
  <div class="gcard profile-card">
    <div class="profile-cover" <?= $puser['cover'] ? 'style="background-image:url('.h($puser['cover']).');background-size:cover;background-position:center"' : '' ?>></div>
    <div class="profile-av-wrap"><img src="<?= avatarSrc($puser['avatar'], $puser['display_name']) ?>" class="profile-av"></div>
    <div class="profile-info">
      <h2><?= h($puser['display_name']) ?><?= userBadges($puser) ?></h2>
      <?php if ($puser['username']): ?><div class="profile-handle">@<?= h($puser['username']) ?></div><?php endif; ?>
      <?php if ($puser['bio']): ?><div class="profile-bio"><?= h($puser['bio']) ?></div><?php endif; ?>
      <div class="profile-stats">
        <span><strong><?= $postCount ?></strong> Posts</span>
        <span><strong><?= $followerCount ?></strong> Followers</span>
        <span><strong><?= $followingCount ?></strong> Following</span>
      </div>
      <div class="profile-actions">
        <?php if ($isOwn): ?>
          <a href="?page=edit_profile" class="gbtn gbtn-outline">Edit Profile</a>
        <?php elseif ($u): ?>
          <?php if (!$pSuspended): ?><button class="gbtn gbtn-primary" onclick="toggleFollow(<?= $puser['id'] ?>,this)"><?= $isFollowing ? 'Unfollow' : 'Follow' ?></button><?php endif; ?>
          <a href="?page=messages&with=<?= $puser['id'] ?>" class="gbtn gbtn-outline" style="font-size:12px">Message</a>
        <?php else: ?>
          <a href="?page=login" class="gbtn gbtn-primary">Follow</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <?php foreach ($profilePosts as $post): ?>
  <div class="gcard">
    <div class="post-head">
      <img src="<?= avatarSrc($puser['avatar'], $puser['display_name']) ?>" class="post-av">
      <div class="post-meta">
        <div class="post-author"><?= h($puser['display_name']) ?><?= userBadges($puser) ?></div>
        <div class="post-time"><?= timeAgo($post['created_at']) ?></div>
      </div>
      <?php if ($u && ($u['id'] == $post['user_id'] || $u['role'] === 'admin')): ?>
      <form method="post" style="margin:0" onsubmit="return confirm('Delete?')"><input type="hidden" name="action" value="delete_post"><input type="hidden" name="post_id" value="<?= $post['id'] ?>"><button class="post-delete">&times;</button></form>
      <?php endif; ?>
    </div>
    <?php if ($post['content']): ?><div class="post-body"><?= nl2br(h($post['content'])) ?></div><?php endif; ?>
    <?php if ($post['image']): ?><img src="<?= h($post['image']) ?>" class="post-img"><?php endif; ?>
    <div class="post-bar">
      <?php if ($u && !isUserSuspended($u)): ?>
      <button class="pbar-btn <?= $post['user_po'] ? 'plusoned' : '' ?>" onclick="plusOne(<?= $post['id'] ?>,this)"><svg viewBox="0 0 24 24"><path d="M1 21h4V9H1v12zm22-11c0-1.1-.9-2-2-2h-6.31l.95-4.57.03-.32c0-.41-.17-.79-.44-1.06L14.17 1 7.59 7.59C7.22 7.95 7 8.45 7 9v10c0 1.1.9 2 2 2h9c.83 0 1.54-.5 1.84-1.22l3.02-7.05c.09-.23.14-.47.14-.73v-2z"/></svg>+1 <span class="poc"><?= $post['po_count'] ?></span></button>
      <button class="pbar-btn" onclick="toggleCmts(<?= $post['id'] ?>)"><svg viewBox="0 0 24 24"><path d="M21.99 4c0-1.1-.89-2-1.99-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h14l4 4-.01-18z"/></svg>Comment</button>
      <?php else: ?>
      <span style="font-size:13px;color:var(--sub);padding:8px 10px"><?= $post['po_count'] ?> +1s</span>
      <?php endif; ?>
    </div>
    <div class="comments-area" id="cmts-<?= $post['id'] ?>" style="display:none">
      <?php $cs = getDB()->prepare("SELECT c.*,u.display_name,u.avatar FROM comments c JOIN users u ON u.id=c.user_id WHERE c.post_id=? ORDER BY c.created_at ASC"); $cs->execute([$post['id']]); foreach ($cs->fetchAll() as $cm): ?>
      <div class="cmt-item"><img src="<?= avatarSrc($cm['avatar'],$cm['display_name']) ?>" class="cmt-av"><div class="cmt-bubble"><div class="cmt-name"><a href="?page=profile&id=<?= $cm['user_id'] ?>"><?= h($cm['display_name']) ?></a></div><div class="cmt-text"><?= h($cm['content']) ?></div><div class="cmt-time"><?= timeAgo($cm['created_at']) ?></div></div></div>
      <?php endforeach; ?>
      <?php if ($u && !isUserSuspended($u)): ?>
      <form method="post" class="cmt-form"><input type="hidden" name="action" value="comment"><input type="hidden" name="post_id" value="<?= $post['id'] ?>"><img src="<?= avatarSrc($u['avatar'],$u['display_name']) ?>" class="cmt-av"><input type="text" name="content" placeholder="Add a comment…" required><button class="gbtn gbtn-blue gbtn-sm">Post</button></form>
      <?php endif; ?>
    </div>
  </div>
  <?php endforeach; ?>
  <?php if (empty($profilePosts)): ?><div class="gcard empty-state"><p>No posts yet.</p></div><?php endif; ?>
</div>
<?php } ?>

<?php elseif ($page === 'edit_profile'):
  requireLogin();
?>
<div id="wrap" style="max-width:600px;margin:0 auto">
  <div class="gcard" style="padding:24px">
    <h3 style="margin:0 0 20px;font-size:18px;font-weight:400">Edit Profile</h3>
    <form method="post" enctype="multipart/form-data">
      <input type="hidden" name="action" value="update_profile">
      <div class="form-group"><label>Display Name</label><input type="text" name="display_name" value="<?= h($u['display_name']) ?>" maxlength="60"></div>
      <div class="form-group"><label>Bio</label><textarea name="bio" rows="3" maxlength="200"><?= h($u['bio'] ?? '') ?></textarea></div>
      <div class="form-group"><label>Tagline</label><input type="text" name="tagline" value="<?= h($u['tagline'] ?? '') ?>" maxlength="80"></div>
      <div class="form-group"><label>Location</label><input type="text" name="location" value="<?= h($u['location'] ?? '') ?>" maxlength="100"></div>
      <div class="form-group"><label>Website</label><input type="url" name="website" value="<?= h($u['website'] ?? '') ?>"></div>
      <div class="form-group"><label>Avatar Photo</label><input type="file" name="avatar" accept="image/*"></div>
      <div class="form-group"><label>Cover Photo</label><input type="file" name="cover" accept="image/*"></div>
      <button class="gbtn gbtn-blue" type="submit">Save Changes</button>
      <a href="?page=profile&id=<?= $u['id'] ?>" class="gbtn gbtn-outline" style="margin-left:8px">Cancel</a>
    </form>
  </div>
</div>

<?php elseif ($page === 'people'):
  $search = trim($_GET['q'] ?? '');
  if ($search) {
    $stmt = getDB()->prepare("SELECT * FROM users WHERE (display_name LIKE ? OR username LIKE ?) AND role!='admin' ORDER BY display_name LIMIT 40");
    $stmt->execute(["%$search%", "%$search%"]);
  } else {
    $stmt = getDB()->query("SELECT * FROM users WHERE role!='admin' ORDER BY id DESC LIMIT 40");
  }
  $people = $stmt->fetchAll();
  $myFollowing = $u ? getDB()->query("SELECT following_id FROM follows WHERE follower_id={$u['id']}")->fetchAll(PDO::FETCH_COLUMN) : [];
?>
<div id="wrap" style="max-width:700px;margin:0 auto">
  <div class="page-head"><h2>&#128100; People</h2></div>
  <div class="people-grid">
    <?php foreach ($people as $p): $pSusp = isUserSuspended($p); ?>
    <div class="gcard people-card">
      <a href="?page=profile&id=<?= $p['id'] ?>"><img src="<?= avatarSrc($p['avatar'], $p['display_name']) ?>" class="people-av"></a>
      <div class="people-info">
        <a href="?page=profile&id=<?= $p['id'] ?>"><strong><?= h($p['display_name']) ?></strong><?= userBadges($p) ?></a>
        <?php if ($p['username']): ?><div style="font-size:13px;color:var(--sub)">@<?= h($p['username']) ?></div><?php endif; ?>
        <?php if ($p['bio']): ?><div style="font-size:13px;margin-top:4px"><?= h(mb_substr($p['bio'],0,80)) ?></div><?php endif; ?>
      </div>
      <?php if ($u && $u['id'] != $p['id'] && !$pSusp): ?>
      <button class="gbtn gbtn-primary gbtn-sm" onclick="toggleFollow(<?= $p['id'] ?>,this)"><?= in_array($p['id'], $myFollowing) ? 'Unfollow' : 'Follow' ?></button>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>
    <?php if (empty($people)): ?><div class="gcard empty-state" style="grid-column:1/-1"><p>No members found.</p></div><?php endif; ?>
  </div>
</div>

<?php
elseif ($page === 'communities'):
  $comms   = getDB()->query("SELECT c.*,u.display_name AS owner_name,(SELECT COUNT(*) FROM community_members WHERE community_id=c.id) AS mc FROM communities c JOIN users u ON u.id=c.owner_id WHERE c.visibility='public' ORDER BY mc DESC,c.created_at DESC")->fetchAll();
  $myComms = $u ? getDB()->query("SELECT community_id FROM community_members WHERE user_id={$u['id']}")->fetchAll(PDO::FETCH_COLUMN) : [];
?>
<div id="wrap" style="max-width:800px;margin:0 auto">
  <div class="page-head">
    <h2>&#127758; Communities</h2>
    <?php if ($u && !isUserSuspended($u)): ?>
    <button class="gbtn gbtn-blue gbtn-sm" onclick="document.getElementById('newCommModal').classList.add('open')">+ Create</button>
    <?php endif; ?>
  </div>
  <div class="comm-grid">
    <?php foreach ($comms as $c):
      $isMember = in_array($c['id'], $myComms);
      $bgStyle  = $c['banner_image'] ? '' : ('background:' . h($c['color']) . ';');
      $hasIconImg = !empty($c['icon_image']);
    ?>
    <div class="comm-card <?= $hasIconImg ? 'comm-card-has-icon' : '' ?>">
      <a href="?page=community&id=<?= $c['id'] ?>">
        <div class="comm-cover-strip" style="<?= $bgStyle ?>">
          <?php if ($c['banner_image']): ?><img src="<?= h($c['banner_image']) ?>" class="comm-banner-img"><?php endif; ?>
          <?php if ($hasIconImg): ?>
            <div class="comm-icon-img-wrap"><img src="<?= h($c['icon_image']) ?>" alt=""></div>
          <?php else: ?>
            <span class="comm-icon-overlay"><?= h($c['icon'] ?? '🌐') ?></span>
          <?php endif; ?>
        </div>
      </a>
      <div class="comm-body">
        <div class="comm-name"><a href="?page=community&id=<?= $c['id'] ?>" style="color:var(--text)"><?= h($c['name']) ?></a></div>
        <div class="comm-members"><?= $c['mc'] ?> member<?= $c['mc'] != 1 ? 's' : '' ?></div>
        <?php if ($u && !isUserSuspended($u)): ?>
        <form method="post" style="margin-top:8px">
          <input type="hidden" name="action" value="join_community">
          <input type="hidden" name="community_id" value="<?= $c['id'] ?>">
          <button class="gbtn gbtn-sm <?= $isMember ? 'gbtn-outline' : 'gbtn-blue' ?>"><?= $isMember ? '✓ Joined' : 'Join' ?></button>
        </form>
        <?php endif; ?>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</div>

<?php if ($u && !isUserSuspended($u)): ?>
<div class="hz-modal-bg" id="newCommModal" onclick="if(event.target===this)this.classList.remove('open')">
  <div class="hz-modal" style="width:480px;max-width:95vw">
    <h3>Create a Community</h3>
    <form method="post" enctype="multipart/form-data">
      <input type="hidden" name="action" value="create_community">
      <div class="form-row"><label>Name</label><input type="text" name="name" required placeholder="Community name"></div>
      <div class="form-row"><label>Description</label><textarea name="description" rows="2" placeholder="What's this community about?"></textarea></div>
      <div class="form-row">
        <label>Icon emoji</label>
        <div class="icon-picker" id="iconPicker">
          <?php foreach ($COMM_ICONS as $ico): ?>
          <div class="icon-opt" onclick="selectIcon('<?= $ico ?>',this)"><?= $ico ?></div>
          <?php endforeach; ?>
        </div>
        <input type="hidden" name="icon" id="iconVal" value="🌐">
      </div>
      <div class="form-row">
        <label>Color</label>
        <div style="display:flex;flex-wrap:wrap;gap:7px;margin-top:4px">
          <?php foreach ($COMM_COLORS as $col): ?>
          <span class="cswatch" style="background:<?= $col ?>" onclick="selectCommColor('<?= $col ?>',this)" title="<?= $col ?>"></span>
          <?php endforeach; ?>
        </div>
        <input type="hidden" name="color" id="commColorVal" value="#4285f4">
      </div>
      <div class="hz-modal-footer">
        <button type="button" class="gbtn gbtn-outline" onclick="document.getElementById('newCommModal').classList.remove('open')">Cancel</button>
        <button class="gbtn gbtn-blue" type="submit">Create</button>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>

<?php elseif ($page === 'community'):
  $cid  = (int)($_GET['id'] ?? 0);
  $comm = $cid ? getDB()->query("SELECT c.*,u.display_name AS owner_name FROM communities c JOIN users u ON u.id=c.owner_id WHERE c.id=$cid")->fetch() : null;
  if (!$comm) { echo '<div class="gcard" style="margin:32px auto;max-width:600px;padding:24px"><p>Community not found.</p></div>'; }
  else {
    $memberCount = getDB()->query("SELECT COUNT(*) FROM community_members WHERE community_id=$cid")->fetchColumn();
    $isMember    = $u ? (bool)getDB()->query("SELECT 1 FROM community_members WHERE community_id=$cid AND user_id={$u['id']}")->fetch() : false;
    $isOwner     = $u && ($u['id'] == $comm['owner_id'] || $u['role'] === 'admin');
    $posts       = getDB()->query("SELECT p.*,u.display_name,u.avatar,u.role,u.early_access,(SELECT COUNT(*) FROM plusones WHERE post_id=p.id) po_count,(SELECT COUNT(*) FROM comments WHERE post_id=p.id) cm_count FROM posts p JOIN users u ON u.id=p.user_id WHERE p.community_id=$cid ORDER BY p.created_at DESC LIMIT 50")->fetchAll();
    $bannerStyle = $comm['banner_image'] ? 'background-image:url('.h($comm['banner_image']).');background-size:cover;background-position:center' : ($comm['banner_color'] ? 'background:'.$comm['banner_color'] : 'background:'.$comm['color']);
    $hasIconImg  = !empty($comm['icon_image']);
?>
<div id="wrap" style="max-width:700px;margin:0 auto">
  <div class="gcard" style="overflow:visible;position:relative">
    <div class="prof-cover" style="<?= $bannerStyle ?>">
      <div class="comm-detail-icon-wrap">
        <?php if ($hasIconImg): ?>
          <img src="<?= h($comm['icon_image']) ?>" alt="">
        <?php else: ?>
          <div class="comm-icon-fallback" style="background:<?= h($comm['color']) ?>"><?= h($comm['icon'] ?? '🌐') ?></div>
        <?php endif; ?>
      </div>
    </div>
    <div class="prof-head" style="margin-top:-20px">
      <div style="width:<?= $hasIconImg ? '80px' : '64px' ?>"></div><!-- spacer for the icon -->
      <div class="prof-info">
        <h2><?= h($comm['name']) ?></h2>
        <?php if ($comm['description']): ?><div class="tagline"><?= h($comm['description']) ?></div><?php endif; ?>
        <div style="font-size:12px;color:var(--sub);margin-top:4px"><?= $memberCount ?> member<?= $memberCount!=1?'s':'' ?> &middot; by <?= h($comm['owner_name']) ?></div>
      </div>
      <div class="prof-actions">
        <?php if ($u && !isUserSuspended($u)): ?>
        <form method="post" style="margin:0"><input type="hidden" name="action" value="join_community"><input type="hidden" name="community_id" value="<?= $cid ?>"><button class="gbtn <?= $isMember ? 'gbtn-outline' : 'gbtn-blue' ?>"><?= $isMember ? '✓ Joined' : 'Join' ?></button></form>
        <?php endif; ?>
        <?php if ($isOwner): ?><a href="?page=edit_community&id=<?= $cid ?>" class="gbtn gbtn-outline gbtn-sm">Edit</a><?php endif; ?>
      </div>
    </div>
  </div>

  <?php if ($u && ($isMember || $isOwner) && !isUserSuspended($u)): ?>
  <div class="gcard"><form method="post" enctype="multipart/form-data">
    <input type="hidden" name="action" value="community_post">
    <input type="hidden" name="community_id" value="<?= $cid ?>">
    <div class="composer-top"><img src="<?= avatarSrc($u['avatar'], $u['display_name']) ?>" class="av40"><textarea name="content" placeholder="Share something with <?= h($comm['name']) ?>…" rows="2"></textarea></div>
    <div class="composer-actions">
      <label class="composer-tool"><svg viewBox="0 0 24 24"><path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/></svg>Photo<input type="file" name="image" accept="image/*" style="display:none"></label>
      <button class="gbtn gbtn-blue gbtn-sm" type="submit">Post</button>
    </div>
  </form></div>
  <?php endif; ?>

  <?php foreach ($posts as $post): ?>
  <div class="gcard">
    <div class="post-head">
      <a href="?page=profile&id=<?= $post['user_id'] ?>"><img src="<?= avatarSrc($post['avatar'], $post['display_name']) ?>" class="post-av"></a>
      <div class="post-meta">
        <div class="post-author"><a href="?page=profile&id=<?= $post['user_id'] ?>"><?= h($post['display_name']) ?></a><?= userBadges($post) ?></div>
        <div class="post-time"><?= timeAgo($post['created_at']) ?></div>
      </div>
      <?php if ($u && ($u['id']==$post['user_id'] || $u['role']==='admin')): ?>
      <form method="post" onsubmit="return confirm('Delete?')"><input type="hidden" name="action" value="delete_post"><input type="hidden" name="post_id" value="<?= $post['id'] ?>"><button class="post-delete">&times;</button></form>
      <?php endif; ?>
    </div>
    <?php if ($post['content']): ?><div class="post-body"><?= h($post['content']) ?></div><?php endif; ?>
    <?php if ($post['image']): ?><img src="<?= h($post['image']) ?>" class="post-img"><?php endif; ?>
    <div class="post-bar">
      <?php if ($u && !isUserSuspended($u)): ?>
      <button class="pbar-btn" onclick="plusOne(<?= $post['id'] ?>,this)"><svg viewBox="0 0 24 24"><path d="M1 21h4V9H1v12zm22-11c0-1.1-.9-2-2-2h-6.31l.95-4.57.03-.32c0-.41-.17-.79-.44-1.06L14.17 1 7.59 7.59C7.22 7.95 7 8.45 7 9v10c0 1.1.9 2 2 2h9c.83 0 1.54-.5 1.84-1.22l3.02-7.05c.09-.23.14-.47.14-.73v-2z"/></svg>+1 <span class="poc"><?= $post['po_count'] ?></span></button>
      <button class="pbar-btn" onclick="toggleCmts(<?= $post['id'] ?>)"><svg viewBox="0 0 24 24"><path d="M21.99 4c0-1.1-.89-2-1.99-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h14l4 4-.01-18z"/></svg><?= $post['cm_count'] ?></button>
      <?php else: ?>
      <span style="font-size:13px;color:var(--sub);padding:8px 10px"><?= $post['po_count'] ?> +1s</span>
      <?php endif; ?>
    </div>
    <div class="comments-area" id="cmts-<?= $post['id'] ?>" style="display:none">
      <?php $cs2 = getDB()->prepare("SELECT c.*,u.display_name,u.avatar FROM comments c JOIN users u ON u.id=c.user_id WHERE c.post_id=? ORDER BY c.created_at ASC"); $cs2->execute([$post['id']]); foreach ($cs2->fetchAll() as $cm): ?>
      <div class="cmt-item"><img src="<?= avatarSrc($cm['avatar'],$cm['display_name']) ?>" class="cmt-av"><div class="cmt-bubble"><div class="cmt-name"><a href="?page=profile&id=<?= $cm['user_id'] ?>"><?= h($cm['display_name']) ?></a></div><div class="cmt-text"><?= h($cm['content']) ?></div><div class="cmt-time"><?= timeAgo($cm['created_at']) ?></div></div></div>
      <?php endforeach; ?>
      <?php if ($u && !isUserSuspended($u)): ?>
      <form method="post" class="cmt-form"><input type="hidden" name="action" value="comment"><input type="hidden" name="post_id" value="<?= $post['id'] ?>"><img src="<?= avatarSrc($u['avatar'],$u['display_name']) ?>" class="cmt-av"><input type="text" name="content" placeholder="Comment…" required><button class="gbtn gbtn-blue gbtn-sm">Post</button></form>
      <?php endif; ?>
    </div>
  </div>
  <?php endforeach; ?>
  <?php if (empty($posts)): ?><div class="gcard empty-state"><p>No posts yet. Be the first!</p></div><?php endif; ?>
</div>
<?php } ?>

<?php elseif ($page === 'edit_community'):
  requireLogin();
  $cid  = (int)($_GET['id'] ?? 0);
  $comm = $cid ? getDB()->query("SELECT * FROM communities WHERE id=$cid")->fetch() : null;
  if (!$comm || ($comm['owner_id'] != $u['id'] && $u['role'] !== 'admin')) { redirect('?page=communities'); }
?>
<div id="wrap" style="max-width:600px;margin:0 auto">
  <div class="gcard" style="padding:24px">
    <h3 style="margin:0 0 20px;font-size:18px;font-weight:400">Edit Community</h3>
    <form method="post" enctype="multipart/form-data">
      <input type="hidden" name="action" value="edit_community">
      <input type="hidden" name="community_id" value="<?= $cid ?>">
      <div class="form-row"><label>Name</label><input type="text" name="name" value="<?= h($comm['name']) ?>" required></div>
      <div class="form-row"><label>Description</label><textarea name="description" rows="3"><?= h($comm['description'] ?? '') ?></textarea></div>
      <div class="form-row">
        <label>Banner Image</label>
        <div class="banner-preview-box" id="bannerPreviewBox" style="<?= $comm['banner_image'] ? '' : 'background:'.$comm['color'] ?>">
          <?php if ($comm['banner_image']): ?><img src="<?= h($comm['banner_image']) ?>" id="bannerPreviewImg"><?php else: ?><div class="banner-preview-bg" id="bannerPreviewBg" style="background:<?= h($comm['color']) ?>"><span style="font-size:42px;opacity:.4">🖼</span></div><?php endif; ?>
        </div>
        <label class="banner-upload-label"><svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:currentColor"><path d="M21 15v4H3v-4H1v4a2 2 0 0 0 2 2h18a2 2 0 0 0 2-2v-4h-2zM17 8l-1.41-1.41L13 9.17V2h-2v7.17L8.41 6.59 7 8l5 5 5-5z"/></svg> Upload banner<input type="file" name="banner_image" accept="image/*" style="display:none"></label>
        <?php if ($comm['banner_image']): ?><label><input type="checkbox" name="clear_banner_image" value="1"> Remove banner image</label><?php endif; ?>
      </div>
      <div class="form-row">
        <label>Community Color</label>
        <div style="display:flex;flex-wrap:wrap;gap:7px;margin-top:4px">
          <?php foreach ($COMM_COLORS as $col): ?>
          <span class="cswatch" style="background:<?= $col ?>;<?= $col===$comm['color']?'border-color:#333;':'border-color:transparent;' ?>" onclick="selectCommColor('<?= $col ?>',this)" title="<?= $col ?>"></span>
          <?php endforeach; ?>
        </div>
        <input type="hidden" name="color" id="commColorVal" value="<?= h($comm['color']) ?>">
      </div>
      <div class="form-row">
        <label>Icon (emoji)</label>
        <div class="icon-picker" id="iconPicker">
          <?php foreach ($COMM_ICONS as $ico): ?>
          <div class="icon-opt <?= $ico===($comm['icon']??'🌐')?'sel':'' ?>" onclick="selectIcon('<?= $ico ?>',this)"><?= $ico ?></div>
          <?php endforeach; ?>
        </div>
        <input type="hidden" name="icon" id="iconVal" value="<?= h($comm['icon'] ?? '🌐') ?>">
      </div>
      <div class="form-row">
        <label>Icon Image (optional, replaces emoji)</label>
        <div class="icon-image-upload-area">
          <div class="icon-img-preview" id="iconImgPreview">
            <?php if (!empty($comm['icon_image'])): ?><img src="<?= h($comm['icon_image']) ?>" id="iconImgPreviewImg"><?php else: ?><span><?= h($comm['icon'] ?? '🌐') ?></span><?php endif; ?>
          </div>
          <label class="icon-upload-btn"><svg viewBox="0 0 24 24" style="width:13px;height:13px;fill:currentColor"><path d="M21 15v4H3v-4H1v4a2 2 0 0 0 2 2h18a2 2 0 0 0 2-2v-4h-2zM17 8l-1.41-1.41L13 9.17V2h-2v7.17L8.41 6.59 7 8l5 5 5-5z"/></svg> Upload icon<input type="file" name="icon_image" accept="image/*" style="display:none" onchange="previewIconImg(event)"></label>
          <?php if (!empty($comm['icon_image'])): ?><label style="font-size:12px;color:#888;cursor:pointer"><input type="checkbox" name="clear_icon_image" value="1"> Remove</label><?php endif; ?>
        </div>
      </div>
      <div class="hz-modal-footer" style="margin-top:20px;padding-top:16px;border-top:1px solid var(--border)">
        <a href="?page=community&id=<?= $cid ?>" class="gbtn gbtn-outline">Cancel</a>
        <button class="gbtn gbtn-blue" type="submit">Save Changes</button>
      </div>
    </form>
  </div>
</div>

<?php elseif ($page === 'messages'):
  requireLogin();
  $withId   = (int)($_GET['with'] ?? 0);
  $withUser = $withId ? getDB()->query("SELECT * FROM users WHERE id=$withId")->fetch() : null;

  if ($withUser) {
    getDB()->prepare("UPDATE messages SET is_read=1 WHERE to_id=? AND from_id=?")->execute([$u['id'], $withId]);
  }

  // people we've had convos with
  $convos = getDB()->prepare("SELECT DISTINCT u.id, u.display_name, u.avatar, u.tagline, MAX(m.created_at) last_msg,
    (SELECT COUNT(*) FROM messages WHERE to_id=? AND from_id=u.id AND is_read=0) unread
    FROM messages m JOIN users u ON (u.id = CASE WHEN m.from_id=? THEN m.to_id ELSE m.from_id END)
    WHERE m.from_id=? OR m.to_id=?
    GROUP BY u.id ORDER BY last_msg DESC LIMIT 20");
  $convos->execute([$u['id'], $u['id'], $u['id'], $u['id']]);
  $convos = $convos->fetchAll();

  if ($withUser) {
    $msgs = getDB()->prepare("SELECT * FROM messages WHERE (from_id=? AND to_id=?) OR (from_id=? AND to_id=?) ORDER BY created_at ASC");
    $msgs->execute([$u['id'], $withId, $withId, $u['id']]);
    $msgs = $msgs->fetchAll();
  } else {
    $msgs = [];
  }
?>
<div id="wrap">
  <div class="gcard msg-layout">
    <div>
      <div class="conv-list-header">Messages</div>
      <?php foreach ($convos as $cv): ?>
      <a class="conv-item <?= $withId==$cv['id']?'active':'' ?>" href="?page=messages&with=<?= $cv['id'] ?>">
        <img src="<?= avatarSrc($cv['avatar'], $cv['display_name']) ?>" class="conv-av">
        <div class="conv-name"><?= h($cv['display_name']) ?></div>
        <?php if ($cv['unread']): ?><span class="tnb" style="position:static"><?= $cv['unread'] ?></span><?php endif; ?>
      </a>
      <?php endforeach; ?>
      <?php if (empty($convos)): ?><div style="padding:20px;font-size:13px;color:var(--sub);text-align:center">No messages yet.</div><?php endif; ?>
    </div>
    <?php if ($withUser): ?>
    <div>
      <div class="chat-header">
        <img src="<?= avatarSrc($withUser['avatar'], $withUser['display_name']) ?>" class="av32">
        <a href="?page=profile&id=<?= $withUser['id'] ?>" style="font-size:14px;font-weight:500;color:#212121"><?= h($withUser['display_name']) ?></a>
      </div>
      <div class="chat-window" id="chatWin">
        <?php foreach ($msgs as $m): $isMe = ($m['from_id'] == $u['id']); ?>
        <div style="display:flex;flex-direction:column;align-items:<?= $isMe?'flex-end':'flex-start' ?>">
          <div class="bubble <?= $isMe?'bubble-me':'bubble-them' ?>"><?= h($m['content']) ?></div>
          <div class="bubble-time" style="padding:0 6px"><?= timeAgo($m['created_at']) ?></div>
        </div>
        <?php endforeach; ?>
        <?php if (empty($msgs)): ?><div style="margin:auto;font-size:13px;color:var(--sub)">Start the conversation!</div><?php endif; ?>
      </div>
      <form method="post" class="chat-input-row">
        <input type="hidden" name="action" value="send_message">
        <input type="hidden" name="to_id" value="<?= $withId ?>">
        <input type="text" name="content" placeholder="Message <?= h($withUser['display_name']) ?>…" autocomplete="off" required autofocus>
        <button class="gbtn gbtn-blue gbtn-sm">Send</button>
      </form>
    </div>
    <?php else: ?>
    <div style="display:flex;align-items:center;justify-content:center;color:var(--sub);font-size:14px">Select a conversation, or go to someone's profile to start one.</div>
    <?php endif; ?>
  </div>
</div>
<script>
// scroll chat to bottom on load
var cw = document.getElementById('chatWin');
if (cw) cw.scrollTop = cw.scrollHeight;
</script>

<?php elseif ($page === 'notifications'):
  requireLogin();
  $notifs = getDB()->prepare("SELECT n.*,u.display_name,u.avatar FROM notifications n JOIN users u ON u.id=n.from_user_id WHERE n.user_id=? ORDER BY n.created_at DESC LIMIT 60");
  $notifs->execute([$u['id']]);
  $notifs = $notifs->fetchAll();
?>
<div id="wrap" style="max-width:640px;margin:0 auto">
  <div class="page-head">
    <h2>&#128276; Notifications</h2>
    <form method="post"><input type="hidden" name="action" value="mark_notifs"><button class="gbtn gbtn-outline gbtn-sm">Mark all read</button></form>
  </div>
  <div class="gcard">
    <?php foreach ($notifs as $n): ?>
    <div class="notif-item <?= $n['is_read']?'':'unread' ?>">
      <a href="?page=profile&id=<?= $n['from_user_id'] ?>"><img src="<?= avatarSrc($n['avatar'], $n['display_name']) ?>" class="notif-av"></a>
      <div class="notif-msg"><?= h($n['message']) ?></div>
      <div class="notif-time"><?= timeAgo($n['created_at']) ?></div>
    </div>
    <?php endforeach; ?>
    <?php if (empty($notifs)): ?><div class="empty-state"><p>No notifications yet.</p></div><?php endif; ?>
  </div>
</div>

<?php elseif ($page === 'admin'):
  requireAdmin();
  $adminTab = $_GET['tab'] ?? 'users';
  $stats = [
    'users'   => getDB()->query("SELECT COUNT(*) FROM users")->fetchColumn(),
    'posts'   => getDB()->query("SELECT COUNT(*) FROM posts")->fetchColumn(),
    'comms'   => getDB()->query("SELECT COUNT(*) FROM communities")->fetchColumn(),
    'online'  => getDB()->query("SELECT COUNT(*) FROM users WHERE last_login > datetime('now','-1 hour')")->fetchColumn(),
  ];
  $users = getDB()->query("SELECT * FROM users ORDER BY id DESC")->fetchAll();
?>
<div id="wrap" style="max-width:900px;margin:0 auto">
  <h2 style="margin:0 0 12px;font-size:22px;font-weight:400">&#128737; Admin Panel</h2>
  <?php if ($error): ?><div class="admin-err"><?= h($error) ?></div><?php endif; ?>
  <?php if ($success): ?><div class="admin-success"><?= h($success) ?></div><?php endif; ?>

  <div class="stat-row">
    <div class="sbox"><div class="n"><?= $stats['users'] ?></div><div class="l">Users</div></div>
    <div class="sbox"><div class="n"><?= $stats['posts'] ?></div><div class="l">Posts</div></div>
    <div class="sbox"><div class="n"><?= $stats['comms'] ?></div><div class="l">Communities</div></div>
    <div class="sbox"><div class="n" style="color:var(--ggreen)"><?= $stats['online'] ?></div><div class="l">Active (1h)</div></div>
  </div>

  <div class="admin-tabs">
    <a href="?page=admin&tab=users"    class="admin-tab <?= $adminTab==='users'?'active':'' ?>">Users</a>
    <a href="?page=admin&tab=settings" class="admin-tab <?= $adminTab==='settings'?'active':'' ?>">Settings</a>
    <a href="?page=admin&tab=create"   class="admin-tab <?= $adminTab==='create'?'active':'' ?>">Create User</a>
  </div>

  <?php if ($adminTab === 'users'): ?>
  <div class="gcard">
    <table class="admin-table">
      <thead><tr><th>User</th><th>Role</th><th>Last Login</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody>
      <?php foreach ($users as $tu):
        $susp = isUserSuspended($tu);
      ?>
      <tr>
        <td>
          <div style="display:flex;align-items:center;gap:9px">
            <img src="<?= avatarSrc($tu['avatar'], $tu['display_name']) ?>" class="av32">
            <div>
              <a href="?page=profile&id=<?= $tu['id'] ?>"><?= h($tu['display_name']) ?></a>
              <?= userBadges($tu) ?>
              <div style="font-size:11px;color:var(--sub)">@<?= h($tu['username']) ?> &middot; <?= h($tu['email']) ?></div>
            </div>
          </div>
        </td>
        <td><span class="role-badge <?= $tu['role']==='admin'?'rb-admin':'rb-user' ?>"><?= $tu['role'] ?></span></td>
        <td style="font-size:12px;color:var(--sub)"><?= $tu['last_login'] ? timeAgo($tu['last_login']) : 'Never' ?></td>
        <td>
          <?php if ($susp): ?>
          <span style="font-size:11px;background:#fce8e6;color:#c5221f;border-radius:10px;padding:2px 8px;font-weight:700">Suspended</span>
          <?php if ($tu['suspended_until']): ?><div style="font-size:10px;color:var(--sub);margin-top:2px">Until <?= date('M j', strtotime($tu['suspended_until'])) ?></div><?php endif; ?>
          <?php else: ?>
          <span style="font-size:11px;background:#e6f4ea;color:#137333;border-radius:10px;padding:2px 8px;font-weight:700">Active</span>
          <?php endif; ?>
        </td>
        <td>
          <div style="display:flex;gap:5px;flex-wrap:wrap">
            <?php if ($tu['id'] != $u['id']): ?>
            <form method="post" style="display:inline"><input type="hidden" name="action" value="admin_toggle_role"><input type="hidden" name="user_id" value="<?= $tu['id'] ?>"><button class="gbtn gbtn-outline gbtn-sm" style="font-size:11px"><?= $tu['role']==='admin'?'Demote':'Make Admin' ?></button></form>
            <?php if ($susp): ?>
            <form method="post" style="display:inline"><input type="hidden" name="action" value="admin_unsuspend_user"><input type="hidden" name="user_id" value="<?= $tu['id'] ?>"><button class="gbtn gbtn-outline gbtn-sm" style="font-size:11px;background:#e6f4ea;color:#137333;border-color:#0f9d58">Unsuspend</button></form>
            <?php else: ?>
            <button class="gbtn gbtn-sm" style="font-size:11px;background:#fff3e0;color:#e65100;border:1px solid #ffb74d" onclick="openSuspendForm(<?= $tu['id'] ?>)">Suspend</button>
            <?php endif; ?>
            <form method="post" style="display:inline" onsubmit="return confirm('Delete <?= h(addslashes($tu['display_name'])) ?>?')"><input type="hidden" name="action" value="admin_delete_user"><input type="hidden" name="user_id" value="<?= $tu['id'] ?>"><button class="gbtn gbtn-red gbtn-sm" style="font-size:11px">Delete</button></form>
            <?php else: ?>
            <span style="font-size:11px;color:var(--sub)">You</span>
            <?php endif; ?>
          </div>
          <div class="suspend-form-wrap" id="suspend-form-<?= $tu['id'] ?>">
            <form method="post">
              <input type="hidden" name="action" value="admin_suspend_user">
              <input type="hidden" name="user_id" value="<?= $tu['id'] ?>">
              <div style="margin-bottom:8px"><input type="text" name="suspend_reason" placeholder="Reason for suspension…" style="width:100%;padding:6px 9px;border:1px solid #ddd;border-radius:2px;font-size:12px;outline:none" required></div>
              <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
                <select name="suspend_type" id="stype-<?= $tu['id'] ?>" onchange="document.getElementById('sdays-<?= $tu['id'] ?>').style.display=this.value==='temporary'?'block':'none'" style="font-size:12px;padding:5px;border:1px solid #ddd;border-radius:2px">
                  <option value="permanent">Permanent</option>
                  <option value="temporary">Temporary</option>
                </select>
                <input type="number" name="suspend_days" id="sdays-<?= $tu['id'] ?>" value="3" min="1" max="365" style="width:70px;padding:5px;border:1px solid #ddd;border-radius:2px;font-size:12px;display:none"> days
                <button class="gbtn gbtn-red gbtn-sm" type="submit">Confirm</button>
                <button type="button" class="gbtn gbtn-outline gbtn-sm" onclick="document.getElementById('suspend-form-<?= $tu['id'] ?>').classList.remove('open')">Cancel</button>
              </div>
            </form>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <?php elseif ($adminTab === 'settings'): ?>
  <div class="gcard" style="padding:24px">
    <div class="ea-panel <?= $earlyAccess?'ea-panel-on':'' ?>" style="margin-bottom:20px">
      <div class="ea-header">
        <div><div class="ea-title">&#9889; Early Access Mode</div><div class="ea-desc">When on, new registrations are disabled. Useful while the site is growing.</div></div>
        <span class="ea-status <?= $earlyAccess?'ea-on':'ea-off' ?>"><?= $earlyAccess?'ON':'OFF' ?></span>
        <form method="post"><input type="hidden" name="action" value="admin_toggle_early_access"><button class="gbtn <?= $earlyAccess?'gbtn-outline':'gbtn-gold' ?> gbtn-sm"><?= $earlyAccess?'Disable':'Enable' ?></button></form>
      </div>
    </div>
    <div class="maint-panel <?= $maintenanceMode?'maint-panel-on':'' ?>" style="margin-bottom:20px">
      <div class="ea-header">
        <div><div class="maint-title">&#128737; Maintenance Mode</div><div class="maint-desc">When on, only admins can access the site. Everyone else sees a holding page.</div></div>
        <span class="ea-status <?= $maintenanceMode?'maint-on':'maint-off' ?>"><?= $maintenanceMode?'ON':'OFF' ?></span>
        <form method="post"><input type="hidden" name="action" value="admin_toggle_maintenance"><button class="gbtn <?= $maintenanceMode?'gbtn-outline':'gbtn-red' ?> gbtn-sm"><?= $maintenanceMode?'Disable':'Enable' ?></button></form>
      </div>
    </div>
    <h3 style="font-size:15px;font-weight:500;margin:0 0 16px">Other Actions</h3>
    <form method="post" onsubmit="return confirm('Clear all base64 images from the database?')"><input type="hidden" name="action" value="admin_clear_base64"><button class="gbtn gbtn-outline gbtn-sm">Clear Old Base64 Images</button></form>
  </div>

  <?php elseif ($adminTab === 'create'): ?>
  <div class="gcard" style="padding:24px;max-width:480px">
    <h3 style="margin:0 0 20px;font-size:16px;font-weight:500">Create a User Account</h3>
    <form method="post">
      <input type="hidden" name="action" value="admin_create_user">
      <div class="form-group"><label>Display Name</label><input type="text" name="display_name" required></div>
      <div class="form-group"><label>Username</label><input type="text" name="username" pattern="[a-zA-Z0-9_]{3,}" required></div>
      <div class="form-group"><label>Email</label><input type="email" name="email" required></div>
      <div class="form-group"><label>Password</label><input type="password" name="password" minlength="6" required></div>
      <button class="gbtn gbtn-blue" type="submit">Create User</button>
    </form>
  </div>
  <?php endif; ?>
</div>

<?php else: // anything unrecognised goes to home ?>
<?php redirect('?page=home'); ?>
<?php endif; // end page routing ?>

<div id="ripples-modal-bg" class="ripples-modal-bg">
  <div class="ripples-modal">
    <div class="ripples-modal-header">
      <h3>&#9711; Ripples <span id="ripplesTitle" style="font-size:13px;opacity:.5;margin-left:6px"></span></h3>
      <button class="ripples-close" onclick="closeRipples()">&#215;</button>
    </div>
    <div class="ripples-canvas-wrap"><canvas id="ripplesCanvas"></canvas></div>
    <div class="ripples-footer"><span class="ripples-stat" id="ripplesStat">Loading…</span><span style="font-size:11px;opacity:.3">Drag to rotate</span></div>
  </div>
</div>

<?php if (!in_array($page, ['login','register','suspended','circles'])): ?>
<footer id="site-footer">
  <div>
    <a href="?page=about"><?= SITE_NAME ?></a> &middot;
    <a href="?page=tos">Terms</a> &middot;
    <a href="?page=privacy">Privacy</a> &middot;
    <a href="?page=guidelines">Guidelines</a> &middot;
    <a href="<?= GITHUB_URL ?>" target="_blank" class="oss-badge" style="vertical-align:middle">&#128196; GitHub</a>
  </div>
  <div>v<?= VERSION ?> &middot; MIT License &middot; Open Source</div>
</footer>
<?php endif; ?>

<script>
// ---- global JS ----

function plusOne(pid, btn) {
  fetch(location.pathname, {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: 'action=plusone&post_id=' + pid
  }).then(r => r.json()).then(d => {
    btn.querySelector('.poc').textContent = d.count;
    btn.classList.toggle('plusoned');
  });
}

function toggleCmts(pid) {
  var el = document.getElementById('cmts-' + pid);
  if (el) el.style.display = el.style.display === 'none' ? 'block' : 'none';
}

function toggleFollow(uid, btn) {
  fetch(location.pathname, {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: 'action=toggle_follow&followee_id=' + uid
  }).then(r => r.json()).then(d => {
    if (d.ok) btn.textContent = d.following ? 'Unfollow' : 'Follow';
  });
}

// reshare popover
var _rsOpen = null;
function toggleResharePopover(pid, btn) {
  if (_rsOpen && _rsOpen.pid === pid) { closeResharePopovers(); return; }
  closeResharePopovers();
  var pop = document.createElement('div');
  pop.className = 'reshare-popover';
  pop.innerHTML = '<div style="font-size:13px;font-weight:500;margin-bottom:8px;color:#333">Add a comment (optional)</div>'
    + '<textarea id="rsComment-'+pid+'" placeholder="Say something about this…" rows="3"></textarea>'
    + '<div class="reshare-popover-actions">'
    + '<button class="gbtn gbtn-outline gbtn-sm" onclick="closeResharePopovers()">Cancel</button>'
    + '<button class="gbtn gbtn-blue gbtn-sm" onclick="doReshare('+pid+')">Reshare</button>'
    + '</div>';
  btn.parentElement.appendChild(pop);
  _rsOpen = {pid: pid, pop: pop};
  setTimeout(() => pop.querySelector('textarea').focus(), 50);
}

function closeResharePopovers() {
  if (_rsOpen) { _rsOpen.pop.remove(); _rsOpen = null; }
}

function doReshare(pid) {
  var comment = (document.getElementById('rsComment-'+pid) || {}).value || '';
  fetch(location.pathname, {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: 'action=reshare&post_id='+pid+'&comment='+encodeURIComponent(comment)
  }).then(r => r.json()).then(d => {
    if (d.ok) {
      var cnt = document.getElementById('rsc-'+pid);
      if (cnt) cnt.textContent = d.count > 0 ? d.count : '';
      var rsBtn = document.getElementById('rsBtn-'+pid);
      if (rsBtn) rsBtn.classList.toggle('reshared', !d.undone);
      closeResharePopovers();
    }
  });
}

document.addEventListener('click', e => {
  if (_rsOpen && !e.target.closest('.reshare-popover') && !e.target.closest('.pbar-btn')) closeResharePopovers();
});

// ripples visualizer
var _ripplesData = null;
function openRipples(pid) {
  document.getElementById('ripples-modal-bg').classList.add('open');
  document.getElementById('ripplesStat').textContent = 'Loading…';
  document.getElementById('ripplesTitle').textContent = '';
  fetch(location.pathname + '?action=ripples&post_id=' + pid, {method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:'action=ripples'})
    .then(r => r.json()).then(data => {
      _ripplesData = data;
      document.getElementById('ripplesStat').textContent = data.total + ' reshare' + (data.total!==1?'s':'');
      drawRipples(data);
    }).catch(() => document.getElementById('ripplesStat').textContent = 'Could not load');
  // actually use GET
  fetch('?action=ripples&post_id=' + pid)
    .then(r => r.json()).then(data => {
      _ripplesData = data;
      document.getElementById('ripplesStat').textContent = data.total + ' reshare' + (data.total!==1?'s':'');
      drawRipples(data);
    }).catch(() => {});
}

function closeRipples() { document.getElementById('ripples-modal-bg').classList.remove('open'); }

function drawRipples(data) {
  var canvas = document.getElementById('ripplesCanvas');
  var wrap   = canvas.parentElement;
  canvas.width  = wrap.clientWidth  || 680;
  canvas.height = wrap.clientHeight || 360;
  var ctx = canvas.getContext('2d');
  ctx.clearRect(0, 0, canvas.width, canvas.height);

  if (!data.nodes || data.nodes.length === 0) {
    ctx.fillStyle = 'rgba(255,255,255,.3)';
    ctx.font = '14px Roboto,sans-serif';
    ctx.textAlign = 'center';
    ctx.fillText('No reshares yet', canvas.width/2, canvas.height/2);
    return;
  }

  var cx = canvas.width/2, cy = canvas.height/2;
  var r1 = 40, r2 = Math.min(cx, cy) - 60;

  // draw rings
  [r2, r2*0.6, r2*0.3].forEach((r, i) => {
    ctx.beginPath();
    ctx.arc(cx, cy, r, 0, Math.PI*2);
    ctx.strokeStyle = 'rgba(255,255,255,' + (0.06 + i*0.04) + ')';
    ctx.lineWidth = 1;
    ctx.stroke();
  });

  // root node
  var root = data.nodes[0];
  ctx.beginPath();
  ctx.arc(cx, cy, r1, 0, Math.PI*2);
  ctx.fillStyle = '#4285f4';
  ctx.fill();
  ctx.fillStyle = '#fff';
  ctx.font = 'bold 13px Roboto,sans-serif';
  ctx.textAlign = 'center';
  ctx.textBaseline = 'middle';
  ctx.fillText((root.name||'?')[0].toUpperCase(), cx, cy);

  // reshare nodes
  data.nodes.slice(1).forEach(function(node, i) {
    var angle  = (2*Math.PI * i / Math.max(data.nodes.length-1, 1)) - Math.PI/2;
    var nx     = cx + r2 * Math.cos(angle);
    var ny     = cy + r2 * Math.sin(angle);
    // connector
    ctx.beginPath();
    ctx.moveTo(cx, cy);
    ctx.lineTo(nx, ny);
    ctx.strokeStyle = 'rgba(255,255,255,.12)';
    ctx.lineWidth = 1;
    ctx.stroke();
    // dot
    ctx.beginPath();
    ctx.arc(nx, ny, 22, 0, Math.PI*2);
    ctx.fillStyle = '#dd4b39';
    ctx.fill();
    ctx.fillStyle = '#fff';
    ctx.font = 'bold 11px Roboto,sans-serif';
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';
    ctx.fillText((node.name||'?')[0].toUpperCase(), nx, ny);
  });
}

// icon/color pickers for create/edit community modals
function selectIcon(ico, el) {
  document.querySelectorAll('.icon-opt').forEach(e => e.classList.remove('sel'));
  el.classList.add('sel');
  var inp = document.getElementById('iconVal');
  if (inp) inp.value = ico;
}
function selectCommColor(col, el) {
  document.querySelectorAll('.cswatch').forEach(e => e.style.borderColor = 'transparent');
  el.style.borderColor = '#333';
  var inp = document.getElementById('commColorVal');
  if (inp) inp.value = col;
}
function previewIconImg(e) {
  var file = e.target.files[0];
  if (!file) return;
  var reader = new FileReader();
  reader.onload = function(ev) {
    var prev = document.getElementById('iconImgPreview');
    if (prev) prev.innerHTML = '<img src="'+ev.target.result+'" style="width:100%;height:100%;object-fit:cover;display:block;border-radius:50%">';
  };
  reader.readAsDataURL(file);
}
function openSuspendForm(uid) {
  document.getElementById('suspend-form-' + uid).classList.toggle('open');
}

// image preview in composer
var imgPick = document.getElementById('imgPick');
var imgLabel = document.getElementById('imgLabel');
if (imgPick && imgLabel) {
  imgPick.addEventListener('change', function() {
    imgLabel.textContent = this.files[0] ? this.files[0].name : '';
  });
}
</script>
</body>
</html>
