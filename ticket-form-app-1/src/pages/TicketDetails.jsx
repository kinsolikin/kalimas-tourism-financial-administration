import React, { useEffect, useState } from 'react';
import { useParams } from 'react-router-dom';
import { fetchTicketDetails } from '../utils/api';

const TicketDetails = () => {
    const { id } = useParams();
    const [ticket, setTicket] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    useEffect(() => {
        const getTicketDetails = async () => {
            try {
                const data = await fetchTicketDetails(id);
                setTicket(data);
            } catch (err) {
                setError('Failed to fetch ticket details');
            } finally {
                setLoading(false);
            }
        };

        getTicketDetails();
    }, [id]);

    if (loading) {
        return <div>Loading...</div>;
    }

    if (error) {
        return <div>{error}</div>;
    }

    return (
        <div>
            <h1>Ticket Details</h1>
            {ticket ? (
                <div>
                    <h2>{ticket.title}</h2>
                    <p>{ticket.description}</p>
                    <p>Status: {ticket.status}</p>
                    <p>Created At: {new Date(ticket.createdAt).toLocaleString()}</p>
                </div>
            ) : (
                <p>No ticket found.</p>
            )}
        </div>
    );
};

export default TicketDetails;