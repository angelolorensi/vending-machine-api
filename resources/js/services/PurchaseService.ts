import ErrorHandler from '@/utils/errorHandler';
import type { Purchase, ServiceResult } from '@/types';

class PurchaseService {
    static async purchaseProduct(
        cardNumber: string,
        machineId: number,
        slotNumber: string
    ): Promise<ServiceResult<Purchase>> {
        try {
            const response = await fetch('/api/purchase', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    card_number: cardNumber,
                    machine_id: machineId,
                    slot_number: slotNumber,
                }),
            });

            return await ErrorHandler.handleResponse<Purchase>(response);
        } catch (error) {
            return ErrorHandler.handleError(error as Error);
        }
    }
}

export default PurchaseService;
