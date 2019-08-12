export default function() {
    let urls = chrome.runtime.getManifest().externally_connectable.matches.map(url => {
        let host = url.match(/\w+\.\w+/)
        if (host) return host[0]
        return ''
    })
    .filter(url => url.length && url.indexOf('.test') < 0)
    chrome.tabs.query({}, tabs => {
        let isLkOpened = tabs.some(tab => {
            let host = new URL(tab.url).host
            if (urls.includes(host)) {
                chrome.tabs.reload(tab.id)
                return true
            }
        })
        if (!isLkOpened) {
            urls.forEach(url => {
                url = 'http://' + url + '/checker/lk'
                chrome.tabs.create({url: url})
            })
        }
    })
}
