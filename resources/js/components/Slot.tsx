import React from 'react';

const Slot = ({ slot, isSelected, onClick }) => {
    const hasProduct = slot.product !== null && slot.product !== undefined;

    // Color mapping for different product types
    const getProductColor = (product) => {
        if (!product) return '#374151';

        const name = product.name.toLowerCase();
        if (name.includes('pepsi')) return '#1e40af';
        if (name.includes('coke') || name.includes('cola')) return '#dc2626';
        if (name.includes('sprite')) return '#16a34a';
        if (name.includes('fanta')) return '#ea580c';
        if (name.includes('water')) return '#0284c7';
        if (name.includes('juice')) return '#7c3aed';
        return '#6b7280'; // Default gray
    };

    return (
        <div className="relative">
            <button
                onClick={() => onClick(slot)}
                disabled={!hasProduct}
                className={`
                    w-16 h-20 rounded-lg border-4 transition-all duration-200 relative overflow-hidden
                    ${hasProduct
                    ? 'border-gray-400 hover:border-yellow-400 shadow-lg'
                    : 'border-gray-600 opacity-50 cursor-not-allowed'
                }
                    ${isSelected ? 'border-yellow-400 ring-2 ring-yellow-300' : ''}
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
                    <div className="absolute inset-2 flex flex-col justify-center items-center text-white">
                        <div className="text-xs font-bold text-center leading-tight">
                            {slot.product.name}
                        </div>
                        <div className="text-xs bg-black bg-opacity-50 px-1 rounded mt-1">
                            {slot.product.price_points}pts
                        </div>
                    </div>
                )}

                {/* Empty indicator */}
                {!hasProduct && (
                    <div className="absolute inset-0 flex items-center justify-center text-gray-500 text-xs">
                        EMPTY
                    </div>
                )}
            </button>
        </div>
    );
};

export default Slot;
