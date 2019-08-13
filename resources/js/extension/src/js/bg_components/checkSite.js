import chromep from 'chrome-promise'

export default async function checkSite(checker) {
    let allTabs = await chromep.tabs.query({pinned: true})
    if (allTabs.length) {
        allTabs = allTabs.map(tab => tab.id)
    }
    let isTab = checker.tabId ? allTabs.includes(checker.tabId) : false
    if (!isTab) {
        let storage = await chromep.storage.local.get(null)
        let tabsIds = Object.keys(storage).filter(key => key.indexOf('checker') >= 0).map(c => {
            return storage[c].tabId
        })
        allTabs.forEach(t => {
            if (!tabsIds.includes(t)) {
                chrome.tabs.remove(t)
            }
        })
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
