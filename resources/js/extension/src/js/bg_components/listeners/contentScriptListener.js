import sendResult from '../sendResult'
import getChecker from '../getChecker'
import getCurrentTime from '../getCurrentTime'
import {timeDifference} from '../getTimeDifference'


chrome.runtime.onMessage.addListener(async function(request, sender, sendResponse) {
    // if (!request.ping) return
    if (!sender.tab || !sender.tab.id || !sender.tab.pinned) return
    let checker = await getChecker(sender)
    if (!checker) return
    let port = chrome.tabs.connect(sender.tab.id)
    port.postMessage({pong: true, search: checker.search});
    port.onMessage.addListener(function(msg) {
        if (!msg.reply) return
        let correctedTime = new Date((getCurrentTime() - timeDifference) * 1000).setSeconds(0,0)
        let correctedTimeSeconds = new Date((getCurrentTime() - timeDifference) * 1000).getSeconds()
        if (correctedTime == checker.lastCheck && correctedTimeSeconds < 50) {
            sendResult(msg.result, checker)
            checker.waitingForAnswer = false
            let key = 'checker' + checker.id
            let ins = {}
            ins[key] = checker
            chrome.storage.local.set(ins)
        }
    })
})
