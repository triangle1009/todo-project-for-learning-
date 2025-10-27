import type { Task,ApiResponse } from './types'; // 引入「類型定義」

const API_URL = 'http://localhost:3000/api/tasks'; //設定node-api

// 取得所有任務
export async function getTasks():Promise<ApiResponse<Task[]>>{ //取得資料並回傳陣列OK
    const response = await fetch(API_URL);
    return response.json();
}

//新增任務，且排除id、created_at、updated_at、status資料
export async function createTask(task: Omit<Task,'id'|'created_at'|'updated_at'|'status'>):Promise<ApiResponse<Task>>{
    const response = await fetch(API_URL,{ method:'POST',headers:{
        'Content-Type':'application/json'
    },
    body:JSON.stringify(task)
 });
    return response.json();
}

//更新任務
export async function updateTask(
    id:number,
    updates: Partial<Omit<Task,'id'|'created_at'|'updated_at'>> //利用 Partial 讓屬性變成可選
):Promise<ApiResponse<Task>>{
    const response = await fetch(`${API_URL}/${id}`, {
        method:'PUT',
        headers : {'Content-Type':'application/json'},
        body: JSON.stringify(updates)
    });
    return response.json();
}

//刪除任務
export async function deleteTask(id:number):Promise<ApiResponse<void>>{
    const response = await fetch(`${API_URL}/${id}`,{method:'DELETE'});
    return response.json()
}