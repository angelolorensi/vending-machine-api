// Base API Response structure
export interface ApiResponse<T = any> {
  success: boolean;
  data: T;
  message?: string;
}

// Error response structure
export interface ApiErrorResponse {
  success: false;
  message: string;
  error?: string;
}

// Service method return types
export type ServiceResult<T> = ApiResponse<T> | ApiErrorResponse;