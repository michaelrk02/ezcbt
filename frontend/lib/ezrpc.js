let address = null;

export function ezRPC(method) {
    return address + '/' + method;
}

window.addEventListener('load', () => {
    address = document.getElementById('app').getAttribute('data-rpc');
});

