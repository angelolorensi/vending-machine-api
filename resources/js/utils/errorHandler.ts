import type { ApiErrorResponse, ServiceResult } from '@/types';

class ErrorHandler {
    static handleError(error: Error | null, response: any = null): ApiErrorResponse {
        let errorMessage = 'An unexpected error occurred';

        if (response && response.error) {
            errorMessage = response.error;
        } else if (response && response.message) {
            errorMessage = response.message;
        } else if (error && error.message) {
            errorMessage = error.message;
        }

        console.error('Error occurred:', {
            error: error,
            response: response,
            message: errorMessage
        });

        alert(errorMessage);

        return {
            success: false,
            message: errorMessage
        };
    }

    static async handleResponse<T>(response: Response): Promise<ServiceResult<T>> {
        const responseData = await response.json();

        if (!response.ok) {
            return this.handleError(null, responseData);
        }

        return responseData;
    }
}

export default ErrorHandler;
