import chromep from 'chrome-promise'

export default async function deleteChecker(checker) {
    let key = 'checker' + checker.id
    await chromep.storage.local.remove(key)
    let allTabs = await chromep.tabs.query({pinned: true})
    if (allTabs.length) {
        allTabs = allTabs.map(tab => tab.id)
    }
    let isTab = checker.tabId ? allTabs.includes(checker.tabId) : false
    if (isTab) {
        chromep.tabs.remove(checker.tabId)
    }
}
