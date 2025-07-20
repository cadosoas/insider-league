import React, { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import axios from 'axios';

const FixturesPage = () => {
    const [fixtures, setFixtures] = useState([]);
    const navigate = useNavigate();

    useEffect(() => {
        axios.get('/league/generate-fixtures')
            .then((res) => setFixtures(res.data));
    }, []);

    const handleStartSimulation = () => {
        navigate('/simulation');
    };

    return (
        <div className="container mt-4">
            <h1 className="mb-4">Fixtures</h1>
            <div className="row">
                {fixtures.map((week, index) => (
                    <div className="col-md-4 mb-4" key={index}>
                        <div className="card h-100 shadow-sm">
                            <div className="card-header text-center">
                                <strong>Week {index + 1}</strong>
                            </div>
                            <ul className="list-group list-group-flush">
                                {week.map((match, idx) => (
                                    <li key={idx} className="list-group-item d-flex justify-content-between">
                                        <span>{match.home_team} vs {match.away_team}</span>
                                        <span>
                                            {match.played_at
                                                ? `${match.home_score} - ${match.away_score}`
                                                : 'Not played'}
                                        </span>
                                    </li>
                                ))}
                            </ul>
                        </div>
                    </div>
                ))}
            </div>

            <div className="text-center mt-4">
                <button className="btn btn-success btn-lg" onClick={handleStartSimulation}>
                    Start Simulation
                </button>
            </div>
        </div>
    );
};

export default FixturesPage;
