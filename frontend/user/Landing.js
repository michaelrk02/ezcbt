import {Component, createElement as $} from 'react';

import {authCheck} from '../lib/user/auth-check.js';

export default class Landing extends Component {

    constructor(props) {
        super(props);

        this.onAuthVerified = this.onAuthVerified.bind(this);
    }

    componentDidMount() {
        authCheck(this);
    }

    render() {
        return null;
    }

    onAuthVerified() {
        this.props.history.replace('/course');
    }

}

