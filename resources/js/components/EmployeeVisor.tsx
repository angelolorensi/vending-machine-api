import React from 'react';
import type { Card } from '@/types';

interface EmployeeVisorProps {
    cardData: Card | null;
    isCardVerified: boolean;
}

const EmployeeVisor: React.FC<EmployeeVisorProps> = ({ cardData, isCardVerified }) => {
    return (
        <div className="bg-black p-3 m-4 rounded border border-gray-600">
            <div className="text-green-400 font-mono text-sm">
                {isCardVerified && cardData ? (
                    <div className="space-y-1">
                        <div>EMPLOYEE: {cardData.employee_name}</div>
                        <div>BALANCE: {cardData.points_balance} POINTS</div>
                        <div className="text-yellow-400">LIMIT: {cardData.daily_point_limit} PTS</div>
                    </div>
                ) : (
                    <div className="text-gray-500">
                        <div>PLEASE INSERT CARD</div>
                        <div>FOR SERVICE</div>
                    </div>
                )}
            </div>
        </div>
    );
};

export default EmployeeVisor;
