import ErrorHandler from '@/utils/errorHandler';
import type { MachineWithSlots, ServiceResult } from '@/types';

class SlotService {
    static async getMachineSlots(machineId: number): Promise<ServiceResult<MachineWithSlots>> {
        try {
            const response = await fetch(`/api/machines/${machineId}?with=slots`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
            });

            const result = await ErrorHandler.handleResponse<MachineWithSlots>(response);
            
            if (result.success && result.data) {
                // Transform slots to include row/col labels
                const transformedData = {
                    ...result.data,
                    slots: result.data.slots?.map((slot, index) => {
                        const slotNumber = parseInt(slot.number, 10);
                        return {
                            ...slot,
                            row: String.fromCharCode(65 + Math.floor((slotNumber - 1) / 4)), // A, B, C, D, E
                            col: ((slotNumber - 1) % 4) + 1
                        };
                    }) || []
                };

                return {
                    ...result,
                    data: transformedData
                };
            }
            
            return result;
        } catch (error) {
            return ErrorHandler.handleError(error as Error);
        }
    }
}

export default SlotService;
