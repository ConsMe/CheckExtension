import chromep from 'chrome-promise'

export default async function () {
    let allTabs = await chromep.tabs.query({pinned: true})
    if (!allTabs.length) return
    allTabs = allTabs.map(tab => tab.id)
    let storage = await chromep.storage.local.get(null)
    let tabsIds = Object.keys(storage).filter(key => key.indexOf('checker') >= 0).map(c => {
        return storage[c].tabId
    })
    allTabs.forEach(t => {
        if (!tabsIds.includes(t)) {
            chrome.tabs.remove(t)
        }
    })
}
