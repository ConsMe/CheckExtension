import getCurrentTime from './getCurrentTime'
import chromep from 'chrome-promise'
import {timeDifference} from './getTimeDifference'
import checkSite from './checkSite'

// let lastMinute = new Date((getCurrentTime() - timeDifference) * 1000).getMinutes()

// export default function startAlarm() {
//     let interval = getInterval()
//     setTimeout(async () => {
//         let correctedTime = new Date((getCurrentTime() - timeDifference) * 1000).setSeconds(0,0)
//         let currentMinutes = new Date(correctedTime).getMinutes()
//         if (currentMinutes != lastMinute) {
//             let storage = await chromep.storage.local.get(null)
//             Object.keys(storage).filter(key => key.indexOf('checker') >= 0).forEach(async (k) => {
//                 let checker = storage[k]
//                 let minuteDifference = Math.round((correctedTime - new Date(checker.started_at + ' GMT+03').getTime()) / 60000)
//                 if (minuteDifference < 0) return
//                 if (checker.interval > 1 && minuteDifference % checker.interval > 0) return
//                 if (checker.lastCheck == correctedTime) return
//                 checker.lastCheck = correctedTime
//                 let key = 'checker' + checker.id
//                 let ins = {}
//                 ins[key] = checker
//                 await chromep.storage.local.set(ins)
//                 checkSite(checker)
//             })
//             lastMinute = currentMinutes
//         }
//         startAlarm()
//     }, interval);
// }

// function getInterval() {
//     let seconds = new Date((getCurrentTime() - timeDifference) * 1000).getSeconds()
//     if (seconds > 55) {
//         return 1000
//     }
//     if (seconds > 45) {
//         return 5000
//     }
//     return 10000
// }


export default function startAlarm() {
    let when = new Date((getCurrentTime() - timeDifference) * 1000 + 60000).setSeconds(1,0)
    console.log(new Date(when), getCurrentTime(), timeDifference)
    chrome.alarms.create('main', {when: when})
}


chrome.alarms.onAlarm.addListener(async (alarm) => {
    console.log(alarm.name, new Date())
    let correctedTime = new Date((getCurrentTime() - timeDifference) * 1000).setSeconds(0,0)
    let storage = await chromep.storage.local.get(null)
    Object.keys(storage).filter(key => key.indexOf('checker') >= 0).forEach(async (k) => {
        let checker = storage[k]
        let minuteDifference = Math.round((correctedTime - new Date(checker.started_at + ' GMT+03').getTime()) / 60000)
        if (minuteDifference < 0) return
        if (checker.interval > 1 && minuteDifference % checker.interval > 0) return
        if (checker.lastCheck == correctedTime) return
        checker.lastCheck = correctedTime
        let key = 'checker' + checker.id
        let ins = {}
        ins[key] = checker
        await chromep.storage.local.set(ins)
        checkSite(checker)
    })
    startAlarm()
})
