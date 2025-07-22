import React, { useState, useEffect } from 'react';
import CardService from '@/services/CardService';
import PurchaseService from '@/services/PurchaseService';
import SlotService from '@/services/SlotService';
import MachineService from '@/services/MachineService';
import CardReader from '@/components/CardReader';
import EmployeeVisor from '@/components/EmployeeVisor';
import Slot from '@/components/Slot';
import SelectionButtons from '@/components/SelectionButtons';
import DispenserArea from '@/components/DispenserArea';
import type { Card, Machine, MachineWithSlots, Slot as SlotType } from '@/types';

const VendingMachine: React.FC = () => {
    const [selectedSlot, setSelectedSlot] = useState<SlotType | null>(null);
    const [cardNumber, setCardNumber] = useState<string>('');
    const [isCardVerified, setIsCardVerified] = useState<boolean>(false);
    const [cardData, setCardData] = useState<Card | null>(null);
    const [isLoading, setIsLoading] = useState<boolean>(false);
    const [machineData, setMachineData] = useState<MachineWithSlots | null>(null);
    const [selectedMachine, setSelectedMachine] = useState<number>(1);
    const [machines, setMachines] = useState<Machine[]>([]);
    const [loadingMachine, setLoadingMachine] = useState<boolean>(true);

    // Fetch available machines on component mount
    useEffect(() => {
        const fetchMachines = async () => {
            const result = await MachineService.getAllMachines();
            if (result.success) {
                setMachines(result.data);
                if (result.data.length > 0) {
                    setSelectedMachine(result.data[0].machine_id);
                }
            }
        };

        fetchMachines();
    }, []);

    // Fetch machine slots when selected machine changes
    useEffect(() => {
        if (selectedMachine) {
            fetchMachineSlots(selectedMachine);
        }
    }, [selectedMachine]);

    const fetchMachineSlots = async (machineId: number) => {
        setLoadingMachine(true);
        const result = await SlotService.getMachineSlots(machineId);

        if (result.success) {
            setMachineData(result.data);
        } else {
            setMachineData(null);
        }
        setLoadingMachine(false);
    };

    const handleCardVerification = async () => {
        if (!cardNumber.trim()) {
            alert('Please enter a card number');
            return;
        }

        setIsLoading(true);
        try {
            const result = await CardService.verifyCard(cardNumber);

            if (result.success) {
                setCardData(result.data);
                setIsCardVerified(true);
            }
        } catch (error) {
            console.error('Unexpected error occurred', error);
        } finally {
            setIsLoading(false);
        }
    };

    const handleSlotSelect = (slot: SlotType) => {
        if (!isCardVerified) {
            alert('Please verify your card first');
            return;
        }
        if (!slot.product) {
            alert('This slot is empty');
            return;
        }
        if (slot.quantity <= 0) {
            alert('This product is out of stock');
            return;
        }
        setSelectedSlot(slot);
    };

    const handlePurchase = async () => {
        if (!selectedSlot || !cardData) return;

        setIsLoading(true);
        try {
            const result = await PurchaseService.purchaseProduct(
                cardNumber,
                selectedMachine,
                selectedSlot.number
            );

            if (result.success) {
                if (result.data && result.data.product) {
                    alert(`Purchase successful! ${result.data.product.name} - ${result.data.product.points_deducted} points deducted. Remaining balance: ${result.data.remaining_balance} points`);
                } else {
                    alert('Purchase successful! Please check your balance.');
                }

                setCardData(prev => prev ? ({
                    ...prev,
                    points_balance: result.data.remaining_balance
                }) : null);

                setSelectedSlot(null);

                await fetchMachineSlots(selectedMachine);
            } else {
                alert(result.message || 'Purchase failed. Please try again.');
            }
        } catch (error) {
            console.error('Unexpected error occurred during purchase', error);
            alert('An unexpected error occurred. Please try again.');
        } finally {
            setIsLoading(false);
        }
    };

    const resetCard = () => {
        setCardNumber('');
        setIsCardVerified(false);
        setCardData(null);
        setSelectedSlot(null);
    };

    const handleMachineChange = (machineId: string) => {
        setSelectedMachine(parseInt(machineId));
        setSelectedSlot(null);
    };

    const slots = machineData?.slots || [];

    return (
        <div className="max-w-5xl mx-auto">
            {/* Machine Selector */}
            {machines.length > 1 && (
                <div className="mb-4 text-center">
                    <select
                        value={selectedMachine}
                        onChange={(e) => handleMachineChange(e.target.value)}
                        className="px-4 py-2 border rounded-lg bg-white"
                        disabled={isLoading}
                    >
                        {machines.map((machine) => (
                            <option key={machine.machine_id} value={machine.machine_id}>
                                {machine.name} - {machine.location}
                            </option>
                        ))}
                    </select>
                </div>
            )}

            {/* Vending Machine Frame */}
            <div className="bg-red-500 p-4 rounded-lg shadow-2xl">
                <div className="bg-gray-700 rounded-lg overflow-hidden">
                    {/* Top Section - Card Reader */}
                    <CardReader
                        cardNumber={cardNumber}
                        setCardNumber={setCardNumber}
                        isCardVerified={isCardVerified}
                        isLoading={isLoading}
                        onVerifyCard={handleCardVerification}
                        onResetCard={resetCard}
                    />

                    {/* Employee Visor */}
                    <EmployeeVisor
                        cardData={cardData}
                        isCardVerified={isCardVerified}
                    />

                    {/* Main Display Area */}
                    <div className="flex p-4 gap-4">
                        {/* Left Side - Product Slots */}
                        <div className="flex-1 bg-gray-800 p-4 rounded">
                            <div className="text-white text-sm font-bold mb-3 text-center">
                                {machineData?.name || 'VENDING MACHINE'}
                            </div>

                            {loadingMachine ? (
                                <div className="text-white text-center py-8">
                                    <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-white mx-auto mb-2"></div>
                                    Loading...
                                </div>
                            ) : (
                                <div className="grid grid-cols-6 gap-3 justify-items-center">
                                    {slots.map((slot) => (
                                        <Slot
                                            key={slot.slot_id}
                                            slot={slot}
                                            isSelected={selectedSlot?.slot_id === slot.slot_id}
                                            onClick={() => {}} // Disabled - selection now via buttons
                                        />
                                    ))}
                                </div>
                            )}
                        </div>

                        {/* Right Side - Selection Panel */}
                        <div className="w-48">
                            <SelectionButtons
                                selectedSlot={selectedSlot}
                                onPurchase={handlePurchase}
                                isLoading={isLoading}
                                isCardVerified={isCardVerified}
                                slots={slots}
                                onSlotSelect={handleSlotSelect}
                            />
                        </div>
                    </div>

                    {/* Bottom - Dispenser */}
                    <DispenserArea />
                </div>
            </div>
        </div>
    );
};

export default VendingMachine;
