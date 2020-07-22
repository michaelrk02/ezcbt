import {call, deleteCookie} from '../rpc.js';
import {ezRPC} from '../ezrpc.js';

export function authCheck(self) {
    call(ezRPC('user/AuthCheck'), null, res => {
        if (res.code != 200) {
            if (res.code != 401) {
                deleteCookie('_$auth_token');
                window.alert('Sesi anda dicabut: ' + res.status + '. Harap login kembali');
            }
            self.props.history.replace('/login', {redirect: self.props.location.pathname});
        } else {
            if (typeof(self.onAuthVerified) === 'function') {
                self.onAuthVerified();
            }
        }
    });
}

