import React, { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import axios from 'axios';

const TeamsPage = () => {
    const [teams, setTeams] = useState([]);
    const navigate = useNavigate();

    useEffect(() => {
        axios.get('/league/teams').then((res) => setTeams(res.data));
    }, []);

    const handleGenerate = () => {
        navigate('/fixtures');
    };

    return (
        <div className="container mt-4">
            <h1 className="mb-4">Teams</h1>
            <ul className="list-group mb-4">
                {teams.map((team) => (
                    <li key={team.id} className="list-group-item">{team.name}</li>
                ))}
            </ul>
            <button className="btn btn-primary" onClick={handleGenerate}>
                Generate Fixtures
            </button>
        </div>
    );
};

export default TeamsPage;
