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
                // Sort slots: products with stock first, then products out of stock, then empty slots
                const sortedSlots = result.data.slots?.sort((a, b) => {
                    // Categorize slots
                    const aHasProductWithStock = a.product && a.quantity > 0;
                    const bHasProductWithStock = b.product && b.quantity > 0;
                    const aHasProductNoStock = a.product && a.quantity <= 0;
                    const bHasProductNoStock = b.product && b.quantity <= 0;
                    const aIsEmpty = !a.product;
                    const bIsEmpty = !b.product;
                    
                    // Priority: 1) Products with stock, 2) Products out of stock, 3) Empty slots
                    if (aHasProductWithStock && !bHasProductWithStock) return -1;
                    if (!aHasProductWithStock && bHasProductWithStock) return 1;
                    if (aHasProductNoStock && bIsEmpty) return -1;
                    if (aIsEmpty && bHasProductNoStock) return 1;
                    
                    // Within same category, sort by slot number
                    return parseInt(a.number) - parseInt(b.number);
                }) || [];

                // Transform slots to include row/col labels based on new position
                const transformedData = {
                    ...result.data,
                    slots: sortedSlots.map((slot, index) => {
                        return {
                            ...slot,
                            row: String.fromCharCode(65 + Math.floor(index / 6)), // A, B, C, D, E
                            col: (index % 6) + 1
                        };
                    })
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
