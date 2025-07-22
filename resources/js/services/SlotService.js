import ErrorHandler from '../utils/errorHandler.js';

class SlotService {
    static async getMachineSlots(machineId) {
        try {
            const response = await fetch(`/api/machines/${machineId}?with=slots`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
            });

            const result = await ErrorHandler.handleResponse(response);
            
            if (result.success && result.data) {
                // Transform slots to include row/col labels
                const transformedData = {
                    ...result.data,
                    slots: result.data.slots?.map((slot, index) => ({
                        ...slot,
                        row: String.fromCharCode(65 + Math.floor((slot.number - 1) / 4)), // A, B, C, D, E
                        col: ((slot.number - 1) % 4) + 1
                    })) || []
                };

                return {
                    ...result,
                    data: transformedData
                };
            }
            
            return result;
        } catch (error) {
            return ErrorHandler.handleError(error);
        }
    }
}

export default SlotService;
