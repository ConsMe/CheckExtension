import chromep from 'chrome-promise'

export default async function getChecker(sender) {
    let storage = await chromep.storage.local.get(null)
    let keys = Object.keys(storage).filter(key => storage[key].waitingForAnswer && storage[key].tabId == sender.tab.id)
    if (!keys.length) {
        return false
    }
    let checker = storage[keys[0]]
    return checker
}
