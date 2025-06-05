import React from 'react';
import TicketForm from '../components/TicketForm';
import TicketList from '../components/TicketList';

const Home = () => {
    return (
        <div className="home-container">
            <h1>Welcome to the Ticket Form Application</h1>
            <TicketForm />
            <TicketList />
        </div>
    );
};

export default Home;