import React from 'react';

const MachineSelector = ({
                             machines,
                             selectedMachine,
                             onMachineChange,
                             isLoading
                         }) => {
    return (
        <div className="bg-gray-600 text-white p-4">
            <div className="flex items-center gap-4 justify-center">
                <label className="font-semibold">Select Machine:</label>
                <select
                    value={selectedMachine}
                    onChange={(e) => onMachineChange(parseInt(e.target.value))}
                    className="px-3 py-2 text-black rounded focus:outline-none focus:ring-2 focus:ring-blue-300 min-w-0 flex-1 max-w-xs"
                    disabled={isLoading}
                >
                    {machines.map((machine) => (
                        <option key={machine.machine_id} value={machine.machine_id}>
                            {machine.name} - {machine.location}
                        </option>
                    ))}
                </select>
                {isLoading && (
                    <div className="flex items-center">
                        <div className="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                        <span className="text-sm">Loading...</span>
                    </div>
                )}
            </div>
        </div>
    );
};

export default MachineSelector;
