import React from 'react';
import type { Slot as SlotType } from '@/types';

interface SelectionButtonsProps {
    selectedSlot: SlotType | null;
    onPurchase: () => void;
    isLoading: boolean;
    isCardVerified: boolean;
    slots: SlotType[];
    onSlotSelect: (slot: SlotType) => void;
}

const SelectionButtons: React.FC<SelectionButtonsProps> = ({
    selectedSlot,
    onPurchase,
    isLoading,
    isCardVerified,
    slots,
    onSlotSelect
}) => {
    const buttonRows = ['A', 'B', 'C', 'D', 'E'];
    const buttonCols = [1, 2, 3, 4, 5, 6];

    const handleSlotSelection = (row: string, col: number) => {
        const slotCode = `${row}${col}`;
        const slot = slots.find(s => `${s.row}${s.col}` === slotCode);
        if (slot && slot.product && slot.quantity > 0) {
            onSlotSelect(slot);
        }
    };

    return (
        <div className="bg-gray-800 p-4 rounded">
            <div className="text-yellow-400 text-xs font-bold mb-2 text-center">SELECTION</div>
            <div className="grid grid-cols-3 gap-1 mb-4">
                {buttonRows.slice(0, 5).map(row =>
                    buttonCols.map(col => {
                        const slotCode = `${row}${col}`;
                        const slot = slots.find(s => `${s.row}${s.col}` === slotCode);
                        const hasAvailableProduct = slot && slot.product && slot.quantity > 0;
                        const isSelectedButton = selectedSlot && `${selectedSlot.row}${selectedSlot.col}` === slotCode;

                        return (
                            <button
                                key={slotCode}
                                onClick={() => handleSlotSelection(row, col)}
                                disabled={!hasAvailableProduct}
                                className={`
                                    text-xs font-mono py-1 px-1 rounded transition-colors border
                                    ${hasAvailableProduct
                                        ? 'bg-black border-gray-600 text-white hover:bg-gray-900 hover:border-yellow-400'
                                        : 'bg-gray-700 border-gray-700 text-gray-500 cursor-not-allowed'
                                    }
                                    ${isSelectedButton ? 'bg-yellow-600 border-yellow-400 text-black' : ''}
                                `}
                            >
                                {slotCode}
                            </button>
                        );
                    })
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
