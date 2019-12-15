import axios from 'axios';

const headers = {
    'SameSite': 'Lax'
};

const Parent = axios.create({
    headers,
});

class Http extends Parent { }

export default Http;

export const { CancelToken, isCancel } = axios;
