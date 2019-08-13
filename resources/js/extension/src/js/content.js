let $ = require('jquery')
console.log(1)
chrome.runtime.onConnect.addListener(function(port) {
    console.log(2)
    port.postMessage({m: 'hello'})
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
