import React from 'react';
import type { Slot as SlotType, Product } from '@/types';

interface SlotProps {
    slot: SlotType;
    isSelected: boolean;
    onClick: (slot: SlotType) => void;
}

const Slot: React.FC<SlotProps> = ({ slot, isSelected, onClick }) => {
    const hasProduct = slot.product !== null && slot.product !== undefined;
    const isOutOfStock = slot.quantity <= 0;

    // Get product color from category or default
    const getProductColor = (product: Product | null) => {
        if (!product) return '#374151';
        
        console.log('Product color debug:', product.name, product.category_color);
        return product.category_color || '#6b7280'; // Use category color or default gray
    };

    return (
        <div className="relative">
            <div
                className={`
                    w-24 h-28 rounded-lg border-4 transition-all duration-200 relative overflow-hidden
                    ${hasProduct && !isOutOfStock
                    ? 'border-gray-400 shadow-lg'
                    : 'border-gray-600 opacity-50'
                }
                    ${isSelected ? 'border-yellow-400 ring-2 ring-yellow-300' : ''}
                    ${isOutOfStock ? 'border-red-400' : ''}
                `}
                style={{
                    background: hasProduct
                        ? `linear-gradient(135deg, ${getProductColor(slot.product)} 0%, ${getProductColor(slot.product)}dd 100%)`
                        : 'linear-gradient(135deg, #374151 0%, #1f2937 100%)'
                }}
            >
                {/* Slot Label */}
                <div className="absolute top-1 left-1 bg-black bg-opacity-70 text-white text-xs px-1 rounded">
                    {slot.row}{slot.col}
                </div>

                {/* Product */}
                {hasProduct && (
                    <div className="absolute inset-2 flex flex-col justify-between text-white">
                        {/* Product name at top */}
                        <div className="text-xs font-bold text-center leading-tight p-1 text-white drop-shadow-lg">
                            {slot.product.name}
                        </div>

                        {/* Price and quantity at bottom */}
                        <div className="flex flex-col gap-1">
                            <div className="text-xs px-2 py-1 text-center text-white drop-shadow-lg">
                                {slot.product.price_points} pts
                            </div>
                            <div className={`text-xs px-2 py-1 rounded text-center font-bold ${isOutOfStock ? 'bg-red-600' : 'bg-green-600'}`}>
                                Qty: {slot.quantity}
                            </div>
                        </div>
                    </div>
                )}

                {/* Empty or Out of Stock indicator */}
                {!hasProduct && (
                    <div className="absolute inset-0 flex items-center justify-center text-gray-500 text-xs">
                        EMPTY
                    </div>
                )}
                {hasProduct && isOutOfStock && (
                    <div className="absolute inset-0 flex items-center justify-center bg-red-600 bg-opacity-80 text-white text-xs font-bold">
                        OUT OF STOCK
                    </div>
                )}
            </div>
        </div>
    );
};

export default Slot;
