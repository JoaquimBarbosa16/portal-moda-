<?php
// ============================================================
//  VOGUE BR — Configuração MySQL
// ============================================================
define('SITE_NAME',    'VOGUE BR');
define('SITE_TAGLINE', 'Fashion · Cultura · Estilo');
define('DB_HOST',    getenv('DB_HOST')  ?: 'localhost');
define('DB_PORT',    getenv('DB_PORT')  ?: '3306');
define('DB_NAME',    getenv('DB_NAME')  ?: 'voguebr');
define('DB_USER',    getenv('DB_USER')  ?: 'root');
define('DB_PASS',    getenv('DB_PASS')  ?: '');
define('DB_CHARSET', 'utf8mb4');

class DB {
    private static ?PDO $pdo = null;
    public static function conn(): PDO {
        if (!self::$pdo) {
            $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', DB_HOST, DB_PORT, DB_NAME, DB_CHARSET);
            try {
                self::$pdo = new PDO($dsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $e) {
                error_log('[DB] '.$e->getMessage());
                http_response_code(500);
                die('<div style="font-family:sans-serif;padding:40px;color:#c00"><h2>Erro de conexão com o banco.</h2><p>Verifique as credenciais em <code>includes/config.php</code> e se o MySQL está rodando.</p></div>');
            }
        }
        return self::$pdo;
    }
    public static function query(string $sql, array $p=[]): array { $st=self::conn()->prepare($sql);$st->execute($p);return $st->fetchAll(); }
    public static function row(string $sql, array $p=[]): ?array { $st=self::conn()->prepare($sql);$st->execute($p);$r=$st->fetch();return $r?:null; }
    public static function scalar(string $sql, array $p=[]): mixed { $st=self::conn()->prepare($sql);$st->execute($p);return $st->fetchColumn(); }
    public static function exec(string $sql, array $p=[]): int { $st=self::conn()->prepare($sql);$st->execute($p);return $st->rowCount(); }
    public static function insert(string $sql, array $p=[]): int { $st=self::conn()->prepare($sql);$st->execute($p);return (int)self::conn()->lastInsertId(); }
}

function session_start_safe(): void { if(session_status()===PHP_SESSION_NONE){session_set_cookie_params(['lifetime'=>0,'path'=>'/','httponly'=>true,'samesite'=>'Lax']);session_start();} }
function current_user(): ?array { session_start_safe(); return $_SESSION['user']??null; }
function require_login(): void { if(!current_user()){header('Location: /index.php');exit;} }
function require_admin(): void { $u=current_user();if(!$u||$u['role']!=='admin'){header('Location: /dashboard.php');exit;} }
function is_admin(): bool { $u=current_user();return $u&&$u['role']==='admin'; }
function flash(string $type, string $msg): void { session_start_safe();$_SESSION['flash']=compact('type','msg'); }
function get_flash(): ?array { session_start_safe();if(!empty($_SESSION['flash'])){$f=$_SESSION['flash'];unset($_SESSION['flash']);return $f;}return null; }
function h(string $s): string { return htmlspecialchars($s,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8'); }
function slugify(string $t): string { $t=mb_strtolower($t,'UTF-8');$t=iconv('UTF-8','ASCII//TRANSLIT',$t);$t=preg_replace('/[^a-z0-9]+/','-',$t);return trim($t,'-'); }
function fmt_date(string $d): string { return (new DateTime($d))->format('d/m/Y'); }
function fmt_relative(string $d): string { $diff=time()-strtotime($d);return match(true){$diff<60=>'Agora mesmo',$diff<3600=>(int)($diff/60).' min atrás',$diff<86400=>(int)($diff/3600).'h atrás',$diff<604800=>(int)($diff/86400).' dias atrás',default=>fmt_date($d)}; }
