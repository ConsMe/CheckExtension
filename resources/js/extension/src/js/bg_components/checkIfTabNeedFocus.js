import chromep from 'chrome-promise'

let lastMinute
let wait = ms => new Promise(resolve => setTimeout(resolve, ms))

export default async function(minute) {
    if (minute == lastMinute) return
    lastMinute = minute
    let storage = await chromep.storage.local.get(null)
    Object.keys(storage).filter(key => key.indexOf('checker') >= 0).map(async (key) => {
        let checker = storage[key]
        if (checker.isworking && checker.waitingForAnswer && checker.tabId) {
            chrome.tabs.update(checker.tabId, {active: true})
            await wait(2000)
        }
    })
}
