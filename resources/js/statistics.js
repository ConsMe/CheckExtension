const moment = require('moment-timezone')
Vue.component('pagination', require('laravel-vue-pagination'));
import Inputmask from "inputmask";
import Http from './Http';

new Vue({
    el: '#app',
    data: {
        data: window.data,
        moment: moment,
        checker: '',
        dateFrom: '',
        dateTo: '',
        disabled: false,
        currentPage: 1
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
        Inputmask("datetime", {
            // regex: "[0-3]\\d\\.[01]\\d\\.[10][90]",
            inputFormat: 'dd.mm.yy HH:MM',
            placeholder: "дд.мм.гг чч:мм"
        }).mask($([this.$refs.dateFrom,this.$refs.dateTo]))
    },
    methods: {
        getData(page) {
            if (page) {
                this.currentPage = page
            } else {
                page = this.currentPage
            }
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
            Http.get('statistics/get', {params: params})
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
        },
        setDate(hours) {
            this.dateFrom = this.moment().tz("Europe/Moscow").subtract(hours, 'hours').format('DD.MM.YY HH:mm')
        }
    }
})
