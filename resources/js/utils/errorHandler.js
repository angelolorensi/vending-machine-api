class ErrorHandler {
    static handleError(error, response = null) {
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
    
    static async handleResponse(response) {
        const responseData = await response.json();
        
        if (!response.ok) {
            return this.handleError(null, responseData);
        }
        
        return {
            success: true,
            data: responseData.data,
            message: responseData.message
        };
    }
}

export default ErrorHandler;