import React from 'react';
import ReactDOM from 'react-dom/client';
import { BrowserRouter, Routes, Route } from 'react-router-dom';
import 'bootstrap/dist/css/bootstrap.min.css';

import TeamsPage from './pages/TeamsPage';
import FixturesPage from './pages/FixturesPage';
import SimulationPage from './pages/SimulationPage';

const App = () => (
    <BrowserRouter>
        <Routes>
            <Route path="/" element={<TeamsPage />} />
            <Route path="/fixtures" element={<FixturesPage />} />
            <Route path="/simulation" element={<SimulationPage />} />
        </Routes>
    </BrowserRouter>
);

ReactDOM.createRoot(document.getElementById('app')).render(<App />);
