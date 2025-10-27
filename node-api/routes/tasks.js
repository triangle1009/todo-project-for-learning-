// routes/tasks.js - 任務路由       抓取SQL的資料
const express = require('express'); //引入express框架
const router = express.Router();    //建立一個路由
const db = require('../db');        //引入我們之前寫的資料庫連線

// 取得所有任務
router.get('/',async(req,res)=>{ //讀取資料
    try {
        const [tasks] = await db.query('SELECT * FROM tasks ORDER BY created_at DESC');
        res.json({
            success:true,
            data:tasks
        });
    }catch (error){
        console.error('取的任務失敗:', error);
        res.status(500).json({
            success: false,
            message: '取得任務失敗'
        });
    }
});
router.post('/',async(req,res)=> { //新增資料
    try {
        const {title, description, priority} = req.body;

        if (!title){
            return res.status(400).json({
                success: false,
                message: '標題為必填欄位'
            })
        }
        //插入資料庫
        const [result] = await db.query(
            'INSERT INTO tasks (title, description, priority) VALUES (?, ?, ?)',
            [title, description || null ,priority || 'medium']
        );
        res.status(201).json({
            success: true,
            message: '任務建立成功',
            data: {
                id:result.insertId,title,description,
                priority:priority || 'medium'
            }
        });
}   catch (error) {
        console.error('建立任務失敗:',error);
        res.status(500).json({
                    success: false,
                    message: '建立任務失敗'
            });
        }
});
router.put('/:id',async(req,res)=>{ // 處理put 請求
     try {
        const {id}=req.params;
        const { title, description, status, priority } =req.body;

        // 檢查任務是否存在
        const [existing] = await db.query('SELECT * FROM tasks WHERE id = ?',[id]);

        if (existing.length === 0){
            return res.status(404).json({
                success:false,
                message:'找不到任務'
            });
        }

     // 更新任務
        await db.query(
            'UPDATE tasks SET title = ?, description = ?, status = ?, priority = ? WHERE id = ?',
            [
                title || existing[0].title,
                description !== undefined ? description : existing[0].description,
                status || existing[0].status,
                priority || existing[0].priority,
                id
            ]
        );
        res.json({
            success: true,
            message:'任務更新成功'
        });
    }catch (error){
        console.error('更新任務失敗:',error);
        res.status(500).json({
            success:false,
            message:'任務更新失敗'
        });
    }
});

router.delete('/:id',async(req,res)=>{ // 刪除任務
    try {
        const{id}=req.params; 

        // 檢查任務是否存在
        const [existing] = await db.query('SELECT * FROM tasks WHERE id = ?',[id]);

        if (existing.length === 0){
            return res.status(404).json({
                success : false,
                message:'找不到該任務'
            })
        }
        await db.query('DELETE FROM tasks WHERE id = ?',[id]);

        res.json({
            success :true,
            message:"任務刪除成功"
        });
    }catch (error){
        console.error('刪除任務失敗:',error);
        res.status(500).json({
            success : false,
            message: '刪除任務失敗'
        })
    }
});
module.exports = router ;