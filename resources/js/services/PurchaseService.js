import ErrorHandler from '../utils/errorHandler.js';

class PurchaseService {
    static async purchaseProduct(cardNumber, machineId, slotNumber) {
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

            return await ErrorHandler.handleResponse(response);
        } catch (error) {
            return ErrorHandler.handleError(error);
        }
    }
}

export default PurchaseService;
