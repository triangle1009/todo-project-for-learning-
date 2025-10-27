# 待辦事項管理系統

全端學習專案，包含 Vue.js (TypeScript)、Node.js、PHP 和 MySQL。

## 🎯 技術棧

- **前端**: Vue 3 + TypeScript + Composition API
- **後端 API**: Node.js + Express
- **管理後台**: PHP + PDO
- **資料庫**: MySQL
- **套件管理**: npm + Composer

## 📁 專案結構
```
todo-project/
├── node-api/          # Node.js REST API
├── vue-frontend/      # Vue.js 前端
├── php-admin-api/     # PHP 管理後台 API
└── database/          # 資料庫 Schema
```

## 🚀 快速開始

### 1. 資料庫設定
```bash
# 在 MySQL 中執行
mysql -u root -p < database/schema.sql
```

### 2. Node.js API
```bash
cd node-api
cp .env.example .env  # 並修改資料庫密碼
npm install
node server.js
# 運行在 http://localhost:3000
```

### 3. Vue.js 前端
```bash
cd vue-frontend
npm install
npm run dev
# 運行在 http://localhost:5173
```

### 4. PHP 管理後台
```bash
cd php-admin-api
cp .env.example .env  # 並修改資料庫密碼
composer install
php -S localhost:8000
# 運行在 http://localhost:8000
```

## 📚 API 端點

### Node.js API (port 3000)
- `GET /api/tasks` - 取得所有任務
- `POST /api/tasks` - 新增任務
- `PUT /api/tasks/:id` - 更新任務
- `DELETE /api/tasks/:id` - 刪除任務

### PHP 管理 API (port 8000)
- `GET /api/stats.php` - 統計資訊
- `POST /api/batch.php` - 批次更新
- `DELETE /api/batch.php` - 批次刪除

## 🔧 環境需求

- Node.js 16+
- PHP 8.0+
- MySQL 8.0+
- Composer

## 📝 授權

MIT