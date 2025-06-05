import React, { useState } from 'react';

const TicketForm = () => {
    const [title, setTitle] = useState('');
    const [description, setDescription] = useState('');
    const [priority, setPriority] = useState('low');

    const handleSubmit = (e) => {
        e.preventDefault();
        // Here you would typically handle the form submission, e.g., by calling an API
        console.log({ title, description, priority });
        // Reset form fields
        setTitle('');
        setDescription('');
        setPriority('low');
    };

    return (
        <form onSubmit={handleSubmit} className="ticket-form">
            <div>
                <label htmlFor="title">Title:</label>
                <input
                    type="text"
                    id="title"
                    value={title}
                    onChange={(e) => setTitle(e.target.value)}
                    required
                />
            </div>
            <div>
                <label htmlFor="description">Description:</label>
                <textarea
                    id="description"
                    value={description}
                    onChange={(e) => setDescription(e.target.value)}
                    required
                />
            </div>
            <div>
                <label htmlFor="priority">Priority:</label>
                <select
                    id="priority"
                    value={priority}
                    onChange={(e) => setPriority(e.target.value)}
                >
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                </select>
            </div>
            <button type="submit">Submit Ticket</button>
        </form>
    );
};

export default TicketForm;