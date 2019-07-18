import Http from './Http';

new Vue({
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
            stop: false
        },
        errors: {
            name: false,
            password: false
        },
        checkers: window.checkers,
        deleteCheckerId: null,
        stopCheckerId: null,
        changePasswordId: null
    },
    mounted() {
        $(this.$refs.confirmation).on('hidden.bs.modal', (e) => {
            this.deleteCheckerId = null
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
            $(this.$refs.confirmation).modal('hide')
            let name = this.checkers.filter(checker => checker.id == id)[0].name
            Http.post(`/checkers/delete`, {id: id})
            .then(response => {
                toastr.success(`Чекер ${name} удален`)
                this.checkers = response.data
            })
            .catch(error => {
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
        showChangeWindow(id) {
            this.changePasswordId = id
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
                $(this.$refs.changeData).modal('hide')
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
