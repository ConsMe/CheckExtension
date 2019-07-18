@extends('layouts.app')

@section('css')
    <style>
        input {
            width: 150px !important;
        }
        select {
            color: #212529cf !important;
        }
    </style>
@endsection

@section('content')
    <section class="text-center">
        @verbatim
        <div class="container mt-5">
            <div class="row">
                <div class="col" v-cloak>
                    <h3 class="mb-5">Статистика</h3>
                    <form class="form-inline justify-content-center" @submit.prevent="getData(1)">
                        <label for="" class="mr-2">Период</label>
                        <input type="text" class="form-control mr-2 text-center" v-model="dateFrom" placeholder="дд.мм.гг чч:мм" ref="dateFrom" >
                        <span class="mr-2">-</span>
                        <input type="text" class="form-control mr-2 text-center" v-model="dateTo" placeholder="дд.мм.гг чч:мм" ref="dateTo" >
                        <label for="" class="mr-2">Чекер</label>
                        <select class="custom-select mr-2" v-model="checker" >
                            <option value="">Все</option>
                            <option :value="id" v-for="(name,id) in checkers" :key="id">{{ name }}</option>
                        </select>
                        <button type="submit" class="btn btn-primary btn-sm mr-2" :disabled="disabled" >Установить</button>
                        <button class="btn btn-secondary btn-sm ml-2" type="button" title="Обновить" :disabled="disabled" @click="getData(null)">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </form>
                    <div class="mt-2">
                        <span class="mr-2">Последний</span>
                        <button class="btn btn-sm btn-outline-secondary" @click="setDate(1)">Час</button>
                        <button class="btn btn-sm btn-outline-secondary" @click="setDate(24)">День</button>
                        <button class="btn btn-sm btn-outline-secondary" @click="setDate(24*7)">Неделя</button>
                    </div>
                    <div class="table-responsive mt-5" v-show="logs.length" >
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Checker name</th>
                                    <th>Checked url</th>
                                    <th>Result</th>
                                    <th>Response</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="log in logs" :key="log.id" >
                                    <td>{{ moment(log.created_at).format('DD.MM.YY HH:mm') }}</td>
                                    <td>{{ log.checker.user.name }}</td>
                                    <td>{{ log.checker.url }}</td>
                                    <td>{{ log.status == 1 ? 'OK' : 'ERROR' }}</td>
                                    <td :class="[log.status == 1 ? 'text-success' : 'text-danger']">
                                        {{ log.status == 1 ? 'ELEMENT DETECTED' : log.status === 0 ? 'ELEMENT UNDETECTED' : 'CHECK UNCOMPLETED' }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p v-show="!logs.length" class="mt-5 font-italic text-center">Not found</p>
                    <pagination :data="data" :limit="5" align="center" class="mt-5" @pagination-change-page="getData"></pagination>
                </div>
            </div>
        </div>

        @endverbatim
    </section>
@endsection

@section('scripts')
    <script>
        window.data = @json($data);
    </script>
    <script src="/js/statistics.js"></script>
@endsection

