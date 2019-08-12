import deleteChecker from './deleteChecker'
import chromep from 'chrome-promise'

export default async function clearAll() {
    let storage = await chromep.storage.local.get(null)
    Object.keys(storage).filter(key => key.indexOf('checker') >= 0).forEach(key => {
        deleteChecker(storage[key])
    })
}
