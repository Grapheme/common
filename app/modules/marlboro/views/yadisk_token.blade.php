@extends(Helper::acclayout())



@section('content')

    <h1>Marlboro - Ya.Disk Token</h1>

    @if (Allow::action($module['group'], 'update_token', true, false))

        <input type="text" value="{{ $token }}" class="input-lg" />

        <a href="https://oauth.yandex.ru/authorize?response_type=code&client_id={{ Config::get('site.marlboro.app_id') }}" class="btn btn-default" target="_blank">Обновить токен</a>
    @endif

    <div class="clear"></div>

    <br/>

    @if (Allow::action($module['group'], 'read_disk', true, false))

        <form action="{{ URL::route('marlboro.read') }}" method="GET" class="form-inline" target="_blank">

            Город:
            {{ Form::select('city', $cities, null, ['class' => 'form-control']) }}
            От:
            {{ Form::text('from', null, ['class' => 'datepicker']) }}
            До:
            {{ Form::text('to', null, ['class' => 'datepicker']) }}
            <button type="submit" class="btn btn-default">Получить CSV</button>

        </form>

{{--        <a href="{{ URL::route('marlboro.read') }}" class="btn btn-default" target="_blank">Получить CSV</a>--}}
    @endif

    <div class="clear"></div>

@stop


@section('scripts')
@stop

