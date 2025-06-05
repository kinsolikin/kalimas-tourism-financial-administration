import React from 'react';
import { BrowserRouter as Router, Route, Switch } from 'react-router-dom';
import Home from './pages/Home';
import TicketDetails from './pages/TicketDetails';

function App() {
    return (
        <Router>
            <Switch>
                <Route path="/" exact component={Home} />
                <Route path="/ticket/:id" component={TicketDetails} />
            </Switch>
        </Router>
    );
}

export default App;