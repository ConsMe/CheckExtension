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
