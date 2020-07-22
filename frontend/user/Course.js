import {Component, createElement as $} from 'react';

import {call} from '../lib/rpc.js';
import {ezRPC} from '../lib/ezrpc.js';
import {authCheck} from '../lib/user/auth-check.js';
import {secondsToHMS, formatHMS} from '../lib/user/';

export default class Course extends Component {

    constructor(props) {
        super(props);
        this.state = {
            course: null,
            duration: null,
            userStatus: null
        };

        this.onStart = this.onStart.bind(this);
    }

    fetchUserStatus() {
        call(ezRPC('user/GetStatus'), null, (res => {
            if (res.code == 200) {
                this.setState({userStatus: res.value});
            } else {
                window.alert('Gagal mendapatkan user status: ' + res.status + '. Mohon coba lagi');
            }
        }).bind(this));
    }

    componentDidMount() {
        authCheck(this);
    }

    render() {
        if ((this.state.course === null) || (this.state.userStatus === null)) {
            return $('div', null, 'Loading ...');
        }

        return $('div', {className: 'container grid-xl', style: {marginTop: '1rem', marginBottom: '1rem'}}, [
            $('h3', null, 'Materi: ' + this.state.course.title),
            $('p', null, this.state.course.description),
            $('p', null, [
                'Materi ini berisi sebanyak ', $('b', null, this.state.course.num_questions), ' soal dengan masing-masing terdiri dari ', $('b', null, this.state.course.num_choices), ' pilihan jawaban.'
                ' Anda mendapatkan skor ',
                $('b', null, this.state.course.score_correct), ' jika benar, ',
                $('b', null, this.state.course.score_empty), ' jika kosong, dan ',
                $('b', null, this.state.course.score_wrong), ' jika salah.',
                ' Jawaban soal ', $('b', null, this.state.course.allow_empty == 1 ? 'diperbolehkan' : 'tidak diperbolehkan'), ' diisi kosong.'
            ]),
            $('p', null, ['Waktu pengerjaan: ', $('b', null, formatHMS(this.state.duration))]),
            $('div', {className: 'bg-gray', style: {padding: '0.5rem'}}, [
                $('h5', null, 'Status pengerjaan'),
                (() => {
                    if (this.state.userStatus.state === 'not_started') {
                        return $('p', null, 'Anda belum mulai mengerjakan. Silakan klik tombol di bawah untuk memulai');
                    } else if (this.state.userStatus.state === 'started') {
                        return $('p', null, [
                            'Anda sedang mengerjakan tes ini.',
                            ' Waktu tersisa: ', $('b', null, formatHMS(secondsToHMS(this.state.userStatus.seconds_left))), '.',
                            ' Jumlah soal terjawab: ', this.state.userStatus.num_answered, ' dari ', this.state.course.num_questions, ' soal.',
                            ' Klik tombol di bawah untuk melanjutkan'
                        ]);
                    } else if (this.state.userStatus.state === 'finished') {
                        return $('p', null, [
                            'Anda telah menyelesaikan tes ini.',
                            ' Jumlah soal terjawab: ', this.state.userStatus.num_answered, ' dari ', this.state.course.num_questions, ' soal.'
                        ]);
                    }
                    return null;
                }).bind(this))()
            ]),
            $('div', null, [
                $('button', {className: 'btn btn-primary', disabled: this.state.userStatus.state === 'finished', onClick: this.onStart}, this.state.userStatus.state === 'started' ? 'Lanjutkan' : 'Mulai')
            ])
        ]);
    }

    onAuthVerified() {
        call(ezRPC('user/GetCourseDetails'), null, (res => {
            if (res.code == 200) {
                this.setState({course: res.value, duration: secondsToHMS(res.value.duration)});
                window.appRef.current.setSubtitle(res.value.title, null);
                this.fetchUserStatus();
            } else {
                window.alert('Gagal mendapatkan keterangan materi: ' + res.status + '. Mohon coba lagi');
            }
        }).bind(this));
    }

    onStart() {
        if (window.confirm('Apakah anda yakin?')) {
            this.props.history.push('/quiz');
        }
    }

}

