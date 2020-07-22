import {Component, createElement as $} from 'react';
import {BrowserRouter as Router, Route, Switch} from 'react-router-dom';

import Header from './Header.js';
import Footer from './Footer.js';

import Landing from './Landing.js';
import Login from './Login.js';
import Course from './Course.js';
import Quiz from './Quiz.js';

window.defaultAppSubtitle = 'CBT application';
window.defaultAppIcon = null;

export default class App extends Component {

    constructor(props) {
        super(props);
        this.state = {
            subtitle: window.defaultAppSubtitle,
            icon: window.defaultAppIcon
        };
    }

    render() {
        return $(Router, {basename: this.props.basename}, [
            $('div', {className: 'app-container'}, [
                $('div', {className: 'app-content'}, [
                    $(Header, {subtitle: this.state.subtitle, icon: this.state.icon}),
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

    setSubtitle(subtitle, icon) {
        this.setState({subtitle: subtitle, icon: icon});
    }

    resetSubtitle() {
        this.setSubtitle(window.defaultAppSubtitle, window.defaultAppIcon);
    }

}

