import 'core-js/stable';

import {createElement as $, createRef} from 'react';
import {render} from 'react-dom';

import App from './user/App.js';

window.addEventListener('load', () => {
    const app = document.getElementById('app');

    render($(App, {basename: app.getAttribute('data-basename')}), app);
});

