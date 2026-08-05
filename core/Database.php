<?php
// core/Database.php

/**
 * فئة الاتصال بقاعدة البيانات - نمط Singleton
 * تضمن اتصال واحد فقط خلال دورة حياة التطبيق
 */
class Database {
    
    /** @var Database|null مثيل واحد من الفئة */
    private static ?Database $instance = null;
    
    /** @var PDO|null اتصال PDO */
    private ?PDO $pdo = null;
    
    /** @var PDOStatement|null الاستعلام الحالي */
    private ?PDOStatement $stmt = null;
    
    /** @var string آخر خطأ حدث */
    private string $lastError = '';
    
    /**
     * منع إنشاء مثيل مباشر - استخدام getInstance()
     */
    private function __construct() {
        $this->connect();
    }
    
    /**
     * الحصول على المثيل الوحيد
     */
    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * إنشاء اتصال بقاعدة البيانات
     */
    private function connect(): void {
        try {
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=utf8mb4',
                DB_HOST,
                DB_NAME
            );
            
            $options = [
                PDO::ATTR_PERSISTENT         => true,
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE   => PDO::FETCH_OBJ,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND  => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ];
            
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            
        } catch (PDOException $e) {
            $this->lastError = $e->getMessage();
            
            if (APP_ENV === 'development') {
                die('<h3>خطأ في الاتصال بقاعدة البيانات</h3>' .
                     '<p>' . htmlspecialchars($e->getMessage()) . '</p>' .
                     '<p>تأكد من صحة الإعدادات في ملف config/config.php</p>');
            } else {
                error_log('Database connection failed: ' . $e->getMessage());
                die('حدث خطأ في النظام. يرجى المحاولة لاحقاً.');
            }
        }
    }
    
    /**
     * تحضير استعلام SQL
     */
    public function query(string $sql): self {
        $this->stmt = $this->pdo->prepare($sql);
        return $this;
    }
    
    /**
     * ربط قيمة بمعامل
     */
    public function bind(string $param, $value, ?int $type = null): self {
        if ($type === null) {
            $type = match (true) {
                is_int($value)    => PDO::PARAM_INT,
                is_bool($value)   => PDO::PARAM_BOOL,
                is_null($value)   => PDO::PARAM_NULL,
                default           => PDO::PARAM_STR,
            };
        }
        $this->stmt->bindValue($param, $value, $type);
        return $this;
    }
    
    /**
     * تنفيذ الاستعلام
     */
    public function execute(): bool {
        return $this->stmt->execute();
    }
    
    /**
     * جلب جميع النتائج كمصفوف كائنات
     */
    public function resultSet(): array {
        $this->execute();
        return $this->stmt->fetchAll();
    }
    
    /**
     * جلب صف واحد ككائن
     */
    public function single(): ?object {
        $this->execute();
        $result = $this->stmt->fetch();
        return $result ?: null;
    }
    
    /**
     * جلب عدد الصفوف المتأثرة
     */
    public function rowCount(): int {
        return $this->stmt->rowCount();
    }
    
    /**
     * جلب آخر ID تم إدخاله
     */
    public function lastInsertId(): string {
        return $this->pdo->lastInsertId();
    }
    
    /**
     * بدء معاملة قاعدة بيانات
     */
    public function beginTransaction(): void {
        $this->pdo->beginTransaction();
    }
    
    /**
     * تأكيد المعاملة
     */
    public function commit(): void {
        $this->pdo->commit();
    }
    
    /**
     * التراجع عن المعاملة
     */
    public function rollBack(): void {
        $this->pdo->rollBack();
    }
    
    /**
     * جلب آخر خطأ
     */
    public function getLastError(): string {
        return $this->lastError;
    }
    
    /**
     * التحقق من وجود الاتصال
     */
    public function isConnected(): bool {
        return $this->pdo !== null;
    }
    
    /**
     * منع نسخ الكائن
     */
    private function __clone() {}
    
    /**
     * منع إلغاء التسلسل
     */
    public function __wakeup() {
        throw new \Exception('Cannot unserialize singleton');
    }
}