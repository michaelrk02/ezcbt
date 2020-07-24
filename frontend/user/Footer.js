import {Component, createElement as $} from 'react';

import {call} from '../lib/rpc.js';
import {ezRPC} from '../lib/ezrpc.js';

export default class Footer extends Component {

    constructor(props) {
        super(props);
        this.state = {
            info: null
        };
    }

    componentDidMount() {
        call(ezRPC('user/GetFooterInfo'), null, (res => {
            if (res.code == 200) {
                this.setState({info: res.value});
            } else {
                window.alert('Gagal mendapatkan footer info: ' + res.status);
            }
        }).bind(this));
    }

    render() {
        return $('div', {className: 'bg-dark', style: {padding: '0.5rem'}}, [
            this.state.info === null ?
                $('div', {className: 'text-center'}, 'Copyright belongs to developer') :
                $('div', {className: 'text-center'}, [
                    'Copyright ',
                    $('span', {dangerouslySetInnerHTML: {__html: '&copy;'}}),
                    ' ' + this.state.info.copyright_year + ', ' + this.state.info.copyright_owner
                ])
        ]);
    }

}

