const cookieKey = '_$rpc_cookie';

function getCookieObject() {
    const item = window.sessionStorage.getItem(cookieKey);
    if ((item !== null) && (typeof(item) === 'string')) {
        try {
            const decoded = window.atob(item);
            return JSON.parse(decoded);
        } catch (e) {
        }
    }
    return null;
}

function setCookieObject(cookie) {
    if (typeof(cookie) === 'object') {
        const json = JSON.stringify(cookie);
        window.sessionStorage.setItem(cookieKey, window.btoa(json));
    } else {
        throw 'argument is not an object';
    }
}

export function getCookie(key) {
    const cookie = getCookieObject();
    if (cookie !== null) {
        return (cookie[key] !== undefined) ? cookie[key] : null;
    }
    return null;
}

export function setCookie(key, value) {
    let cookie = getCookieObject();
    if (cookie === null) {
        cookie = {};
    }
    cookie[key] = value;
    setCookieObject(cookie);
}

export function deleteCookie(key) {
    const cookie = getCookieObject();
    delete cookie[key];
    setCookieObject(cookie);
}

export function call(address, args, callback) {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', address);
    xhr.responseType = 'json';

    const cookie = window.sessionStorage.getItem(cookieKey);
    if (cookie !== null) {
        xhr.setRequestHeader('X-RPC-Cookie', cookie);
    }
    xhr.addEventListener('load', () => {
        const res = {};
        res.code = xhr.status;
        res.status = xhr.statusText;
        res.value = xhr.response;

        if ((typeof(res.value) === 'object') && (res.value !== null)) {
            res.value = res.value.__value;
            setCookieObject(JSON.parse(res.value.__cookie));
        }

        if (typeof(callback) === 'function') {
            callback(res);
        }
    });
}

