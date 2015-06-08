@extends(Helper::acclayout())



@section('content')

    <h1>EVE - Лица</h1>

    <p>
        <a href="?filter_status=0&order_by=created_at&order_type=ASC" class="btn btn-default">Новые</a>
        <a href="?filter_status=1&order_by=updated_at&order_type=DESC" class="btn btn-success">Одобренные</a>
        <a href="?filter_status=2&order_by=created_at&order_type=ASC" class="btn btn-warning">Отложенные</a>
        <a href="?filter_status=3&order_by=updated_at&order_type=DESC" class="btn btn-danger">Отклоненные</a>
        |
        <a href="{{ URL::route('eve.full_delete')  }}" class="btn btn-danger" onclick="return confirm('ВНИМАНИЕ! Будут удалены все данные. Продолжить?')" target="_blank">Очистить базу</a>
    </p>
    <br/>

	@if ($count = @count($faces))

        <table class="table table-striped table-bordered min-table white-bg">
            @foreach ($faces as $face)
                <?
                $data = json_decode($face->data, true);
                ?>

{{--                {{ Helper::ta($face) }}--}}
{{--                {{ Helper::ta($data) }}--}}
                <tr>
                    <td class="text-center">
                        <img src="{{ URL::to('/uploads/eve/' . $face->image) }}" width="200" />
                    </td>
                    <td>
                        <p>
                            <strong>
                                {{ $data['name'] }}
                                {{ $data['lastname'] }}
                            </strong>
                            <br/>
                            {{ $face->city }}
                            <br/>
                            {{ $face->created_at->format('d.m.Y H:i:s') }} # {{ $face->id }}
                        </p>
                        <p data-id="{{ $face->id }}">
                            <button class="btn btn-success change_status_button" data-status="1">Одобрить</button>
                            <button class="btn btn-warning change_status_button" data-status="2">Отложить</button>
                            <button class="btn btn-danger change_status_button" data-status="3">Отклонить</button>
                        </p>
                        @if (isset($data['phone']) && $data['phone'] != '')
                            <p>
                                <i class="fa fa-fw fa-mobile-phone"></i>
                                {{ $data['phone'] }}
                            </p>
                        @endif
                        @if (isset($data['vk']) && $data['vk'] != '')
                            <p>
                                <i class="fa fa-fw fa-vk"></i>
                                <a href="https://vk.com/{{ $data['vk'] }}" target="_blank">{{ $data['vk'] }}</a>
                            </p>
                        @endif
                        @if (isset($data['instagram']) && $data['instagram'] != '')
                            <p>
                                <i class="fa fa-fw fa-instagram"></i>
                                <a href="https://instagram.com/{{ $data['instagram'] }}" target="_blank">{{ $data['instagram'] }}</a>
                            </p>
                        @endif
                        @if (isset($data['answer']) && $data['answer'] != '')
                            <p>
                                <i class="fa fa-fw fa-quote-left"></i>
                                <i>
                                    {{ $data['answer'] }}
                                </i>
                            </p>
                        @endif
                    </td>
                </tr>

            @endforeach
        </table>

        {{ $faces->appends(Input::all())->links() }}

	@else

        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <div class="ajax-notifications custom">
                    <div class="alert alert-transparent">
                        <h4>Список пуст</h4>
                        <p><br><i class="regular-color-light fa fa-th-list fa-3x"></i></p>
                    </div>
                </div>
            </div>
        </div>

	@endif

    <div class="clear"></div>

@stop


@section('scripts')
    <script>
        $('.change_status_button').click(function(){
            var $button = $(this);
            var $buttons = $($button).parent().find('button');
            var $line = $($button).parents('tr');
            var $status = $($button).data('status');
            var $id = $($button).parent().data('id');

            //alert($id + " > " + $status);

            $($buttons).attr('disabled', 'disabled');

            $.ajax({
                url: "{{ URL::route('eve.change_status') }}",
                data: {'id': $id, 'status': $status},
                method: "POST"
            })
                    .done(function (result) {
                        console.log(result);
                        //alert("success");
                        if (result.hide) {
                            $($line).slideUp("slow");
                        } else {
                            $($buttons).removeAttr('disabled');
                        }
                    })
                    .fail(function () {
                        //alert("error");
                        $($buttons).removeAttr('disabled');
                    })
                    .always(function () {
                        //alert("complete");
                    });
        });
    </script>
@stop

