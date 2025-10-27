require('dotenv').config();  //讀取.env檔案
const mysql = require('mysql2')

// 建立資料庫連線池
const pool = mysql.createPool({
    host: process.env.DB_HOST,
    user: process.env.DB_USER,
    password: process.env.DB_PASSWORD,
    database: process.env.DB_NAME,
    port: process.env.DB_PORT,
    waitForConnections: true,
    connectionLimit: 10,
    queueLimit: 0
});

// 使用 Promise 版本
const promisePool = pool.promise();

module.exports = promisePool;