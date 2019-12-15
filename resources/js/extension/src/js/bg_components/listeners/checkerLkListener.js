import setCheckers from '../setCheckers'
import clearAll from '../clearAll'
import { joinChannel } from '../connectToChannels';
import { leaveChannel } from '../connectToChannels';

/* Communicate with checker LK */
chrome.runtime.onMessageExternal.addListener(function(request, sender, sendResponse) {
    if (request.isSettedUp) {
        let version = chrome.runtime.getManifest().version
        chrome.storage.local.set({url: request.url})
        joinChannel(request.id);
        sendResponse({settedUp: true, version: version})
        setCheckers(request.checkers)
    }
    else if (request.refreshCheckers) {
        setCheckers(request.checkers)
    }
    else if (request.exit) {
        clearAll()
        leaveChannel(request.id)
        sendResponse({ok: true})
    }
})
