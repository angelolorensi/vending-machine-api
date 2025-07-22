import React from 'react';
import { createRoot } from 'react-dom/client';
import VendingMachine from './pages/VendingMachine.jsx';

function App() {
    return (
        <div>
            <VendingMachine />
        </div>
    );
}
const container = document.getElementById('app');
const root = createRoot(container);
root.render(<App/>)

