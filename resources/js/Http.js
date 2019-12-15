import axios from 'axios';

const baseURL = window.location.origin;
let lastTimestamp = new Date().getTime();

const headers = {
    'X-CSRF-TOKEN': document.head.querySelector('meta[name=csrf-token]').getAttribute('content'),
    'X-Requested-With': 'XMLHttpRequest',
};

const Parent = axios.create({
    baseURL,
    headers,
});

function setToken(token) {
    Parent.defaults.headers['X-CSRF-TOKEN'] = token;
    document.querySelector('meta[name=csrf-token]').setAttribute('content', token);
}

Parent.interceptors.response.use((response) => {
    if (response.headers && response.headers['new-token']) {
        if (response.headers['new-token'].length) {
            setToken(response.headers['new-token']);
        }
    }
    return response;
}, error => Promise.reject(error));

function refreshAppTokens() {
    Parent.get('/')
        .then(({ data }) => {
            const wrapper = document.createElement('div');
            wrapper.innerHTML = data;
            return wrapper.querySelector('meta[name=csrf-token]').getAttribute('content');
        })
        .then((token) => {
            if (!token || !token.length) return;
            setToken(token);
            lastTimestamp = new Date().getTime();
        });
}

function isTimeToRefresh() {
    if ((new Date().getTime() - lastTimestamp) > 1000 * 60 * 60) {
        refreshAppTokens();
    }
}

setInterval(isTimeToRefresh, 1000 * 60 * 5);
document.addEventListener('visibilitychange', () => {
    if (document.visibilityState === 'visible') {
        isTimeToRefresh();
    }
});

class Http extends Parent { }

export default Http;

export const { CancelToken, isCancel } = axios;


// import axios from 'axios';

// const baseURL = window.App.base_url;

// const headers = {
//     'X-CSRF-TOKEN'    : document.head.querySelector('meta[name=csrf-token]').getAttribute('content'),
//     'X-Requested-With': 'XMLHttpRequest',
// };

// const Parent = axios.create({
//     baseURL,
//     headers,
// });

// function refreshAppTokens() {
//     Parent.get('/')
//     .then(({ data }) => {
//         const wrapper = document.createElement('div');
//         wrapper.innerHTML = data;
//         return wrapper.querySelector('meta[name=csrf-token]').getAttribute('content');
//     })
//     .then((token) => {
//         Parent.defaults.headers['X-CSRF-TOKEN'] = token;
//         document.querySelector('meta[name=csrf-token]').setAttribute('content', token);
//         console.log('refreshed')
//     })
// }

// setInterval(refreshAppTokens, 1000 * 60 * 60);

// class Http extends Parent {};

// export default Http;
