import Http from './Http';

new Vue({
    el: '#app',
    data: {
        url: '',
        search: '',
        interval: 5,
        disabled: {
            add: false,
            delete: false,
            exit: false
        },
        checkers: window.checkers,
        deleteCheckerId: null,
        extensionId: 'ochcllcealknpgfmcbdcileoboabcikp',
        notSettedUp: false,
        extensionVersion: window.extensionVersion,
        warningVersion: false,
        id: window.id,
        delay: 1,
        withLogs: true,
    },
    mounted() {
        this.checkSettedUp()
        $(this.$refs.confirmation).on('hidden.bs.modal', (e) => {
            this.deleteCheckerId = null
            this.withLogs = true
        })
        $(this.$refs.exitConfirm).on('hidden.bs.modal', (e) => {
            this.disabled.exit = false
        })
        $(this.$refs.exitButton).removeAttr('onclick')
        .click(e => {
            e.originalEvent.preventDefault()
            e.originalEvent.stopPropagation()
            $(this.$refs.exitConfirm).modal('show')
        })
    },
    methods: {
        add() {
            let {url, search, interval, delay} = this.$data
            this.disabled.add = true
            Http.post('/checker/add', {url, search, interval, delay})
            .then(r => {
                toastr.success('Чекер добавлен')
                this.checkers = r.data
                this.url = ''
                this.search = ''
                this.interval = 5
                chrome.runtime.sendMessage(this.extensionId, {refreshCheckers: true, checkers: this.checkers})
            })
            .catch(error => {
                if (error.response.data.errors) {
                    toastr.warning(Object.entries(error.response.data.errors)[0][1][0])
                    return
                }
                toastr.warning('Что-то пошло не так, попробуйте позднее')
            })
            .finally(() => {
                this.disabled.add = false
            });
        },
        stopstart(checker) {
            console.log(checker)
            let {id, isworking} = checker
            isworking = !isworking
            checker.disabled = true
            Http.post('/checker/stopstart', {id, isworking})
            .then(r => {
                let status = isworking ? 'запущен' : 'остановлен'
                toastr.success(`Чекер ${status}`)
                this.checkers = r.data
                chrome.runtime.sendMessage(this.extensionId, {refreshCheckers: true, checkers: this.checkers})
            })
            .catch(error => {
                checker.disabled = false
                if (error.response.data.errors) {
                    toastr.warning(Object.entries(error.response.data.errors)[0][1][0])
                    return
                }
                toastr.warning('Что-то пошло не так, попробуйте позднее')
            });
        },
        confirmDelete(checker) {
            this.deleteCheckerId = checker.id
            $(this.$refs.confirmation).modal('show')
        },
        deleteChecker() {
            if (!this.deleteCheckerId) return
            this.disabled.delete = true
            let id = this.deleteCheckerId
            const withLogs = this.withLogs
            $(this.$refs.confirmation).modal('hide')
            Http.post(`/checker/delete`, {id, withLogs})
            .then(response => {
                toastr.success(`Чекер удален`)
                this.checkers = response.data
                chrome.runtime.sendMessage(this.extensionId, {refreshCheckers: true, checkers: this.checkers})
            })
            .catch(error => {
                toastr.warning('Что-то пошло не так, попробуйте позднее')
            })
            .finally(() => {
                this.disabled.delete = false
            });
        },
        checkSettedUp() {
            if (!chrome.runtime) {
                this.notSettedUp = true
                return
            }
            chrome.runtime.sendMessage(this.extensionId, {
                isSettedUp: true,
                checkers: this.checkers,
                url: window.location.origin + '/',
                id: this.id,
            }, response => {
                let lastError = chrome.runtime.lastError
                if (lastError || !response.settedUp) {
                    this.notSettedUp = true
                    return
                }
                if (response.version != this.extensionVersion) {
                    this.warningVersion = true
                    return
                }
            })
        },
        exit() {
            this.disabled.exit = true
            if (!chrome.runtime) {
                $('#logout-form').submit()
                return
            }
            chrome.runtime.sendMessage(this.extensionId, {exit: true, id: this.id}, response => {
                $('#logout-form').submit()
            })
        }
    }
})
