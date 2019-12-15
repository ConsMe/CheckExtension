import deleteChecker from './deleteChecker'
import createChecker from './createChecker'
import chromep from 'chrome-promise'

export  default async function setCheckers(outCheckers) {
    console.log(outCheckers)
    let inCheckers = await chromep.storage.local.get(null)
    inCheckers = Object.keys(inCheckers).filter(key => key.indexOf('checker') >= 0).map(key => inCheckers[key])
    let inIds = inCheckers.map(checker => checker.id)
    let outIds = outCheckers.map(checker => checker.id)
    inIds.forEach((id,i) => {
        if (!outIds.includes(id) || !outCheckers[outIds.indexOf(id)].isworking) {
            deleteChecker(inCheckers[i])
        }
    })
    outIds.forEach((id,i) => {
        if (!inIds.includes(id) && outCheckers[i].isworking) {
            createChecker(outCheckers[i])
        }
    })
}
