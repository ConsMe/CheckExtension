const moment = require('moment')
Vue.component('pagination', require('laravel-vue-pagination'));
import Inputmask from "inputmask";

new Vue({
    el: '#app',
    data: {
        data: window.data,
        moment: moment,
        checker: '',
        dateFrom: '',
        dateTo: '',
        disabled: false
    },
    computed: {
        logs() {
            return this.data.data
        },
        checkers() {
            return this.data.checkers
        }
    },
    mounted() {
        Inputmask({regex: "[0-3]\\d\\.[01]\\d\\.[10][90]", placeholder: "дд.мм.гг"}).mask($([this.$refs.dateFrom,this.$refs.dateTo]))
    },
    methods: {
        getData(page = 1) {
            this.disabled = true
            let params = {page: page}
            if (this.dateFrom.length) {
                params.dateFrom = this.dateFrom
            }
            if (this.dateTo.length) {
                params.dateTo = this.dateTo
            }
            if (this.checker.length) {
                params.checker = this.checker
            }
            axios.get('statistics/get', {params: params})
            .then(r => {
                this.data = r.data
            })
            .catch(e => {
                if (e.response.data.errors) {
                    toastr.warning(Object.entries(e.response.data.errors)[0][1][0])
                    return
                }
                toastr.warning('Что-то пошло не так, попробуйте позднее')
            })
            .finally(() => {
                this.disabled = false
            })
        }
    }
})
