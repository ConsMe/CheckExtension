import Http from './Http';
import Echo from "laravel-echo"
window.io = require('socket.io-client');

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
        checkerId: window.checkerId,
        online: false,
        delay: 1,
        withLogs: true,
    },
    created() {
        const port = window.location.hostname.indexOf('test') > 0 ? '6001' : '8443'
        const echo = new Echo({
            broadcaster: 'socket.io',
            host: window.location.hostname + ':' + port,
        });
        echo.join('followTheCheckers')
            .here((users) => {
                const checker = users.find(u => u.id === this.checkerId);
                if (checker) this.online = true;
            })
            .joining((user) => {
                if (user.id === this.checkerId) this.online = true;
            })
            .leaving((user) => {
                if (user.id === this.checkerId) this.online = false;
            });
    },
    mounted() {
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
            Http.post('/checker/add', { url, search, interval, delay, checker_id: this.checkerId })
            .then(r => {
                toastr.success('Чекер добавлен')
                this.checkers = r.data
                this.url = ''
                this.search = ''
                this.interval = 5
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
            let {id, isworking} = checker
            isworking = !isworking
            checker.disabled = true
            Http.post('/checker/stopstart', {id, isworking})
            .then(r => {
                let status = isworking ? 'запущен' : 'остановлен'
                toastr.success(`Чекер ${status}`)
                this.checkers = r.data
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
            })
            .catch(error => {
                toastr.warning('Что-то пошло не так, попробуйте позднее')
            })
            .finally(() => {
                this.disabled.delete = false
            });
        },
        exit() {
            this.disabled.exit = true
            $('#logout-form').submit()
        }
    }
})
