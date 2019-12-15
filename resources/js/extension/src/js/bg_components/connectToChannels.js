import setCheckers from './setCheckers'
import Echo from "laravel-echo"
window.io = require('socket.io-client');

let echo = null;
const presenceChannel = 'followTheCheckers';

function joinChannel(id) {
    if (!echo) {
        echo = new Echo({
            broadcaster: 'socket.io',
            host: 'http://checkextension.test:6001',
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
