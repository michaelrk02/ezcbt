import {Component, createElement as $, createRef} from 'react';

import {call} from '../lib/rpc.js';
import {ezRPC} from '../lib/ezrpc.js';

export default class Login extends Component {

    constructor(props) {
        super(props);
        this.state = {
            courseID: '',
            userID: '',
            userIDShown: false,
            loggingIn: false,
            courses: null
        };

        this.redirect = '/course';
        if ((typeof(this.props.location.state) === 'object') && (this.props.location.state !== null) && (typeof(this.props.location.state.redirect) === 'string')) {
            this.redirect = this.props.location.state.redirect;
        }

        this.onCourseChange = this.onCourseChange.bind(this);
        this.onUserIDChange = this.onUserIDChange.bind(this);
        this.onUserIDShowToggle = this.onUserIDShowToggle.bind(this);
        this.onLogin = this.onLogin.bind(this);
    }

    fetchCourses() {
        call(ezRPC('user/GetCourses'), {incl_locked: false}, (res => {
            if (res.code == 200) {
                const courses = [{id: '', title: '-- pilih materi --'}];
                for (let course of res.value) {
                    courses.push({id: course.course_id, title: course.title});
                }
                this.setState({courses: courses});
            } else {
                window.alert('Gagal mendapatkan list materi: ' + res.status + '. Mohon coba lagi');
            }
        }).bind(this));
    }

    componentDidMount() {
        call(ezRPC('user/AuthCheck'), null, (res => {
            if (res.code == 200) {
                this.props.location.replace(this.redirect);
            } else if (res.code != 401) {
                window.alert('Gagal mengecek login: ' + res.status);
            } else {
                this.fetchCourses();
            }
        }).bind(this));
    }

    render() {
        if (this.state.courses === null) {
            return $('div', null, 'Loading ...');
        }

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
                    $('div', {className: 'card-subtitle text-gray'}, 'Masukkan materi dan user ID di bawah')
                ]),
                $('form', {onSubmit: this.onLogin}, [
                    $('div', {className: 'card-body'}, [
                        $('div', {className: 'form-group'}, [
                            $('label', {className: 'form-label'}, 'Materi'),
                            $('select', {className: 'form-select', value: this.state.courseID, onChange: this.onCourseChange}, this.state.courses.map((course => {
                                return $('option', {value: course.id}, course.title);
                            }).bind(this)))
                        ]),
                        $('div', {className: 'form-group'}, [
                            $('label', {className: 'form-label'}, 'User ID'),
                            $('input', {type: this.state.userIDShown ? 'text' : 'password', className: 'form-input', style: {fontFamily: 'monospace'}, placeholder: 'Masukkan user ID', onChange: this.onUserIDChange}),
                            $('label', {className: 'form-checkbox'}, [
                                $('input', {type: 'checkbox', checked: this.state.userIDShown, onChange: this.onUserIDShowToggle}),
                                $('i', {className: 'form-icon'}),
                                ' Perlihatkan user ID'
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

    onCourseChange(e) {
        this.setState({courseID: e.target.value});
    }

    onUserIDChange(e) {
        this.setState({userID: e.target.value});
    }

    onUserIDShowToggle(e) {
        this.setState({userIDShown: e.target.checked});
    }

    onLogin(e) {
        e.preventDefault();

        this.setState({loggingIn: true});

        call(ezRPC('user/Login'), {course_id: this.state.courseID, user_id: this.state.userID}, (res => {
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

