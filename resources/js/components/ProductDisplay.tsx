import React from 'react';

const ProductDisplay = ({
                            selectedSlot,
                            isCardVerified,
                            isLoading,
                            onPurchase
                        }) => {
    if (!selectedSlot || !selectedSlot.product) {
        return (
            <div className="bg-gray-100 p-4 rounded-lg mb-4 text-center text-gray-500">
                <p>No product selected</p>
                <p className="text-sm">Click on a slot to view product details</p>
            </div>
        );
    }

    return (
        <div className="space-y-4">
            {/* Selected Product Display */}
            <div className="bg-yellow-100 border border-yellow-200 p-4 rounded-lg">
                <h4 className="font-bold text-lg mb-3 text-gray-800">Selected Product</h4>
                <div className="grid grid-cols-2 gap-4">
                    <div>
                        <p className="text-sm text-gray-600">Slot Number</p>
                        <p className="font-semibold text-lg">{selectedSlot.number}</p>
                    </div>
                    <div>
                        <p className="text-sm text-gray-600">Price</p>
                        <p className="font-bold text-lg text-green-600">{selectedSlot.product.price_points} pts</p>
                    </div>
                </div>
                <div className="mt-3">
                    <p className="text-sm text-gray-600">Product Name</p>
                    <p className="font-semibold text-xl">{selectedSlot.product.name}</p>
                </div>
                {selectedSlot.product.description && (
                    <div className="mt-3">
                        <p className="text-sm text-gray-600">Description</p>
                        <p className="text-gray-800">{selectedSlot.product.description}</p>
                    </div>
                )}
            </div>

            {/* Purchase Button */}
            <div className="text-center">
                <button
                    onClick={onPurchase}
                    disabled={!selectedSlot || !isCardVerified || isLoading || !selectedSlot?.product}
                    className="bg-green-500 hover:bg-green-600 text-white px-8 py-3 rounded-lg font-bold disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 transform hover:scale-105 active:scale-95 shadow-lg"
                >
                    {isLoading ? (
                        <div className="flex items-center">
                            <div className="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                            Processing...
                        </div>
                    ) : (
                        'Purchase Product'
                    )}
                </button>
            </div>
        </div>
    );
};

export default ProductDisplay;
