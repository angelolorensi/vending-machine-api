import ErrorHandler from '../utils/errorHandler.js';

class MachineService {
    static async getAllMachines() {
        try {
            const response = await fetch('/api/machines', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
            });

            return await ErrorHandler.handleResponse(response);
        } catch (error) {
            return ErrorHandler.handleError(error);
        }
    }
}

export default MachineService;
