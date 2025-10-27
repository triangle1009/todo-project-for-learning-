<?php
// api/stats.php - 統計 API

header("Access-Control-Allow-Origin: *"); //CORS（跨域資源共享）設定
header("Content-Type: application/json; charset=UTF-8");//回傳的是 JSON 格式，編碼是 UTF-8
header("Access-Control-Allow-Methods: GET"); //這個 API 只接受 GET 請求

require_once __DIR__ . '/../config/database.php'; //載入資料庫連線

// 建立資料庫連線
$database = new Database(); 
$conn = $database->getConnection();

//檢查連線是否成功
if (!$conn) {
    http_response_code(500);
    echo json_encode(array(
        "success" => false,
        "message" => "資料庫連線失敗"
    ));
    exit;
}

try {
    // 1. 總任務數
    $query = "SELECT COUNT(*) as total FROM tasks";
    $stmt = $conn->prepare($query);//防止 SQL 注入攻擊 的安全機制
    $stmt->execute(); //執行 SQL 查詢
    $total = $stmt->fetch(PDO::FETCH_ASSOC);
    $total = $total['total'];
    
    // 2. 各狀態的任務數
    $query = "SELECT status, COUNT(*) as count FROM tasks GROUP BY status";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $statusCounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // 轉換成更好讀的格式
    $statusStats = array(
        'pending' => 0,
        'in_progress' => 0,
        'completed' => 0
    );
    
    foreach ($statusCounts as $row) {
        $statusStats[$row['status']] = (int)$row['count'];
    }
    
    // 3. 各優先級的任務數
    $query = "SELECT priority, COUNT(*) as count FROM tasks GROUP BY priority";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $priorityCounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $priorityStats = array(
        'low' => 0,
        'medium' => 0,
        'high' => 0
    );
    
    foreach ($priorityCounts as $row) {
        $priorityStats[$row['priority']] = (int)$row['count'];
    }
    
    // 4. 計算完成率
    $completedRate = 0;
    if ($total > 0) {
        $completedRate = round(($statusStats['completed'] / $total) * 100, 2);
    }
    
    // 5. 最近建立的任務
    $query = "SELECT id, title, created_at FROM tasks ORDER BY created_at DESC LIMIT 5";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $recentTasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // 回傳結果
    http_response_code(200);
    echo json_encode(array(
        "success" => true,
        "data" => array(
            "total" => (int)$total,
            "statusStats" => $statusStats,
            "priorityStats" => $priorityStats,
            "completedRate" => $completedRate,
            "recentTasks" => $recentTasks
        )
    ), JSON_UNESCAPED_UNICODE); //確保中文字正常顯示
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(array(
        "success" => false,
        "message" => "查詢失敗: " . $e->getMessage()
    ));
}
?>