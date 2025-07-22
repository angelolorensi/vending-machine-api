import ErrorHandler from '@/utils/errorHandler';
import type { Card, ServiceResult } from '@/types';

class CardService {
    static async verifyCard(cardNumber: string): Promise<ServiceResult<Card>> {
        try {
            const response = await fetch('/api/cards/verify', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ card_number: cardNumber }),
            });

            return await ErrorHandler.handleResponse<Card>(response);
        } catch (error) {
            return ErrorHandler.handleError(error as Error);
        }
    }
}

export default CardService;
