import setCheckers from './setCheckers'
import clearAll from './clearAll'
import chromep from 'chrome-promise'
import axios from 'axios'
const CancelToken = axios.CancelToken
const sourceAxios = CancelToken.source()

export default async function sendResult(result, checker) {
    let code = result > 0 ? 1 : 0
    let url = await chromep.storage.local.get('url')
    axios.post(url.url + 'writelog', {code: code, id: checker.id, datetime: checker.lastCheck}, {cancelToken: sourceAxios.token})
    .then(r => {
        if (r.status != 200) {
            sourceAxios.cancel()
            clearAll()
            return false
        }
        setCheckers(r.data)
    })
    .catch(async e => {
        if (e.response.status == 401) {
            clearAll()
        }
    })
}
