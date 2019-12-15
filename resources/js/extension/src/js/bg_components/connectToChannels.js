import setCheckers from './setCheckers'
import chromep from 'chrome-promise'
import Echo from "laravel-echo"
window.io = require('socket.io-client');

let echo = null;
const presenceChannel = 'followTheCheckers';

async function joinChannel(id) {
    if (!echo) {
        let url = await chromep.storage.local.get('url')
        url = url.url
        if (url.substr(url.length - 1, 1) === '/') {
            url = url.substr(0, url.length - 2)
        }
        echo = new Echo({
            broadcaster: 'socket.io',
            host: `${url}:6001`,
        });
        echo.join(presenceChannel);
        echo.private(`checker.${id}`)
            .listen('.checker.message', (e) => {
                setCheckers(e.checkers)
            }).listen('.checker.disconnect', () => {
                leaveChannel(id)
            });
    }
}

function leaveChannel(id) {
    if (echo) {
        echo.leave(presenceChannel);
        echo.leave(`checker.${id}`);
        echo = null;
    }
}

export { joinChannel, leaveChannel }
