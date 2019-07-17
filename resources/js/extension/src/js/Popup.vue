<template>
    <div>
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="/">
                    CheckExtension
                </a>
            </div>
        </nav>
        <div class="container mt-5">
            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                    <preloader v-if="ping"></preloader>
                    <!-- <login-page v-if="!logged"></login-page>
                    <checkers-page v-else></checkers-page> -->
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Preloader from './Preloader.vue'
    const axios = require('axios')

    export default {
        components: {Preloader},
        data() {
            return {
                ping: true,
                logged: false
            }
        },
        created() {
            axios.post(`/checker/ping`)
            .then(response => {
                this.logged = true
            })
            .catch(error => {

            })
            .finally(() => {
                this.ping = false
            });
        }
    }
</script>
