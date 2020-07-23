import {Component, createElement as $, createRef} from 'react';
import {BrowserRouter as Router, Route, Switch} from 'react-router-dom';

import Header from './Header.js';
import Footer from './Footer.js';

import Landing from './Landing.js';
import Login from './Login.js';
import Course from './Course.js';
import Quiz from './Quiz.js';

window.header = createRef();

export default class App extends Component {

    constructor(props) {
        super(props);
    }

    render() {
        return $(Router, {basename: this.props.basename}, [
            $('div', {className: 'app-container'}, [
                $('div', {className: 'app-content'}, [
                    $(Route, {path: '/', render: ({history}) => $(Header, {history: history, ref: window.header})}),
                    $(Switch, null, [
                        $(Route, {exact: true, path: '/', component: Landing}),
                        $(Route, {exact: true, path: '/login', component: Login}),
                        $(Route, {exact: true, path: '/course', component: Course}),
                        $(Route, {exact: true, path: '/quiz', component: Quiz})
                    ])
                ]),
                $(Footer)
            ])
        ]);
    }

}

