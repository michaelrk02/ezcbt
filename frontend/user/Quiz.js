import {Component, createElement as $} from 'react';

import {authCheck} from '../lib/user/auth-check.js';
import {call} from '../lib/rpc.js';
import {ezRPC} from '../lib/ezrpc.js';
import {formatHMS, secondsToHMS} from '../lib/utility.js';

export default class Quiz extends Component {

    constructor(props) {
        super(props);
        this.state = {
            course: null,
            coursePDFURL: null,
            secondsLeft: 0,
            activeTab: 'questions',
            answerData: null
        };

        this.tabs = [
            {id: 'details', name: 'Informasi Tes'},
            {id: 'questions', name: 'Soal'},
            {id: 'answers', name: 'Jawaban'}
        ];
        for (let tab of this.tabs) {
            tab.onClick = (() => {
                this.setState({activeTab: tab.id});
            }).bind(this);
        }

        this.timerID = null;

        this.onMark = [];
        this.onFinish = this.onFinish.bind(this);
    }

    fetchUserStatus() {
        call(ezRPC('user/GetStatus'), null, (res => {
            if (res.code == 200) {
                const status = res.value;
                if (status.state === 'started') {
                    this.setState({secondsLeft: status.seconds_left});
                    this.fetchCourseInfo();
                } else {
                    window.alert('Anda tidak sedang mengerjakan tes ini');
                    this.props.history.replace('/course');
                }
            } else {
                window.alert('Gagal mendapatkan user status: ' + res.status + '. Mohon coba lagi');
            }
        }).bind(this));
    }

    fetchCourseInfo() {
        call(ezRPC('user/GetCourseDetails'), null, (res => {
            if (res.code == 200) {
                this.setState({course: res.value});
                const choices = ['-'];
                for (let i = 0; i < res.value.num_choices; i++) {
                    choices.push(i);
                }
                for (let i = 0; i < res.value.num_questions; i++) {
                    this.onMark.push((e => {
                        call(ezRPC('user/Mark'), {question_id: i, choice_id: e.target.value}, (res => {
                            if (res.code == 200) {
                                this.fetchAnswerData();
                            } else {
                                window.alert('Gagal menulis jawaban: ' + res.status + '. Mohon coba lagi');
                            }
                        }).bind(this));
                    }).bind(this));
                }
                this.fetchCoursePDFURL();
            } else {
                window.alert('Gagal mendapatkan detail tes: ' + res.status + '. Mohon coba lagi');
            }
        }).bind(this));
    }

    fetchCoursePDFURL() {
        call(ezRPC('user/GetCoursePDFURL'), null, (res => {
            if (res.code == 200) {
                this.setState({coursePDFURL: res.value});
                this.initializeTimer();
                this.fetchAnswerData();
            } else {
                window.alert('Gagal mendapatkan URL PDF: ' + res.status + '. Mohon coba lagi');
            }
        }).bind(this));
    }

    fetchAnswerData() {
        call(ezRPC('user/GetAnswerData'), null, (res => {
            if (res.code == 200) {
                this.setState({answerData: res.value});
            } else {
                window.alert('Gagal mendapatkan data jawaban: ' + res.status + '. Mohon coba lagi');
            }
        }).bind(this))
    }

    initializeTimer() {
        this.timerID = window.setInterval((() => {
            if (this.state.secondsLeft % 30 == 0) {
                call(ezRPC('user/GetStatus'), null, (res => {
                    if (res.code == 200) {
                        this.setState({secondsLeft: res.value.seconds_left});
                    }
                }).bind(this));
            }

            const secs = this.state.secondsLeft - 1;
            if (secs >= 0) {
                this.setState({secondsLeft: secs});
            } else {
                this.setState({secondsLeft: 0});
                window.clearInterval(this.timerID);
            }
        }).bind(this), 1000);
    }

    getChoiceStr(choiceID) {
        if (choiceID !== '-') {
            return String.fromCharCode(0x41 + parseInt(choiceID));
        }
        return '(kosong)';
    }

    componentDidMount() {
        authCheck(this);
    }

