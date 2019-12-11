@extends('layouts.app')

@section('css')
    <style>
        tbody>tr {
            cursor: pointer;
        }
    </style>
@endsection

@section('content')
    <section class="text-center">
        @verbatim
        <div class="container mt-5">
            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                    <h3 class="mb-5">Админы</h3>
                    <form class="form-inline justify-content-center" @submit.prevent="addadmin">
                        <input type="text" class="form-control mr-2" v-model="name" :class="{'is-invalid': errors.name}"
                            required placeholder="Имя пользователя" autocomplete="off">
                        <input type="text" class="form-control mr-2" v-model="password" required
                            placeholder="Пароль" autocomplete="off" :class="{'is-invalid': errors.password}" >
                        <button type="submit" class="btn btn-primary mr-2" :disabled="disabled.add" >Добавить</button>
                        <button type="button" class="btn btn-secondary" :disabled="disabled.add" @click="passgen" >PassGen</button>
                        <small id="emailHelp" class="form-text text-muted">Сохраните пароль, посмотреть позже его будет нельзя</small>
                    </form>
                    <div class="table-responsive mt-5" v-cloak>
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="30%">Имя</th>
                                    <th>Чекеры</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="admin in sortedAdmins" :key="admin.id" @click="changeAdminWindow(admin.id)">
                                    <td>{{ admin.name }}</td>
                                    <td>
                                        <span v-for="(checker,i) in admin.checkers" :key="checker.id" >
                                            <span>{{ checker.user.name }}</span>
                                            <span v-if="i < (admin.checkers.length - 1)">, </span>
                                        </span>
                                    </td>
                                    <td class="text-right">
                                        <button class="btn btn-outline-danger btn-sm mr-2" type="button" @click.stop="confirmDeleteAdmin(admin.id)">
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
                        <h4 class="modal-title">Удалить админа admin1?</h4><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button></div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col text-right"><button class="btn btn-danger" type="button" @click="deleteAdmin">Удалить</button></div>
                            <div class="col text-left"><button class="btn btn-secondary" type="button" data-dismiss="modal">Отмена</button></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div role="dialog" tabindex="-1" class="modal" ref="changeadminwindow">
            <div class="modal-dialog modal-lg" role="document">
                <change-admin
                    :choosedadmin="choosedAdmin"
                    :checkers="checkers"
                    v-if="choosedAdmin"
                    @refreshdata="refreshData" />
            </div>
        </div>
        @endverbatim
    </section>
@endsection

@section('scripts')
    <script>
        @if (isset($users['admin']))
            window.admins = @json($users['admin']);
        @else
            window.admins = [];
        @endif
        @if (isset($users['checker']))
            window.checkers = @json($users['checker']);
        @else
            window.checkers = [];
        @endif
    </script>
    <script src="/js/chance.min.js"></script>
    <script src="/js/admin.js"></script>
@endsection
