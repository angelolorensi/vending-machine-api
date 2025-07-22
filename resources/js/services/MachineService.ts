import ErrorHandler from '@/utils/errorHandler';
import type { Machine, ServiceResult } from '@/types';

class MachineService {
    static async getAllMachines(): Promise<ServiceResult<Machine[]>> {
        try {
            const response = await fetch('/api/machines', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
            });

            return await ErrorHandler.handleResponse<Machine[]>(response);
        } catch (error) {
            return ErrorHandler.handleError(error as Error);
        }
    }
}

export default MachineService;
