@extends('layouts.app')

@section('css')
    <style>
        tbody>tr {
            cursor: pointer;
        }
        a.url {
            color: #212529cf !important;
        }
    </style>
@endsection

@section('content')
    <section class="text-center">
        @verbatim
        <div class="container mt-5">
            <div class="row">
                <div class="col">
                    <h3 class="mb-5">Чекеры</h3>
                    <div class="row">
                        <div class="col col-lg-8 offset-lg-2">
                            <form class="form-inline justify-content-center" @submit.prevent="addChecker">
                                <input type="text" class="form-control mr-2" v-model="name" :class="{'is-invalid': errors.name}"
                                    required placeholder="Имя чекера" autocomplete="off">
                                <input type="text" class="form-control mr-2" v-model="password" required
                                    placeholder="Пароль" autocomplete="off" :class="{'is-invalid': errors.password}" >
                                <button type="submit" class="btn btn-primary mr-2" :disabled="disabled.add" >Добавить</button>
                                <button type="button" class="btn btn-secondary" :disabled="disabled.add" @click="passgen('password')" >PassGen</button>
                                <small id="emailHelp" class="form-text text-muted">Сохраните пароль, посмотреть позже его будет нельзя</small>
                            </form>
                        </div>
                    </div>
                    <div class="table-responsive mt-5" v-cloak>
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="30%">Имя</th>
                                    <th>Активные задачи</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(checker,i) in sortedCheckers" :key="checker.id" @click="showChangeWindow(checker.id)" >
                                    <td>{{ checker.name }}</td>
                                    <td class="text-left">
                                        <template v-for="(task,n) in chekerUrls[i]">
                                            <a :href="task.url" target="_blank" @click.stop :key="task.id" class="url">
                                                <strong>{{ task.url.substr(0,8) }}</strong><span>{{ task.url.substr(8) }}</span>
                                            </a>
                                            <span v-if="n < (chekerUrls[i].length - 1)">, </span>
                                        </template>

                                    </td>
                                    <td class="text-right">
                                        <button class="btn btn-outline-danger btn-sm mr-2" type="button" @click.stop="confirmDeleteChecker(checker.id)">
                                            удалить
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div role="dialog" tabindex="-1" class="modal" ref="confirmation">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">{{ 'Удалить чекер ' + deleteCheckerName + '?' }}</h4><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button></div>
                    <div class="modal-body">
                        <div class="alert alert-dismissible alert-warning text-left">
                            <h5 class="alert-heading mb-0">Внимание!</h5>
                            <p class="mb-0">Вместе с чекером будут удалены все его логи!</p>
                        </div>
                        <div class="row">
                            <div class="col text-right"><button class="btn btn-danger" type="button" @click="deleteChecker">Удалить</button></div>
                            <div class="col text-left"><button class="btn btn-secondary" type="button" data-dismiss="modal">Отмена</button></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div role="dialog" tabindex="-1" class="modal" ref="changeData">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">{{ 'Чекер ' + changePasswordName }}</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form class="form-inline justify-content-center" @submit.prevent="changePassword">
                                <label class="mr-2">Изменить пароль</label>
                                <input type="text" class="form-control mr-2" v-model="newpassword" required :disabled="disabled.changepassword"
                                    placeholder="Новый пароль" autocomplete="off" :class="{'is-invalid': errors.password}" >
                                <button type="button" class="btn btn-secondary" :disabled="disabled.add" @click="passgen('newpassword')" :disabled="disabled.changepassword">
                                    PassGen
                                </button>
                                <p class="text-center text-secondary mb-0">
                                    <small>Чекер будет автоматически разлогинен и все его работы будут остановлены</small>
                                </p>
                                <div class="row mt-5">
                                    <div class="col text-right">
                                        <button class="btn btn-primary" type="submit" :disabled="disabled.changepassword">
                                            Изменить
                                        </button>
                                    </div>
                                    <div class="col text-left"><button class="btn btn-secondary" type="button" data-dismiss="modal">Отмена</button></div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endverbatim
    </section>
@endsection

@section('scripts')
    <script>
        window.checkers = @json($checkers);
    </script>
    <script src="/js/chance.min.js"></script>
    <script src="/js/checkers.js"></script>
@endsection

