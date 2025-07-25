import React from 'react';
import Slot from './Slot';

const MachineSlots = ({
                          slots,
                          selectedSlot,
                          isLoading,
                          isCardVerified,
                          onSlotClick
                      }) => {
    const handleSlotClick = (slot) => {
        if (!isCardVerified) {
            alert('Please verify your card first');
            return;
        }

        if (!slot.product) {
            alert('This slot is empty');
            return;
        }

        onSlotClick(slot);
    };

    return (
        <div className="bg-gray-800 rounded-lg p-6">
            <h3 className="text-white text-xl mb-4 text-center">
                Select a Product
            </h3>

            {isLoading ? (
                <div className="text-white text-center py-8">
                    <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-white mx-auto mb-2"></div>
                    Loading machine slots...
                </div>
            ) : (
                <div className="grid grid-cols-6 gap-2 sm:grid-cols-4 lg:grid-cols-6">
                    {slots.map((slot) => (
                        <Slot
                            key={slot.slot_id || slot.number}
                            slot={slot}
                            isSelected={selectedSlot?.number === slot.number}
                            onClick={handleSlotClick}
                        />
                    ))}
                </div>
            )}
        </div>
    );
};

export default MachineSlots;
