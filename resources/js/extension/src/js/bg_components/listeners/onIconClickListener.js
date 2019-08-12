import chromep from 'chrome-promise'

/* Open checker LK on icon click */
chrome.browserAction.onClicked.addListener(async function(tab) {
    let url = await chromep.storage.local.get('url')
    chrome.tabs.create({url: `${url}`})
});