    render() {
        if ((this.state.course === null) || (this.state.coursePDFURL === null)) {
            return $('div', null, 'loading');
        }

        return $('div', {className: 'container grid-lg', style: {marginTop: '2rem', marginBottom: '2rem'}}, [
            $('div', {className: 'panel'}, [
                $('div', {className: 'panel-header'}, [
                    $('h5', {className: 'panel-title text-center'}, this.state.course.title),
                    $('h5', {className: 'panel-title text-center'}, this.renderTimer())
                ]),
                $('div', {className: 'panel-nav'}, [
                    $('ul', {className: 'tab tab-block'}, this.tabs.map((tab => {
                        return $('li', {className: 'tab-item' + (this.state.activeTab === tab.id ? ' active' : '')}, $('a', {className: 'btn btn-link', onClick: tab.onClick}, tab.name));
                    }).bind(this)))
                ]),
                $('div', {className: 'panel-body', style: {height: '768px'}}, [
                    $('div', {style: {margin: '0.5rem', overflowY: 'auto'}}, [
                        $('div', {className: 'bg-gray', style: {padding: '0.5rem', display: this.state.activeTab === 'details' ? 'block' : 'none'}}, [
                            $('p', null, this.state.course.description),
                            $('p', null, 'Jumlah soal: ' + this.state.course.num_questions + ' @ ' + this.state.course.num_choices + ' pilihan'),
                            $('p', null, 'Skor: ' + this.state.course.score_correct + ' (benar), ' + this.state.course.score_empty + ' (kosong), ' + this.state.course.score_wrong + ' (salah)'),
                            $('p', null, 'Pilihan boleh kosong: ' + (this.state.course.allow_empty == 1 ? 'ya' : 'tidak')),
                            $('p', null, 'Waktu pengerjaan: ' + formatHMS(secondsToHMS(this.state.course.duration)))
                        ]),
                        $('iframe', {style: {display: this.state.activeTab === 'questions' ? 'block' : 'none'}, src: this.state.coursePDFURL, width: '100%', height: '90%%'}),
                        $('div', {style: {display: this.state.activeTab === 'answers' ? 'block' : 'none'}}, this.renderAnswers())
                    ])
                ]),
                $('div', {className: 'panel-footer'}, [
                    $('button', {className: 'btn btn-success btn-block', onClick: this.onFinish}, 'Selesai')
                ])
            ])
        ]);
    }

    renderTimer() {
        const hms = secondsToHMS(this.state.secondsLeft);
        let color = 'success';
        if ((60 <= this.state.secondsLeft) && (this.state.secondsLeft < 900)) {
            color = 'warning';
        } else if (this.state.secondsLeft < 60) {
            color = 'error';
        }

        hms.hours = hms.hours.toString().padStart(2, '0');
        hms.minutes = hms.minutes.toString().padStart(2, '0');
        hms.seconds = hms.seconds.toString().padStart(2, '0');
        const str = hms.hours + ':' + hms.minutes + ':' + hms.seconds;

        return $('span', {style: {fontFamily: 'monospace'}, className: 'label label-rounded label-' + color}, str);
    }

    renderAnswers() {
        if (this.state.answerData === null) {
            return $('div', null, 'Loading ...');
        }

        const elements = [];
        for (let i = 0; i < this.state.course.num_questions; i++) {
            const choiceID = this.state.answerData.charAt(i);

            const options = [{id: '-', name: this.getChoiceStr('-')}];
            for (let j = 0; j < this.state.course.num_choices; j++) {
                options.push({id: j, name: this.getChoiceStr(j)});
            }

            elements.push($('div', {className: 'column col-6 col-sm-12'}, [
                $('div', {className: 'columns', style: {margin: '0.2rem'}}, [
                    $('div', {className: 'column col-3'}, (i + 1) + '. '),
                    $('div', {className: 'column col-9 input-group'}, [
                        $('span', {className: 'input-group-addon'}, 'Jawaban:'),
                        $('select', {className: 'form-select', value: this.state.answerData.charAt(i), onChange: this.onMark[i]}, options.map(opt => $('option', {value: opt.id}, opt.name)))
                    ])
                ])
            ]));
        }
        return $('div', {className: 'columns'}, elements);
    }

    onAuthVerified() {
        this.fetchUserStatus();
    }

    onFinish() {
        if (window.confirm('Apakah anda yakin?')) {
            call(ezRPC('user/Finish'), null, (res => {
                if (res.code == 200) {
                    this.props.history.replace('/course');
                } else {
                    window.alert('Tidak dapat menyelesaikan tes: ' + res.status);
                }
            }).bind(this));
        }
    }

}

