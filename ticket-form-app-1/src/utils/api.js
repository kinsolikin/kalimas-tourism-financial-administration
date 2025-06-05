import axios from 'axios';

const API_URL = 'https://your-api-url.com/api/tickets';

export const fetchTickets = async () => {
    try {
        const response = await axios.get(API_URL);
        return response.data;
    } catch (error) {
        throw new Error('Error fetching tickets: ' + error.message);
    }
};

export const submitTicket = async (ticketData) => {
    try {
        const response = await axios.post(API_URL, ticketData);
        return response.data;
    } catch (error) {
        throw new Error('Error submitting ticket: ' + error.message);
    }
};

export const fetchTicketDetails = async (ticketId) => {
    try {
        const response = await axios.get(`${API_URL}/${ticketId}`);
        return response.data;
    } catch (error) {
        throw new Error('Error fetching ticket details: ' + error.message);
    }
};