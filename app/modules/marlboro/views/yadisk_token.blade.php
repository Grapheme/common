@extends(Helper::acclayout())



@section('content')

    <h1>Marlboro - Ya.Disk Token</h1>

    @if (Allow::action($module['group'], 'update_token', true, false))

        <input type="text" value="{{ $token }}" class="input-lg" />

        <a href="https://oauth.yandex.ru/authorize?response_type=code&client_id={{ Config::get('site.marlboro.app_id') }}" class="btn btn-default" target="_blank">Обновить токен</a>
    @endif


    <div class="clear"></div>

@stop


@section('scripts')
@stop
