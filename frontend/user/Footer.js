import {Component, createElement as $} from 'react';

export default class Footer extends Component {

    constructor(props) {
        super(props);
    }

    render() {
        return $('div', {className: 'bg-dark', style: {padding: '0.5rem'}}, [
            $('h5', {className: 'text-center'}, 'Didukung oleh Admin SMAGA'),
            $('div', {className: 'text-center'}, [
                'Copyright ',
                $('span', {dangerouslySetInnerHTML: {__html: '&copy;'}}),
                ' 2020, Michael R. Krisnadhi'
            ])
        ]);
    }

}

