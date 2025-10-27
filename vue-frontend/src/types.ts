//定義資料類型
export interface Task {
    id: number;
    title: string;
    description: string;
    status: 'pending' | 'in_progress' | 'completed';
    priority: 'low' | 'medium' | 'high';
    created_at: string;
    updated_at: string;
}

export interface ApiResponse<T>{
    success : boolean;
    data? : T;
    message? : string;
}