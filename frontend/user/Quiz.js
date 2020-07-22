import {Component, createElement as $} from 'react';

import {authCheck} from '../lib/user/auth-check.js';

export default class Quiz extends Component {

    constructor(props) {
        super(props);
    }

    componentDidMount() {
        authCheck(this);
    }

    render() {
        return $('div', null, 'quiz page');
    }

}

