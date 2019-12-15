@extends('layouts.app')

@section('css')
<style>
  tbody>tr {
    cursor: pointer;
  }

  a.url {
    color: #212529cf !important;
  }

  table tr td,
  table tr td *:not(.tr-link) {
    position: relative;
  }

  td>button {
    z-index: 2;
  }

  .tr-link {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    display: inline-block;
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
        <div class="row" v-if="canAddEditCheckers">
          <div class="col col-lg-8 offset-lg-2">
            <form class="form-inline justify-content-center" @submit.prevent="addChecker">
              <input type="text" class="form-control mr-2" v-model="name"
                :class="{'is-invalid': errors.name}" required placeholder="Имя чекера"
                autocomplete="off">
              <input type="text" class="form-control mr-2" v-model="password" required
                placeholder="Пароль" autocomplete="off" :class="{'is-invalid': errors.password}">
              <button type="submit" class="btn btn-primary mr-2"
                :disabled="disabled.add">Добавить</button>
              <button type="button" class="btn btn-secondary" :disabled="disabled.add"
                @click="passgen('password')">PassGen</button>
              <small id="emailHelp" class="form-text text-muted">Сохраните пароль, посмотреть позже
                его будет нельзя</small>
            </form>
          </div>
        </div>
        <div class="table-responsive mt-5" v-cloak>
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Статус</th>
                <th width="30%">Имя</th>
                <th>Активные задачи</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(checker,i) in sortedCheckers" :key="checker.id">
                <td>
                  <a :href="`/checkers/${checker.id}`" class="tr-link"></a>
                  <span class="d-inline-block rounded-circle"
                    :class="[onlineId.includes(checker.id) ? 'bg-success' : 'bg-danger']"
                    style="width: 1rem; height: 1rem; vertical-align: middle;">
                  </span>
                </td>
                <td>
                  <a :href="`/checkers/${checker.id}`" class="tr-link"></a>
                  {{ checker.name }}
                </td>
                <td class="text-left">
                  <a :href="`/checkers/${checker.id}`" class="tr-link"></a>
                  <template v-for="(task,n) in chekerUrls[i]">
                    <a :href="task.url" target="_blank" @click.stop :key="task.id" class="url">
                      <strong>{{ task.url.substr(0,8) }}</strong><span>{{ task.url.substr(8) }}</span>
                    </a>
                    <span v-if="n < (chekerUrls[i].length - 1)">, </span>
                  </template>

                </td>
                <td class="text-right">
                  <a :href="`/checkers/${checker.id}`" class="tr-link"></a>
                  <button class="btn btn-outline-secondary btn-sm mr-2" type="button"
                    title="Настройки" @click.stop="showChangeWindow(checker)">
                    <i class="fas fa-cog"></i>
                  </button>
                  <button class="btn btn-outline-danger btn-sm mr-2" type="button"
                    @click.stop="confirmDeleteChecker(checker.id)" title="Удалить чекер"
                    v-if="!chekerUrls[i].length && canAddEditCheckers">
                    <i class="fas fa-trash-alt"></i>
                  </button>
                  <button class="btn btn-outline-warning btn-sm mr-2" type="button"
                    @click.stop="confirmStopChecker(checker.id)" title="Остановить все задачи"
                    v-if="chekerUrls[i].length">
                    <i class="fas fa-stop"></i>
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
          <h4 class="modal-title">{{ 'Удалить чекер ' + deleteCheckerName + '?' }}</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
              aria-hidden="true">×</span></button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <div class="custom-control custom-checkbox mb-5 mt-3">
              <input type="checkbox" class="custom-control-input" id="customCheck1" v-model="withLogs">
              <label class="custom-control-label" for="customCheck1" style="cursor: pointer;">
                Удалить все его логи
              </label>
            </div>
          </div>
          <div class="row">
            <div class="col text-right"><button class="btn btn-danger" type="button"
                @click="deleteChecker">Удалить</button></div>
            <div class="col text-left"><button class="btn btn-secondary" type="button"
                data-dismiss="modal">Отмена</button></div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div role="dialog" tabindex="-1" class="modal" ref="stopConfirmation">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">{{ 'Остановить чекер ' + stopCheckerName + '?' }}</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
              aria-hidden="true">×</span></button>
        </div>
        <div class="modal-body">
          <p>Все работы чекера будут остановлены</p>
          <div class="row">
            <div class="col text-right"><button class="btn btn-danger" type="button"
                @click="stopChecker">Остановить</button></div>
            <div class="col text-left"><button class="btn btn-secondary" type="button"
                data-dismiss="modal">Отмена</button></div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div role="dialog" tabindex="-1" class="modal" ref="changeData">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">{{ 'Чекер ' + changePasswordName }}</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">
          <h5 class="text-center mb-3">Количество ошибок подряд</h5>
          <div class="row justify-content-center">
            <div class="col col-12 col-md-auto mb-2">
              <form class="form-inline justify-content-center">
                <label class="mb-0 mr-2">UNDETECTED</label>
                <select v-model="max_undetected_errors" class="form-control w-auto custom-select"
                  @change="changeErrorsCount('max_undetected_errors')"
                  :disabled="disabled.max_undetected_errors">
                  <option :value="n" v-for="n in 10">{{ n }}</option>
                </select>
              </form>
            </div>
            <div class="col col-12 col-md-auto mb-2">
              <form class="form-inline justify-content-center">
                <label class="mb-0 mr-2">UNCOMPLETED</label>
                <select v-model="max_uncompleted_errors" class="form-control w-auto custom-select"
                  @change="changeErrorsCount('max_uncompleted_errors')"
                  :disabled="disabled.max_uncompleted_errors">
                  <option :value="n" v-for="n in 10">{{ n }}</option>
                </select>
              </form>
            </div>
          </div>
          <form class="form-inline justify-content-center mt-4" @submit.prevent="changePassword" v-if="canAddEditCheckers">
            <label class="mr-2">Изменить пароль</label>
            <input type="text" class="form-control mr-2" v-model="newpassword" required
              :disabled="disabled.changepassword" placeholder="Новый пароль" autocomplete="off"
              :class="{'is-invalid': errors.password}">
            <button type="submit" class="btn btn-primary mr-2"
              :disabled="disabled.changepassword">Изменить</button>
            <button type="button" class="btn btn-secondary" :disabled="disabled.add"
              @click="passgen('newpassword')" :disabled="disabled.changepassword">
              PassGen
            </button>
            <p class="text-center text-secondary mb-0">
              <small>Чекер будет автоматически разлогинен здесь и в телеграме, все его работы будут
                остановлены</small>
            </p>
          </form>
        </div>
        <div class="modal-footer">
          <button class="btn btn-light" type="button" data-dismiss="modal">Закрыть</button>
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
  window.canAddEditCheckers = {{ $canAddEditCheckers }};
</script>
<script src="/js/chance.min.js"></script>
<script src="/js/checkers.js"></script>
@endsection
