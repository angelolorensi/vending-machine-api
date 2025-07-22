import React from 'react';

const SelectionButtons = ({ selectedSlot, onPurchase, isLoading, isCardVerified }) => {
    const buttonRows = ['A', 'B', 'C', 'D', 'E'];
    const buttonCols = [1, 2, 3, 4, 5, 6];

    return (
        <div className="bg-gray-800 p-4 rounded">
            <div className="text-yellow-400 text-xs font-bold mb-2 text-center">SELECTION</div>
            <div className="grid grid-cols-3 gap-1 mb-4 max-h-32 overflow-y-auto">
                {buttonRows.slice(0, 6).map(row =>
                    buttonCols.slice(0, 6).map(col => (
                        <button
                            key={`${row}${col}`}
                            className="bg-black border border-gray-600 text-white text-xs font-mono py-1 px-1 rounded hover:bg-gray-900"
                        >
                            {row}{col}
                        </button>
                    ))
                )}
            </div>

            {/* Product Display Window */}
            <div className="bg-black border border-gray-600 p-2 mb-4 h-16 rounded">
                {selectedSlot && selectedSlot.product ? (
                    <div className="text-green-400 text-xs font-mono">
                        <div>SELECTED: {selectedSlot.row}{selectedSlot.col}</div>
                        <div>{selectedSlot.product.name}</div>
                        <div>PRICE: {selectedSlot.product.price_points} PTS</div>
                    </div>
                ) : (
                    <div className="text-gray-500 text-xs font-mono">
                        SELECT PRODUCT
                    </div>
                )}
            </div>

            {/* Purchase Button */}
            <button
                onClick={onPurchase}
                disabled={!selectedSlot || !isCardVerified || isLoading || !selectedSlot?.product}
                className="w-full bg-green-600 hover:bg-green-500 disabled:bg-gray-600 disabled:opacity-50 text-white font-bold py-2 px-4 rounded transition-colors"
            >
                {isLoading ? 'PROCESSING...' : 'PURCHASE'}
            </button>
        </div>
    );
};

export default SelectionButtons;
