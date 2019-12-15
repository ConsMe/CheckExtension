import Http from './Http';
import Echo from "laravel-echo"
window.io = require('socket.io-client');

window.vm = new Vue({
    el: '#app',
    data: {
        symbols: 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789',
        name: '',
        password: '',
        newpassword: '',
        disabled: {
            add: false,
            delete: false,
            changepassword: false,
            stop: false,
            max_undetected_errors: false,
            max_uncompleted_errors: false
        },
        errors: {
            name: false,
            password: false
        },
        checkers: window.checkers,
        deleteCheckerId: null,
        stopCheckerId: null,
        changePasswordId: null,
        max_undetected_errors: 1,
        max_uncompleted_errors: 1,
        onlineId: [],
        canAddEditCheckers: window.canAddEditCheckers,
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
                this.onlineId = users.filter(u => u.id).map(u => u.id);
            })
            .joining((user) => {
                if (!user.id || this.onlineId.includes(user.id)) return;
                this.onlineId.push(user.id);
            })
            .leaving((user) => {
                if (!user.id) return;
                const index = this.onlineId.indexOf(user.id);
                if (index >= 0) {
                    this.onlineId.splice(index, 1);
                }
            });
    },
    mounted() {
        $(this.$refs.confirmation).on('hidden.bs.modal', (e) => {
            this.deleteCheckerId = null
            this.withLogs = true
        })
        $(this.$refs.stopConfirmation).on('hidden.bs.modal', (e) => {
            this.stopCheckerId = null
        })
        $(this.$refs.changeData).on('hidden.bs.modal', (e) => {
            this.changePasswordId = null
            this.newpassword = ''
        })
    },
    computed: {
        sortedCheckers() {
            if (this.checkers.length <= 1) return this.checkers
            return this.checkers.sort((checker1,checker2) => -(checker1.name < checker2.name) || +(checker1.name > checker2.name))
        },
        deleteCheckerName() {
            if (this.deleteCheckerId) {
                return this.checkers.filter(checker => checker.id == this.deleteCheckerId)[0].name
            }
            return ''
        },
        stopCheckerName() {
            if (this.stopCheckerId) {
                return this.checkers.filter(checker => checker.id == this.stopCheckerId)[0].name
            }
            return ''
        },
        changePasswordName() {
            if (this.changePasswordId) {
                return this.checkers.filter(checker => checker.id == this.changePasswordId)[0].name
            }
            return ''
        },
        chekerUrls() {
            return this.sortedCheckers.map(checker => {
                if ('checkertasks' in checker) {
                    return checker.checkertasks.filter(task => task.isworking)
                }
                return ''
            })
        }
    },
    methods: {
        changeErrorsCount(parameter) {
            this.disabled[parameter] = true
            let id = this.changePasswordId
            Http.post(`/checkers/change-errors-count`, {parameter: parameter, value: this[parameter], id})
            .then(response => {
                toastr.success(`Параметр изменен`)
                this.checkers = response.data
            })
            .catch(error => {
                if (error.response.data.errors) {
                    toastr.warning(Object.entries(error.response.data.errors)[0][1][0])
                    return
                }
                toastr.warning('Что-то пошло не так, попробуйте позднее')
            })
            .finally(() => {
                this.disabled[parameter] = false
            });
        },
        passgen(password) {
            this[password] = chance.string({ length: 15, pool: this.symbols })
        },
        addChecker() {
            this.disabled.add = true
            let name = this.name
            Http.post(`/register/checker`, {name: name, password: this.password})
            .then(response => {
                this.name = ''
                this.password = ''
                toastr.success(`Чекер ${name} добавлен`)
                this.checkers = response.data
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
        confirmDeleteChecker(id) {
            this.deleteCheckerId = id
            $(this.$refs.confirmation).modal('show')
        },
        deleteChecker() {
            if (!this.deleteCheckerId) return
            this.disabled.delete = true
            let id = this.deleteCheckerId
            let withLogs = this.withLogs
            $(this.$refs.confirmation).modal('hide')
            let name = this.checkers.filter(checker => checker.id == id)[0].name
            Http.post(`/checkers/delete`, {id, withLogs})
            .then(response => {
                toastr.success(`Чекер ${name} удален`)
                this.checkers = response.data
            })
            .catch(error => {
                if (error.response.data && error.response.data.errors) {
                    toastr.warning(Object.entries(error.response.data.errors)[0][1][0])
                    return
                }
                toastr.warning('Что-то пошло не так, попробуйте позднее')
            })
            .finally(() => {
                this.disabled.delete = false
            });
        },
        confirmStopChecker(id) {
            this.stopCheckerId = id
            $(this.$refs.stopConfirmation).modal('show')
        },
        stopChecker() {
            if (!this.stopCheckerId) return
            this.disabled.stop = true
            let id = this.stopCheckerId
            $(this.$refs.stopConfirmation).modal('hide')
            let name = this.checkers.filter(checker => checker.id == id)[0].name
            Http.post(`/checkers/stop`, {id: id})
            .then(response => {
                toastr.success(`Чекер ${name} остановлен`)
                this.checkers = response.data
            })
            .catch(error => {
                toastr.warning('Что-то пошло не так, попробуйте позднее')
            })
            .finally(() => {
                this.disabled.stop = false
            });
        },
        showChangeWindow(checker) {
            this.max_uncompleted_errors = checker.max_uncompleted_errors
            this.max_undetected_errors = checker.max_undetected_errors
            this.changePasswordId = checker.id
            $(this.$refs.changeData).modal('show')
        },
        changePassword() {
            if (!this.changePasswordId) return
            let id = this.changePasswordId
            let password = this.newpassword
            let name = this.checkers.filter(checker => checker.id == id)[0].name
            this.disabled.changepassword = true
            Http.post(`/users/changepassword`, {id, password})
            .then(response => {
                toastr.success(`Пароль чекера ${name} изменен`)
                // $(this.$refs.changeData).modal('hide')
            })
            .catch(error => {
                if (error.response.data.errors) {
                    toastr.warning(Object.entries(error.response.data.errors)[0][1][0])
                    return
                }
                toastr.warning('Что-то пошло не так, попробуйте позднее')
            })
            .finally(() => {
                this.disabled.changepassword = false
            });;
        },
    }
})
