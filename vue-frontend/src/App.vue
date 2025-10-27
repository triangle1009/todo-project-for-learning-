<script setup lang="ts">  //使用typescript
import { ref, onMounted } from 'vue'  //從Vue 引入功能
import { getTasks,createTask, deleteTask, updateTask } from './api' //從api.ts引入api函式
import type { Task } from './types' // 從type.js引入類型

// 響應式資料
const tasks = ref<Task[]>([]) //讓資料變成響應式
const loading = ref(false)

// 載入任務列表
async function loadTasks() {
  loading.value = true
  try {
    const result = await getTasks()       //呼叫api
    if (result.success && result.data) {  //確認API與資料有無
      tasks.value = result.data
    }
  } catch (error) {
    console.error('載入失敗:', error)
  } finally {
    loading.value = false
  }
}

// 組件掛載時載入資料
onMounted(() => { loadTasks() }) //生命週期

// 新增任務的表單資料
const newTask = ref({
  title:'',
  description:'',
  priority:'medium' as 'low' | 'medium' | 'high'    //告訴 TypeScript：「priority 的值只能是這三個之一」
})

// 新增任務
async function addTask() {
  if (!newTask.value.title.trim()){
    alert('請輸入標題')
    return
  }
loading.value = true
try {
  const result = await createTask(newTask.value)
  if (result.success){
    //清空表單
    newTask.value = {
      title:'',
      description:'',
      priority:'medium'  
    }
    await loadTasks()//重新載入
  }
} catch (error) {
    console.error('新增失敗:',error)
    alert('新增失敗')
} finally{
    loading.value = false 
    }
}
// 刪除任務
async function removeTask(id:number) {    // 根據id來刪除任務
  if (!confirm('確定要刪除這個任務嗎?')){
    return 
  }
  loading.value =true

  try {
    const result = await deleteTask(id)
    if (result.success){
      await loadTasks()
    }
  }catch (error){
    console.error('刪除任務失敗:',error)
    alert('刪除失敗')
  }finally{
    loading.value = false
  }
}
//更新任務
async function toggleStatus(task:Task) { //定義狀態切換規則
  const statusMap = {
    'pending': 'in_progress',
    'in_progress': 'completed',
    'completed': 'pending'
  }
  const newStatus = statusMap[task.status] as Task['status'] //取得新狀態

  loading.value = true 
  try {
    const result = await updateTask(task.id,{ status: newStatus }) //更新 API 呼叫
    if (result.success){
      await loadTasks()
    }
  }catch(error) {
    console.error('更新失敗:',error)
    alert('更新失敗')
  }finally{
    loading.value = false 
  }
}
</script>
<template>
  <div class="app">
    <h1>📝 待辦事項管理</h1>
    <!-- 新增任務表單 -->
<div class="add-form">
  <h2>➕ 新增任務</h2>
  <input v-model="newTask.title" type="text" placeholder="任務標題" @keyup.enter="addTask"/> 
  <textarea v-model="newTask.description" placeholder="任務描述" rows="3"></textarea>
  <select v-model="newTask.priority">
    <option value="low">低優先級</option>
    <option value="medium">中優先級</option>
    <option value="high">高優先級</option>
  </select>
  <button @click="addTask" :disabled="loading">
    {{ loading ? '處理中...' : '新增任務' }}
  </button>
</div>
    <!-- 載入中 -->
    <div v-if="loading">載入中...</div>
    
    <!-- 任務列表 -->
    <div v-else class="task-list">
      <div v-for="task in tasks" :key="task.id" class="task-item">
        <!--**`:key` = 唯一識別碼**

        **為什麼需要？**
        - 讓 Vue 知道哪個元素是哪個
        - 提升效能
        - 避免渲染錯誤

        **比喻：**
        ```
        沒有 key：像是一排沒編號的學生
        有 key：像是每個學生有學號-->
        <div class="task-content">
          <h3>{{ task.title }}</h3>
          <p>{{ task.description }}</p>
          <div class="task-tags">
            <span 
              class="status" 
              :style="{
                background: task.status === 'completed' ? '#c8e6c9' : 
                            task.status === 'in_progress' ? '#fff9c4' : '#e3f2fd',
                color: task.status === 'completed' ? '#2e7d32' : 
                      task.status === 'in_progress' ? '#f57f17' : '#1976d2'
              }">
              {{ task.status === 'pending' ? '待處理' : 
                task.status === 'in_progress' ? '進行中' : 
                '已完成' }}
            </span>
            <span class="priority">{{ task.priority }}</span>
          </div>
        </div>
        <div class="task-actions">
          <button @click="toggleStatus(task)" class="btn-status">
            {{ task.status === 'pending' ? '⏳ 待處理' : 
              task.status === 'in_progress' ? '🔄 進行中' : 
              '✅ 已完成' }}
          </button> 
          <button @click="removeTask(task.id)" class="btn-delete">
            🗑️ 刪除
          </button>
        </div>
      </div>
  </div>
</div>
</template>

<style scoped>
.app {
  max-width: 800px;
  margin: 0 auto;
  padding: 20px;
}

h1 {
  color: #42b983;
  text-align: center;
}

.task-list {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.task-item {
  border: 1px solid #ddd;
  border-radius: 8px;
  padding: 15px;
  background: #f9f9f9;
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
}

.task-content {
  flex: 1;
}

.task-content h3 {
  margin: 0 0 10px 0;
  color: #333;
}

.task-content p {
  margin: 0 0 10px 0;
  color: #666;
}

.task-tags {
  display: flex;
  gap: 5px;
}

.task-actions {
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.btn-delete {
  padding: 8px 15px;
  background: #ff5252;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
}
.btn-status {
  padding: 8px 15px;
  background: #42b983;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
  white-space: nowrap;
}
.btn-status:hover {
  background: #369970;
}

.btn-delete:hover {
  background: #ff1744;
}

.task-item h3 {
  margin: 0 0 10px 0;
  color: #333;
}

.task-item p {
  margin: 0 0 10px 0;
  color: #666;
}

.status, .priority {
  display: inline-block;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  margin-right: 5px;
}

.status {
  display: inline-block;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  margin-right: 5px;
  background: #e3f2fd;
  color: #1976d2;
}
.priority {
  background: #fff3e0;
  color: #f57c00;
}
.add-form {
  background: #f0f9ff;
  border: 2px solid #42b983;
  border-radius: 8px;
  padding: 20px;
  margin-bottom: 30px;
}

.add-form h2 {
  margin: 0 0 15px 0;
  color: #42b983;
}

.add-form input,
.add-form textarea,
.add-form select {
  width: 100%;
  padding: 10px;
  margin-bottom: 10px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
  box-sizing: border-box;
}

.add-form button {
  width: 100%;
  padding: 12px;
  background: #42b983;
  color: white;
  border: none;
  border-radius: 4px;
  font-size: 16px;
  cursor: pointer;
}

.add-form button:hover {
  background: #369970;
}

.add-form button:disabled {
  background: #ccc;
  cursor: not-allowed;
}
</style>