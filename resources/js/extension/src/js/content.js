let $ = require('jquery')

chrome.runtime.onConnect.addListener(function(port) {
    port.onMessage.addListener(function(msg) {
        console.log(msg)
        if (!msg.pong) return
        setTimeout(() => {
            let search = $('html').html().indexOf(msg.search)
            port.postMessage({reply: true, result: search})
        }, 2000)
    });
});

chrome.runtime.sendMessage({ping: true})
