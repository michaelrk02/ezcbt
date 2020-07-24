import {Component, createElement as $} from 'react';

import {call, deleteCookie} from '../lib/rpc.js';
import {ezRPC} from '../lib/ezrpc.js';

export default class Header extends Component {

    constructor(props) {
        super(props);
        this.state = {
            update: false,
            courseTitle: null,
            userName: null
        };

        this.onLogout = this.onLogout.bind(this);
    }

    componentDidMount() {
        this.setState({update: true});
    }

    componentDidUpdate() {
        if (this.state.update) {
            call(ezRPC('user/GetHeaderInfo'), null, (res => {
                if (res.code == 200) {
                    this.setState({update: false, courseTitle: res.value.course_title, userName: res.value.user_name});
                } else if (res.code == 401) {
                    this.setState({update: false, courseTitle: null, userName: null});
                } else {
                    window.alert('Gagal mendapatkan header info: ' + res.status + '. Mohon coba lagi');
                }
            }).bind(this));
        }
    }

    render() {
        return $('div', {className: 'columns bg-secondary', style: {boxShadow: '0px 0px 16px lightgray'}}, [
            $('div', {className: 'column bg-primary', style: {padding: '1rem'}}, [
                $('h3', null, 'Computer-Based Test'),
                $('h5', null, this.state.courseTitle !== null ? this.state.courseTitle : 'CBT application')
            ]),
            this.state.userName === null ?
                null :
                $('div', {className: 'column col-auto', style: {padding: '1rem', marginTop: 'auto', marginBottom: 'auto'}}, [
                    $('div', {className: 'dropdown dropdown-right'}, [
                        $('button', {type: 'button', className: 'btn btn-lg btn-secondary s-circle dropdown-toggle'}, $('i', {className: 'icon icon-people'})),
                        $('ul', {className: 'menu'}, [
                            $('li', {className: 'menu-item'}, $('b', null, this.state.userName)),
                            $('li', {className: 'divider', 'data-content': 'ACTIONS'}),
                            $('li', {className: 'menu-item'}, $('button', {type: 'button', className: 'btn btn-link text-error', onClick: this.onLogout}, 'Logout'))
                        ])
                    ])
                ])
        ]);
    }

    onLogout() {
        if (window.confirm('Apakah anda yakin ingin keluar? (anda akan kehilangan waktu jika anda masih mengerjakan tes ini)')) {
            this.setState({update: true});
            deleteCookie('_$auth_token');
            this.props.history.replace('/');
        }
    }

}

