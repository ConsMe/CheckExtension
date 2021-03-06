@extends('layouts.app')

@section('css')
    <style>
        li {
            word-break: break-all;
        }
    </style>
@endsection

@section('content')
    <section class="text-center">
        <div class="container mt-5">
            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                    <h3 class="mb-5">Чекер {{ $checkerName }}</h3>
                    <div v-cloak>
                        <div class="alert alert-dismissible alert-warning mt-5 text-left" v-if="notSettedUp || warningVersion">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <h4 class="alert-heading mb-0">Внимание!</h4>
                            <p v-if="!warningVersion">Расширение для браузера не обнаружено, ваш чекер работать не будет</p>
                            <p v-else>Расширение для браузера устарело, загрузите новое</p>
                            <a href="/download/extension" class="btn btn-sm btn-primary" style="text-decoration: none;" role="button">Скачать</a>
                        </div>
                        <div class="mt-5">
                            <ul class="list-group text-left">
                                <li class="list-group-item bg-transparent" v-for="checker in checkers" :key="checker.id" >
                                    <p class="mb-0">
                                        <strong class="mr-2">URL:</strong>
                                        <a :href="checker.url" target="_blank">
                                            @{{ checker.url }}
                                        </a>
                                    </p>
                                    <p class="mb-0">
                                        <strong class="mr-2">Что ищем:</strong>
                                        @{{ checker.search }}
                                    </p>
                                    <p class="mb-0">
                                        <strong class="mr-2">Интервал:</strong>
                                        @{{ checker.interval }}
                                    </p>
                                    <p>
                                      <strong class="mr-2">Задержка:</strong>
                                      @{{ checker.delay }}
                                    </p>
                                    <div class="row">
                                        <div class="col text-left">
                                            <button type="button" class="btn" @click="stopstart(checker)" :class="[checker.isworking ? 'btn-secondary' : 'btn-primary']"
                                                :disabled="checker.disabled || notSettedUp" >
                                                @{{ checker.isworking ? 'Остановить' : 'Запустить' }}
                                            </button>
                                        </div>
                                        <div class="col text-right">
                                            <button type="button" class="btn btn-warning" @click="confirmDelete(checker)" :disabled="checker.disabled || notSettedUp" >
                                                Удалить
                                            </button>
                                        </div>
                                    </div>

                                </li>
                                <li class="list-group-item bg-transparent">
                                    <form class="mt-5" @submit.prevent="add" >
                                        <div class="form-group row"><label class="col-sm-2 col-form-label">URL</label>
                                            <div class="col-sm-10">
                                                <input type="text" placeholder="https://..." class="form-control" v-model="url" required :disabled="notSettedUp" />
                                            </div>
                                        </div>
                                        <div class="form-group row"><label class="col-sm-2 col-form-label">Что ищем</label>
                                            <div class="col-sm-10">
                                                <input type="text" placeholder="&lt;a href=&quot;&quot;&gt;&lt;/a&gt;" class="form-control" v-model="search" required :disabled="notSettedUp" />
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Интервал</label>
                                            <div class="col-auto">
                                                <input type="number" value="5" class="form-control text-center" style="width: 5rem;" v-model="interval" required :disabled="notSettedUp" />
                                            </div>
                                            <label class="col-sm-2 col-form-label pl-0">мин.</label>
                                            <label class="col-sm-2 col-form-label">Задержка</label>
                                            <div class="col-auto">
                                              <input type="number" class="form-control text-center" style="width: 5rem;" v-model="delay"
                                                required />
                                            </div>
                                            <label class="col-sm-2 col-form-label pl-0">мин.</label>
                                        </div>
                                        <div class="form-group text-left">
                                            <button class="btn btn-primary" type="submit" :disabled="disabled.add || notSettedUp" >Добавить</button>
                                        </div>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div role="dialog" tabindex="-1" class="modal" ref="confirmation">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">@{{ 'Удалить чекер?' }}</h4><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button></div>
                    <div class="modal-body">
                        <div class="form-group">
                          <div class="custom-control custom-checkbox mb-5 mt-3">
                            <input type="checkbox" class="custom-control-input" id="customCheck1" v-model="withLogs">
                            <label class="custom-control-label" for="customCheck1" style="cursor: pointer;">
                              Удалить все логи по этой задаче
                            </label>
                          </div>
                        </div>
                        <div class="row">
                            <div class="col text-right">
                                <button class="btn btn-danger" type="button" @click="deleteChecker" :disabled="disabled.delete">Удалить</button>
                            </div>
                            <div class="col text-left">
                                <button class="btn btn-secondary" type="button" data-dismiss="modal" :disabled="disabled.delete">Отмена</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div role="dialog" tabindex="-1" class="modal" ref="exitConfirm">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Выйти из аккаунта?</h4><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button></div>
                        <div class="modal-body">
                            <div class="alert alert-dismissible alert-warning text-left">
                                <h5 class="alert-heading mb-0">Внимание!</h5>
                                <p class="mb-0">Все ваши чекеры будут остановлены</p>
                            </div>
                            <div class="row">
                                <div class="col text-right">
                                    <button class="btn btn-danger" type="button" @click="exit" :disabled="disabled.exit" >Выйти</button>
                                </div>
                                <div class="col text-left">
                                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Отмена</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </section>
@endsection

@section('scripts')
    <script>
        window.checkers = @json($checkers);
        window.extensionVersion = "{{ $extensionVersion }}";
        window.id = {{ $id }};
    </script>
    <script src="/js/checkerlk.js"></script>
@endsection
