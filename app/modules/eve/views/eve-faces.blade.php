@extends(Helper::acclayout())



@section('content')

	@if ($count = @count($faces))

        <table class="table table-striped table-bordered min-table white-bg">
            @foreach ($faces as $face)
                <?
                $data = json_decode($face->data, true);
                ?>

                {{ Helper::ta($face) }}
                {{ Helper::ta($data) }}
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
                        </p>
                        @if (isset($data['phone']) && $data['phone'] != '')
                            <p>
                                <i class="fa fa-mobile-phone"></i>
                                {{ $data['phone'] }}
                            </p>
                        @endif
                        @if (isset($data['vk']) && $data['vk'] != '')
                            <p>
                                <i class="fa fa-vk"></i>
                                <a href="https://vk.com/{{ $data['vk'] }}" target="_blank">{{ $data['vk'] }}</a>
                            </p>
                        @endif
                        @if (isset($data['instagram']) && $data['instagram'] != '')
                            <p>
                                <i class="fa fa-instagram"></i>
                                {{ $data['instagram'] }}
                                <a href="https://instagram.com/{{ $data['instagram'] }}" target="_blank">{{ $data['instagram'] }}</a>
                            </p>
                        @endif
                    </td>
                </tr>

            @endforeach
        </table>

        {{ $faces->links() }}

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
@stop

