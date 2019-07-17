const $ = require('jquery')
const axios = window.axios = require('axios')
const CancelToken = axios.CancelToken;
const sourceAxios = CancelToken.source();
import chromep from 'chrome-promise';

const url = 'http://checkextension.test/'

chrome.browserAction.onClicked.addListener(function(tab) {
    chromep.tabs.create({url: `${url}`})
});

chrome.runtime.onMessageExternal.addListener(function(request, sender, sendResponse) {
    console.log(request)
    if (request.isSettedUp) {
        let version = chrome.runtime.getManifest().version
        sendResponse({settedUp: true, version: version})
        setCheckers(request.checkers)
    }
    else if (request.refreshCheckers) {
        setCheckers(request.checkers)
    }
    else if (request.exit) {
        clearAll()
        sendResponse({ok: true})
    }
})

async function setCheckers(outCheckers) {
    let inCheckers = await chromep.storage.local.get(null)
    inCheckers = Object.keys(inCheckers).filter(key => key.indexOf('checker') >= 0).map(key => inCheckers[key])
    let inIds = inCheckers.map(checker => checker.id)
    let outIds = outCheckers.map(checker => checker.id)
    inIds.forEach((id,i) => {
        if (!outIds.includes(id) || !outCheckers[outIds.indexOf(id)].isworking) {
            deleteChecker(inCheckers[i])
        }
    })
    outIds.forEach((id,i) => {
        if (!inIds.includes(id) && outCheckers[i].isworking) {
            createChecker(outCheckers[i])
        }
    })
}

async function createChecker(checker) {
    let key = 'checker' + checker.id
    chrome.alarms.create(key, {periodInMinutes: checker.interval})
    checkSite(checker)
}

async function deleteChecker(checker) {
    let key = 'checker' + checker.id
    await chromep.storage.local.remove(key)
    if (await chromep.alarms.get(key)) {
        chrome.alarms.clear(key)
    }
    let allTabs = await chromep.tabs.query({pinned: true})
    if (allTabs.length) {
        allTabs = allTabs.map(tab => tab.id)
    }
    let isTab = checker.tabId ? allTabs.includes(checker.tabId) : false
    if (isTab) {
        chromep.tabs.remove(checker.tabId)
    }
}

chrome.alarms.onAlarm.addListener(async alarm => {
    let key = alarm.name
    let checker = await chromep.storage.local.get(key)
    if (!checker || !checker[key] || !checker[key].isworking) {
        deleteChecker(checker[key])
        return
    }
    checkSite(checker[key])
})

chrome.runtime.onMessage.addListener(async function(request, sender, sendResponse) {
    console.log(request, sender)
    if (!sender.tab || !sender.tab.id) return
    let checker = await getChecker(sender)
    if (!checker) return
    if (request.ping) {
        let port = chrome.tabs.connect(sender.tab.id)
        port.postMessage({pong: true, search: checker.search});
        port.onMessage.addListener(function(msg) {
            if (!msg.reply) return
            console.log(msg.result)
            sendResult(msg.result, checker)
        });
    }
})


async function checkSite(checker) {
    let allTabs = await chromep.tabs.query({pinned: true})
    if (allTabs.length) {
        allTabs = allTabs.map(tab => tab.id)
    }
    let isTab = checker.tabId ? allTabs.includes(checker.tabId) : false
    if (!isTab) {
        let tab = await chromep.tabs.create({
            index: 0,
            pinned: true,
            active: false,
            selected: false
        })
        checker.tabId = tab.id
    }
    checker.waitingForAnswer = true
    let key = 'checker' + checker.id
    let ins = {}
    ins[key] = checker
    await chromep.storage.local.set(ins)
    await chromep.tabs.update(checker.tabId, {url: ''})
    await chromep.tabs.update(checker.tabId, {url: checker.url})
}


async function getChecker(sender) {
    let storage = await chromep.storage.local.get(null)
    let keys = Object.keys(storage).filter(key => storage[key].waitingForAnswer && storage[key].tabId == sender.tab.id)
    if (!keys.length) {
        return false
    }
    let checker = storage[keys[0]]
    return checker
}

async function sendResult(result, checker) {
    let code = result > 0 ? 1 : 0
    axios.post(url + 'writelog', {code: code, id: checker.id}, {cancelToken: sourceAxios.token})
    .then(r => {
        if (r.status != 200) {
            sourceAxios.cancel()
            clearAll()
            return false
        }
        console.log(r.data)
        setCheckers(r.data)
    })
    .catch(async e => {
        if (e.response.status == 401) {
            clearAll()
        }
    })
}

async function onStart() {
    let storage = await chromep.storage.local.get(null)
    let alarms = await chromep.alarms.getAll()
    let alarmsName = alarms.map(alarm => alarm.name)
    Object.keys(storage).filter(key => key.indexOf('checker') >= 0).forEach(key => {
        if (!alarmsName.includes(key)) {
            createChecker(storage[key])
        }
    })
}

chrome.windows.onRemoved.addListener(async e => {
    e.preventDefault()
    let storage = await chromep.storage.local.get(null)
    let tabs = await chromep.tabs.getAll()
    let tabsId = tabs.map(tab => tab.id)
    Object.keys(storage).filter(key => key.indexOf('checker') >= 0).forEach(key => {
        if (storage[key].tabId && tabsId.includes(storage[key].tabId)) {
            chrome.tabs.remove(storage[key].tabId)
        }
    })
    return true
})


async function clearAll() {
    let storage = await chromep.storage.local.get(null)
    Object.keys(storage).filter(key => key.indexOf('checker') >= 0).forEach(key => {
        deleteChecker(storage[key])
    })
}

onStart()
