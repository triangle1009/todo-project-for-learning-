<?php
// api/batch.php - 批次操作 API

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, DELETE, OPTIONS"); //允許 POST、DELETE、OPTIONS
header("Access-Control-Allow-Headers: Content-Type");   //允許跨域，我接受 POST/DELETE/OPTIONS 請求

// 處理 OPTIONS 請求（CORS 預檢）
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../config/database.php';

// 建立資料庫連線
$database = new Database();
$conn = $database->getConnection();

if (!$conn) {
    http_response_code(500);
    echo json_encode(array(
        "success" => false,
        "message" => "資料庫連線失敗"
    ));
    exit;
}

// 取得請求方法
$method = $_SERVER['REQUEST_METHOD'];

// 取得 JSON 資料
$data = json_decode(file_get_contents("php://input"), true);

try {
    // 批次刪除
    if ($method === 'DELETE') { // 檢查前端是否有傳遞「有效的 ID 陣列」，如果沒有就回傳錯誤
        if (!isset($data['ids']) || !is_array($data['ids']) || empty($data['ids'])) {
            http_response_code(400);
            echo json_encode(array(
                "success" => false,
                "message" => "請提供要刪除的任務 ID 陣列"
            ));
            exit;
        }
        // SQL 查詢
        $ids = $data['ids'];
        $placeholders = str_repeat('?,', count($ids) - 1) . '?'; //根據 ID 數量，動態生成對應數量的佔位符

        $query = "DELETE FROM tasks WHERE id IN ($placeholders)";//刪除 tasks 資料表中，id 在 (?, ?, ?) 裡面的任務，每個 ? 會被實際的 ID 替換
        $stmt = $conn->prepare($query);
        $stmt->execute($ids);
        
        $deletedCount = $stmt->rowCount();
        //回傳成功訊息和實際刪除的任務數量
        http_response_code(200);
        echo json_encode(array(
            "success" => true,
            "message" => "成功刪除 $deletedCount 個任務",
            "deletedCount" => $deletedCount
        ), JSON_UNESCAPED_UNICODE);
    }
    
    // 批次更新狀態
    else if ($method === 'POST') {
        $action = isset($data['action']) ? $data['action'] : ''; //取得前端指定的操作類型
        
        if ($action === 'updateStatus') { //檢查是否為更新狀態操作
            if (!isset($data['ids']) || !is_array($data['ids']) || empty($data['ids'])) {
                http_response_code(400);
                echo json_encode(array(
                    "success" => false,
                    "message" => "請提供要更新的任務 ID 陣列"
                ));
                exit;
            }
            
            if (!isset($data['status']) || !in_array($data['status'], array('pending', 'in_progress', 'completed'))) {
                http_response_code(400);
                echo json_encode(array(
                    "success" => false,
                    "message" => "請提供有效的狀態值"
                ));
                exit;
            }
            //準備 SQL 查詢
            $ids = $data['ids'];
            $status = $data['status'];
            $placeholders = str_repeat('?,', count($ids) - 1) . '?';
            
            $query = "UPDATE tasks SET status = ? WHERE id IN ($placeholders)";
            $params = array_merge(array($status), $ids); // 把 status 和 ID 陣列合併成一個參數陣列
            $stmt = $conn->prepare($query);
            $stmt->execute($params);
            
            $updatedCount = $stmt->rowCount();
            
            http_response_code(200);
            echo json_encode(array(
                "success" => true,
                "message" => "成功更新 $updatedCount 個任務",
                "updatedCount" => $updatedCount
            ), JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(400);
            echo json_encode(array(
                "success" => false,
                "message" => "未知的操作"
            ));
        }
    }
    
    else {
        http_response_code(405);
        echo json_encode(array(
            "success" => false,
            "message" => "不支援的請求方法"
        ));
    }
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(array(
        "success" => false,
        "message" => "操作失敗: " . $e->getMessage()
    ));
}
?>