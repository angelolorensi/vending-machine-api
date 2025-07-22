import React from 'react';

const CardReader = ({ cardNumber, setCardNumber, isCardVerified, isLoading, onVerifyCard, onResetCard }) => {
    return (
        <div className="bg-gray-800 p-4 rounded-t-lg">
            <div className="bg-black p-3 rounded">
                <h3 className="text-green-400 text-sm font-mono mb-2">CARD READER</h3>
                <div className="flex gap-2">
                    <input
                        type="text"
                        placeholder="Insert Card Number"
                        value={cardNumber}
                        onChange={(e) => setCardNumber(e.target.value)}
                        className="flex-1 bg-gray-900 text-green-400 px-2 py-1 text-sm font-mono border border-gray-600 rounded focus:border-green-400"
                        disabled={isCardVerified || isLoading}
                        onKeyPress={(e) => e.key === 'Enter' && !isCardVerified && cardNumber.trim() && onVerifyCard()}
                    />
                    <button
                        onClick={onVerifyCard}
                        disabled={isCardVerified || !cardNumber.trim() || isLoading}
                        className="bg-green-600 hover:bg-green-500 px-3 py-1 text-xs font-bold rounded disabled:opacity-50"
                    >
                        {isLoading ? 'VERIFY...' : 'VERIFY'}
                    </button>
                    {isCardVerified && (
                        <button
                            onClick={onResetCard}
                            className="bg-red-600 hover:bg-red-500 px-2 py-1 text-xs font-bold rounded"
                        >
                            RESET
                        </button>
                    )}
                </div>
            </div>
        </div>
    );
};

export default CardReader;
