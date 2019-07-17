let $ = require('jquery')

chrome.runtime.onConnect.addListener(function(port) {
    port.postMessage({m: 'hello'})
    port.onMessage.addListener(function(msg) {
        console.log(msg)
        if (!msg.pong) return
        setTimeout(() => {
            let search = $(document).text().indexOf(msg.search)
            port.postMessage({reply: true, result: search})
        }, 2000);
    });
});

chrome.runtime.sendMessage({ping: true})
