Vue.component('change-admin', require('./components/ChangeAdmin.vue').default);
import Http from './Http';

new Vue({
    el: '#app',
    data: {
        symbols: 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789',
        name: '',
        password: '',
        disabled: {
            add: false,
            delete: false
        },
        errors: {
            name: false,
            password: false
        },
        admins: window.admins,
        deleteAdminId: null,
        choosedAdmin: null,
        checkers: window.checkers
    },
    computed: {
        sortedAdmins() {
            return this.admins.sort((admin1,admin2) => -(admin1.name < admin2.name) || +(admin1.name > admin2.name))
        }
    },
    mounted() {
        $(this.$refs.confirmation).on('hidden.bs.modal', (e) => {
            this.deleteAdminId = null
        })
        $(this.$refs.changeadminwindow).on('hidden.bs.modal', (e) => {
            this.choosedAdmin = null
        })
    },
    methods: {
        passgen() {
            this.password = chance.string({ length: 15, pool: this.symbols })
        },
        addadmin() {
            this.disabled.add = true
            let name = this.name
            Http.post(`/register-admin/admin`, {name: name, password: this.password})
            .then(response => {
                this.name = ''
                this.password = ''
                toastr.success(`Админ ${name} добавлен`)
                this.refreshData(response.data)
            })
            .catch(error => {
                if (error.response.data && error.response.data.errors) {
                    toastr.warning(Object.entries(error.response.data.errors)[0][1][0])
                    return
                }
                toastr.warning('Что-то пошло не так, попробуйте позднее')
            })
            .finally(() => {
                this.disabled.add = false
            });
        },
        confirmDeleteAdmin(id) {
            this.deleteAdminId = id
            $(this.$refs.confirmation).modal('show')
        },
        deleteAdmin() {
            if (!this.deleteAdminId) return
            this.disabled.delete = true
            let id = this.deleteAdminId
            $(this.$refs.confirmation).modal('hide')
            let name = this.admins.filter(admin => admin.id == id)[0].name
            Http.post(`/admins/delete`, {id: id})
            .then(response => {
                toastr.success(`Админ ${name} удален`)
                this.refreshData(response.data)
            })
            .catch(error => {
                toastr.warning('Что-то пошло не так, попробуйте позднее')
            })
            .finally(() => {
                this.disabled.delete = false
            });
        },
        changeAdminWindow(id) {
            this.choosedAdmin = this.admins.filter(admin => admin.id == id)[0]
            $(this.$refs.changeadminwindow).modal('show')
        },
        refreshData(data) {
            this.admins = data.admin ? data.admin : []
            this.checkers = data.checker ? data.checker : []
        }
    }
})
