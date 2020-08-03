import {Component, createElement as $, createRef} from 'react';

import {call} from '../lib/rpc.js';
import {ezRPC} from '../lib/ezrpc.js';

export default class Login extends Component {

    constructor(props) {
        super(props);
        this.state = {
            sessionID: '',
            sessionIDShown: false,
            loggingIn: false
        };

        this.redirect = '/course';
        if ((typeof(this.props.location.state) === 'object') && (this.props.location.state !== null) && (typeof(this.props.location.state.redirect) === 'string')) {
            this.redirect = this.props.location.state.redirect;
        }

        this.onSessionIDChange = this.onSessionIDChange.bind(this);
        this.onSessionIDShowToggle = this.onSessionIDShowToggle.bind(this);
        this.onLogin = this.onLogin.bind(this);
    }

    componentDidMount() {
        call(ezRPC('user/AuthCheck'), null, (res => {
            if (res.code == 200) {
                this.props.location.replace(this.redirect);
            } else if (res.code != 401) {
                window.alert('Gagal mengecek login: ' + res.status);
            }
        }).bind(this));
    }

    render() {
        return $('div', {className: 'container grid-xs', style: {marginTop: '2rem', marginBottom: '2rem'}}, [
            $('div', {className: 'card'}, [
                $('div', {className: 'card-image'}, [
                    $('div', {className: 'empty'}, [
                        $('div', {className: 'empty-icon'}, [
                            $('i', {className: 'icon icon-people icon-4x'})
                        ])
                    ])
                ]),
                $('div', {className: 'card-header'}, [
                    $('div', {className: 'card-title h5'}, 'Login'),
                    $('div', {className: 'card-subtitle text-gray'}, 'Masukkan token atau ID sesi di bawah')
                ]),
                $('form', {onSubmit: this.onLogin}, [
                    $('div', {className: 'card-body'}, [
                        $('div', {className: 'form-group'}, [
                            $('label', {className: 'form-label'}, 'Token'),
                            $('input', {type: this.state.sessionIDShown ? 'text' : 'password', className: 'form-input', style: {fontFamily: 'monospace'}, placeholder: 'Masukkan token', onChange: this.onSessionIDChange}),
                            $('label', {className: 'form-checkbox'}, [
                                $('input', {type: 'checkbox', checked: this.state.sessionIDShown, onChange: this.onSessionIDShowToggle}),
                                $('i', {className: 'form-icon'}),
                                ' Perlihatkan token'
                            ])
                        ])
                    ]),
                    $('div', {className: 'card-footer'}, [
                        $('button', {className: 'btn btn-success btn-block', disabled: (this.state.courseID === '') || (this.state.userID === '') || this.state.loggingIn}, this.state.loggingIn ? 'Logging in ...' : 'Login')
                    ])
                ])
            ])
        ]);
    }

    onSessionIDChange(e) {
        this.setState({sessionID: e.target.value});
    }

    onSessionIDShowToggle(e) {
        this.setState({sessionIDShown: e.target.checked});
    }

    onLogin(e) {
        e.preventDefault();

        this.setState({loggingIn: true});

        call(ezRPC('user/Login'), {session_id: this.state.sessionID}, (res => {
            if (res.code == 200) {
                window.alert('Login berhasil. Selamat datang, ' + res.value.name + '!');
                window.header.current.setState({update: true});
                this.props.history.replace(this.redirect);
            } else {
                window.alert('Tidak dapat melakukan login: ' + res.status);
                this.setState({loggingIn: false});
            }
        }).bind(this));
    }

}

