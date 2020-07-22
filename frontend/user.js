import {createElement as $, createRef} from 'react';
import {render} from 'react-dom';

import App from './user/App.js';

window.appRef = createRef();

window.addEventListener('load', () => {
    const app = document.getElementById('app');

    render($(App, {ref: window.appRef, basename: app.getAttribute('data-basename')}), app);
});

