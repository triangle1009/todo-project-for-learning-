-- database/schema.sql
-- 待辦事項管理系統 - 資料庫結構
-- 建立日期：2025-10-23

-- 建立資料庫
CREATE DATABASE IF NOT EXISTS todo_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE todo_app;

-- 任務表
CREATE TABLE IF NOT EXISTS tasks (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT '任務 ID',
    title VARCHAR(255) NOT NULL COMMENT '任務標題',
    description TEXT COMMENT '任務描述',
    status ENUM('pending', 'in_progress', 'completed') DEFAULT 'pending' COMMENT '任務狀態',
    priority ENUM('low', 'medium', 'high') DEFAULT 'medium' COMMENT '優先級',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '建立時間',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新時間',
    
    -- 索引（提升查詢效能）
    INDEX idx_status (status),
    INDEX idx_priority (priority),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='待辦任務表';

-- 插入初始測試資料
INSERT INTO tasks (title, description, status, priority) VALUES
('學習 Node.js', '完成 Node.js API 開發', 'in_progress', 'high'),
('學習 Vue.js', '建立 Vue.js 前端', 'pending', 'high'),
('學習 PHP', '建立 PHP 管理後台', 'pending', 'medium')
ON DUPLICATE KEY UPDATE title=title;  -- 避免重複插入