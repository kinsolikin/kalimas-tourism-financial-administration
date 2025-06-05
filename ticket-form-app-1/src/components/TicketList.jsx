import React, { useEffect, useState } from 'react';
import { fetchTickets } from '../utils/api';

const TicketList = () => {
    const [tickets, setTickets] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    useEffect(() => {
        const getTickets = async () => {
            try {
                const data = await fetchTickets();
                setTickets(data);
            } catch (err) {
                setError(err.message);
            } finally {
                setLoading(false);
            }
        };

        getTickets();
    }, []);

    if (loading) {
        return <div>Loading tickets...</div>;
    }

    if (error) {
        return <div>Error fetching tickets: {error}</div>;
    }

    return (
        <div>
            <h2>Submitted Tickets</h2>
            <ul>
                {tickets.map(ticket => (
                    <li key={ticket.id}>
                        <h3>{ticket.title}</h3>
                        <p>{ticket.description}</p>
                    </li>
                ))}
            </ul>
        </div>
    );
};

export default TicketList;