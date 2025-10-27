// server.js - 主程式
require('dotenv').config();            //請求env
const express = require('express');    //引入express框架
const cors = require('cors');          //引入CORS套件

const app = express();
const PORT = process.env.PORT || 3000;

// 中介軟體（Middleware）
app.use(cors()); // 允許跨域請求
app.use(express.json()); // 解析 JSON 格式的請求

// 測試路由
app.get('/',(req,res)=>{res.json({message:'歡迎使用待辦事項 API'})});

// 引入任務路由
const tasksRouter = require('./routes/tasks');

// 使用任務路由
app.use('/api/tasks', tasksRouter);

// 啟動伺服器
app.listen(PORT,()=>{console.log(`🚀 Node.js API 運行在 http://localhost:${PORT}`);
});