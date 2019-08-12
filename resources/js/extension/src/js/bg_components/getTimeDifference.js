import axios from 'axios'
import chromep from 'chrome-promise'
import getCurrentTime from './getCurrentTime'

let timeDifference = 0
let timer

async function getTimeDifference(callback) {
    let url = await chromep.storage.local.get('url')
    if (!url.url) {
        fallBack(callback)
        return
    }
    axios.get(url.url + 'get-current-time')
    .then(r => {
        timeDifference = Math.round(new Date().getTime()/1000) - r.data
        if (callback) callback()
        refreshTimeDifference(new Date().getTime())
    })
    .catch(e => {
        fallBack(callback)
    })
}

function fallBack(callback) {
    axios.get('http://worldtimeapi.org/api/timezone/Europe/Moscow')
    .then(r => {
        timeDifference = Math.round(new Date().getTime()/1000) - r.data.unixtime
        if (callback) callback()
        refreshTimeDifference(new Date().getTime())
    })
    .catch(e => {
        clearTimeout(timer)
        timer = setTimeout(() => {
            getTimeDifference(callback)
        }, 5000);
    })
}

function refreshTimeDifference(lastRefreshTimeDifference) {
    setTimeout(() => {
        let now = new Date().getTime()
        if ((now - lastRefreshTimeDifference) > 3600000) {
            getTimeDifference(null)
        } else {
            refreshTimeDifference(lastRefreshTimeDifference)
        }
    }, 60000);
}

export {timeDifference, getTimeDifference}
