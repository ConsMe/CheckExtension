import chromep from 'chrome-promise'

export default async function createChecker(checker) {
    let key = 'checker' + checker.id
    let ins = {}
    ins[key] = checker
    await chromep.storage.local.set(ins)
}
