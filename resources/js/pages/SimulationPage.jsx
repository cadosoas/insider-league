import React, { useEffect, useState } from 'react';
import axios from 'axios';
import { useNavigate } from 'react-router-dom';

const SimulationPage = () => {
    const navigate = useNavigate();

    const [table, setTable] = useState([]);
    const [currentWeek, setCurrentWeek] = useState(1);
    const [fixtures, setFixtures] = useState([]);
    const [champion, setChampion] = useState(null);
    const [predictions, setPredictions] = useState([]);

    useEffect(() => {
        axios.get('/league/simulate')
            .then((res) => {
                setTable(res.data.tables || []);
                setCurrentWeek(res.data.current_week || 1);
                setFixtures(res.data.fixtures || []);
                setChampion(res.data.champion || null);
                setPredictions(res.data.predictions || []);
            });
    }, []);

    const handlePlayAllWeeks = () => {
        axios.get('/league/simulate/play-all-weeks')
            .then((res) => {
                setTable(res.data.tables || []);
                setCurrentWeek(res.data.current_week || 1);
                setFixtures(res.data.fixtures || []);
                setChampion(res.data.champion || null);
                setPredictions(res.data.predictions || []);
            });
    };

    const handlePlayNextWeek = () => {
        axios.get('/league/simulate/play-week-by-week')
            .then((res) => {
                setTable(res.data.tables || []);
                setCurrentWeek(res.data.current_week || 1);
                setFixtures(res.data.fixtures || []);
                setChampion(res.data.champion || null);
                setPredictions(res.data.predictions || []);
            });
    };

    const handleReset = () => {

        axios.get('/league/reset').then(() => {
            navigate('/');
        });
    };

    return (
        <div className="container mt-4">
            <h1 className="mb-4">Simulation</h1>
            <div className="row">
                {/* Standings */}
                <div className="col-md-4 d-flex flex-column">
                    <div className="card flex-grow-1 mb-3">
                        <div className="card-header text-center">
                            <strong>Standings</strong>
                        </div>
                        <div className="table-responsive">
                            <table className="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>Team</th>
                                        <th>Pts</th>
                                        <th>P</th>
                                        <th>W</th>
                                        <th>D</th>
                                        <th>L</th>
                                        <th>GF</th>
                                        <th>GA</th>
                                        <th>GD</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {table.map((row, idx) => (
                                        <tr key={idx}>
                                            <td>{row.team}</td>
                                            <td>{row.points}</td>
                                            <td>{row.played}</td>
                                            <td>{row.wins}</td>
                                            <td>{row.draws}</td>
                                            <td>{row.losses}</td>
                                            <td>{row.goals_for}</td>
                                            <td>{row.goals_against}</td>
                                            <td>{row.goal_difference}</td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <button className="btn btn-primary mt-auto" onClick={handlePlayAllWeeks} disabled={champion}>
                        Play All Weeks
                    </button>

                </div>


                <div className="col-md-4 d-flex flex-column">
                    <div className="card flex-grow-1 mb-3">
                        <div className="card-header text-center">
                            {champion ? <strong>League Finished</strong> : <strong>Week {currentWeek}</strong>}
                        </div>
                        <div className="card-body text-center text-muted">
                            {champion ? (
                                <div>
                                    <h3>Champion: {champion}</h3>
                                </div>
                            ) : fixtures.map((match, idx) => (
                                <div key={idx}>
                                    {match.home_team} vs {match.away_team}
                                </div>
                            ))}
                        </div>
                    </div>
                    <button className="btn btn-success mt-auto" onClick={handlePlayNextWeek} disabled={champion}>
                        Play Next Week
                    </button>
                </div>


                <div className="col-md-4 d-flex flex-column">
                    <div className="card flex-grow-1 mb-3">
                        <div className="card-header text-center">
                            <strong>Prediction</strong>
                        </div>
                        <div className="card-body text-center text-muted">
                            <table className="table table-striped mb-0">
                                <tbody>
                                    {predictions.map((prediction, idx) => (
                                        <tr key={idx}>
                                            <td>{prediction.team}</td>
                                            <td>{prediction.percentage}%</td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <button className="btn btn-danger mt-3 w-100" onClick={handleReset}>
                        Reset League
                    </button>
                </div>

            </div>
        </div>
    );
};

export default SimulationPage;
