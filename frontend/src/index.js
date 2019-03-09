import React from 'react';
import ReactDOM from 'react-dom';
import * as serviceWorker from './serviceWorker';
import { createStore } from "redux";
import createHistory from 'history/createBrowserHistory';
import { Provider } from 'react-redux';
import { Router, Route } from 'react-router';

import App from "./components/App";


const store = createStore(
    state => state
);
const history = createHistory();

ReactDOM.render((
    <Provider store={store}>
        <Router history={history}>
            <Route path="/" component={App} />
        </Router>
    </Provider>
), document.getElementById('root'));
//ReactDOM.render(<App />, document.getElementById('root'));

// If you want your app to work offline and load faster, you can change
// unregister() to register() below. Note this comes with some pitfalls.
// Learn more about service workers: https://bit.ly/CRA-PWA
serviceWorker.unregister();
