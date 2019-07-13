@extends('layouts.app')

@section('css')
    <style>

    </style>
@endsection

@section('content')
    <section class="text-center">
        <div class="container mt-5">
            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                    <h3 class="mb-5">Админы</h3>
                    @if ($errors->any())
                        @foreach ($errors->all() as $error)
                            <div class="alert alert-dismissible alert-danger text-left">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                {{ $error }}
                            </div>
                        @endforeach
                    @else
                        @if (isset($newAdmin))
                            <div class="alert alert-dismissible alert-success text-left">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                Админ {{ $newAdmin }} успешно добавлен
                            </div>
                        @endisset
                    @endif
                    <form class="form-inline" method="POST" action="/register/admin" id="addAdmin">
                        @csrf
                        <input type="text" class="form-control mr-2 @error('name') is-invalid @enderror" value="{{ old('name') }}" name="name"
                            required placeholder="Имя пользователя" autocomplete="off">
                        <input type="text" class="form-control mr-2 @error('password') is-invalid @enderror" name="password" required
                            placeholder="Пароль" id="password" autocomplete="off" >
                        <button type="submit" class="btn btn-primary mr-2" id="addAdminButton">Добавить</button>
                        <button type="button" class="btn btn-secondary" id="passgen" >PassGen</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script src="/js/chance.min.js"></script>
    <script>
        let symbols = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $('#passgen').click(e => {
            $('#password').val(chance.string({ length: 15, pool: symbols }));
        })
        $('#addAdmin').submit(e => {
            $('#addAdminButton').attr('disabled', 'disabled');
            return true;
        })
    </script>
@endsection
