import {Component, createElement as $} from 'react';

export default class Header extends Component {

    constructor(props) {
        super(props);
    }

    render() {
        return $('div', {className: 'bg-primary', style: {padding: '1rem'}}, [
            $('h3', null, 'Computer-Based Test'),
            $('h5', null, this.props.subtitle)
        ]);
    }

}

