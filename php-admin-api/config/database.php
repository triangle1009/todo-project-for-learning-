<?php
require_once __DIR__ . '/../vendor/autoload.php'; // 載入 Composer 的自動載入器

use Dotenv\Dotenv; 

// 載入 .env
$dotenv = Dotenv::createImmutable(__DIR__ . '/..'); // 建立一個不可改變Dotenv 物件
$dotenv->load();    //載入 .env 檔案，把裡面的變數放進 $_ENV

class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $port;
    public $conn;
    public function __construct(){
        $this->host = $_ENV['DB_HOST'];
        $this->db_name = $_ENV['DB_NAME'];
        $this->username = $_ENV['DB_USER'];
        $this->password = $_ENV['DB_PASSWORD'];
        $this->port = $_ENV['DB_PORT'];
    }
    public function getConnection() {
        $this ->conn = null;
        try {
            $this->conn = new PDO(  //php連接資料庫的標準模式
           "mysql:host=" . $this ->host.
                ";port=" . $this ->port.
                ";dbname=" . $this -> db_name,
                $this ->username,
                $this ->password
            );
            $this ->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //設定PDO錯誤屬性的處理
            $this->conn->exec("SET NAMES utf8mb4"); //支援所有 Unicode 字元
        }catch(PDOException $exception) {
            echo"連線錯誤".$exception->getMessage()."";
        }
        return $this ->conn;
    }
}
?>