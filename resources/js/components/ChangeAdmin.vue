<template>
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title">{{ 'Админ ' + admin.name }}</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
        </div>
        <div class="modal-body">
            <h5>Доступ к чекерам</h5>
            <div class="row">
                <div class="col">
                    <div class="card border-primary mb-3 h-100">
                        <div class="card-header text-left">Чекеры</div>
                        <div class="card-body text-left">
                            <button class="btn btn-outline-info btn-sm mr-2 mb-2" type="button" :disabled="checker.disabled"
                                v-for="checker in list" :key="checker.id" @click="addChecker(checker.id, checker.name)">
                                {{ checker.name }}
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card border-primary mb-3 h-100">
                        <div class="card-header text-left">Разрешен доступ</div>
                        <div class="card-body text-left">
                            <button class="btn btn-outline-info btn-sm mr-2 mb-2" type="button"
                                v-for="checker in sortedCheckers" :key="checker.user.id" @click="removeChecker(checker.user.id, checker.user.name)">
                                {{ checker.user.name }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <form class="form-inline justify-content-center mt-5" @submit.prevent="changePassword">
                <label class="mr-2">Изменить пароль</label>
                <input type="text" class="form-control mr-2" v-model="newpassword" placeholder="Новый пароль" autocomplete="off" :disabled="disabled.newpassword" required>
                <button type="submit" class="btn btn-primary mr-2" :disabled="disabled.newpassword" >Изменить</button>
                <button type="button" class="btn btn-secondary" @click="passgen()" :disabled="disabled.newpassword" >PassGen</button>
                <p class="text-center text-secondary">
                    <small>Админ будет автоматически разлогинен здесь и в телеграме</small>
                </p>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-light" type="button" data-dismiss="modal">Закрыть</button>
        </div>
    </div>
</template>

<script>
    import Http from '../Http';

    export default {
        props: ['choosedadmin', 'checkers'],
        data() {
            return {
                admin: JSON.parse(JSON.stringify(this.choosedadmin)),
                newpassword: '',
                symbols: 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789',
                disabled: {
                    newpassword: false
                }
            }
        },
        computed: {
            hasCheckers() {
                if (!this.admin.checkers) return []
                return this.admin.checkers.map(checker => checker.user.id)
            },
            list() {
                return this.checkers.filter(checker => !this.hasCheckers.includes(checker.id))
            },
            sortedCheckers() {
                if (!this.admin.checkers) return []
                return this.admin.checkers.sort((checker1,checker2) => -(checker1.user.name < checker2.user.name) || +(checker1.user.name > checker2.user.name))
            }
        },
        created() {
            console.log(this.admin)
        },
        methods: {
            addChecker(id, name) {
                Vue.set(this.checkers.filter(checker => checker.id == id)[0], 'disabled', true)
                Http.post(`/admins/addchecker`, {checker_id: id, admin_id: this.admin.id})
                .then(response => {
                    if (this.admin.checkers) {
                        this.admin.checkers.push({user: {id: id, name: name}})
                    } else {
                        Vue.set(this.admin, 'checkers', [{user: {id: id, name: name}}])
                    }
                    toastr.success(`Чекер ${name} добавлен`)
                    this.$emit('refreshdata', response.data)
                })
                .catch(error => {
                    if (error.response && error.response.data.errors) {
                        toastr.warning(Object.entries(error.response.data.errors)[0][1][0])
                    }
                    toastr.warning('Что-то пошло не так, попробуйте позднее')
                })
                .finally(() => {
                    this.checkers.filter(checker => checker.id == id)[0].disabled = false
                })
            },
            removeChecker(id, name) {
                Vue.set(this.checkers.filter(checker => checker.id == id)[0], 'disabled', true)
                Http.post(`/admins/removechecker`, {checker_id: id, admin_id: this.admin.id})
                .then(response => {
                    this.admin.checkers.some((checker, i) => {
                        if (checker.user.id == id) {
                            this.admin.checkers.splice(i, 1)
                            return true
                        }
                    })
                    toastr.success(`Чекер ${name} удален`)
                    this.$emit('refreshdata', response.data)
                })
                .catch(error => {
                    if (error.response && error.response.data.errors) {
                        toastr.warning(Object.entries(error.response.data.errors)[0][1][0])
                    } else {
                        toastr.warning('Что-то пошло не так, попробуйте позднее')
                    }
                })
                .finally(() => {
                    this.checkers.filter(checker => checker.id == id)[0].disabled = false
                })
            },
            passgen() {
                this.newpassword = chance.string({ length: 15, pool: this.symbols })
            },
            changePassword() {
                this.disabled.newpassword = true
                let password = this.newpassword
                Http.post(`/users/changepassword`, {id: this.admin.id, password})
                .then(response => {
                    toastr.success(`Пароль чекера ${name} изменен`)
                })
                .catch(error => {
                    if (error.response && error.response.data.errors) {
                        toastr.warning(Object.entries(error.response.data.errors)[0][1][0])
                    } else {
                        toastr.warning('Что-то пошло не так, попробуйте позднее')
                    }
                })
                .finally(() => {
                    this.disabled.newpassword = false
                });
            },
        },
    }
</script>
