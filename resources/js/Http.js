import axios from 'axios';

const baseURL = window.App.base_url;

const headers = {
    'X-CSRF-TOKEN'    : document.head.querySelector('meta[name=csrf-token]').getAttribute('content'),
    'X-Requested-With': 'XMLHttpRequest',
};

const Parent = axios.create({
    baseURL,
    headers,
});

function refreshAppTokens() {
    Parent.get('/')
    .then(({ data }) => {
        const wrapper = document.createElement('div');
        wrapper.innerHTML = data;
        return wrapper.querySelector('meta[name=csrf-token]').getAttribute('content');
    })
    .then((token) => {
        Parent.defaults.headers['X-CSRF-TOKEN'] = token;
        document.querySelector('meta[name=csrf-token]').setAttribute('content', token);
        console.log('refreshed')
    })
}

setInterval(refreshAppTokens, 1000 * 60 * 60);

class Http extends Parent {};

export default Http;
