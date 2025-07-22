import ErrorHandler from '../utils/errorHandler.js';

class CardService {
    static async verifyCard(cardNumber) {
        try {
            const response = await fetch('/api/cards/verify', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ card_number: cardNumber }),
            });

            return await ErrorHandler.handleResponse(response);
        } catch (error) {
            return ErrorHandler.handleError(error);
        }
    }
}

export default CardService;
