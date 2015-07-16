@extends(Helper::acclayout())


@section('content')

    <h1>Инстаграм</h1>

    @include($module['tpl'].'/menu')

	@if(@$posts->count())
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						<th class="text-center">Инстаграм</th>
						<th colspan="2" class="width-250 text-center">Действия</th>
					</tr>
				</thead>
				<tbody>
				@foreach($posts as $post)

{{ Helper::dd_($post) }}

					<tr>
						<td class="text-center">
						    <img src="{{ $post->image }}" style="max-width:300px" />
                            <p class="margin-top-10">
                            {{ Helper::dd_($post->tags) }}
                            @foreach ($post->tags as $tag)
                                #{{ $tag->tag }}
                            @endforeach
                            </p>
						</td>
						<td class="text-center">

                            @if ($post->status != '2')
                            {{ Form::open(array('url'=>link::auth($module['rest'].'/approve/'.$post->id), 'role'=>'form', 'class'=>'smart-form', 'id'=>'post-form', 'method'=>'post')) }}
                            <label class="toggle width-100 margin-center">

                                {{ Form::checkbox('approved', ($post->status==0?0:1), (bool)$post->status, array('class' => 'approve-checkbox', 'data-id' => $post->id)) }}
                                <i data-swchon-text="да" data-swchoff-text="нет"></i>
                                Одобрено:
                            </label>
                            {{ Form::close() }}
                            @endif

                            <div class="margin-top-10">
                                Добавлено: <abbr title="{{ $post->created_at->format("H:i:s") }}">{{ $post->created_at->format("d.m.Y") }}</abbr>
                            </div>


                            @if ($post->status != '2')
							<form method="POST" action="{{ link::auth($module['rest'].'/banned/'.$post->id) }}">
								<button type="button" class="btn btn-default margin-top-10 banned-post">
									Отклонить
								</button>
							</form>
                            @else
							<form method="POST" action="{{ link::auth($module['rest'].'/approve/'.$post->id) }}">
								<button type="button" class="btn btn-default margin-top-10 approve-post">
									Одобрить
								</button>
							</form>
                            @endif

                            {{--
							<form method="POST" action="{{ link::auth($module['rest'].'/destroy/'.$post->id) }}">
								<button type="button" class="btn btn-default margin-top-10 remove-post">
									Удалить
								</button>
							</form>
                            --}}
						</td>
					</tr>
				@endforeach
				</tbody>
			</table>
		</div>
	</div>

    {{ $posts->appends(array('view' => Input::get('view')))->links() }}

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
@stop


@section('scripts')
    <script>
    var essence = 'post';
    var essence_name = 'фото';
	var validation_rules = {
		name: { required: true }
	};
	var validation_messages = {
		name: { required: 'Укажите название' }
	};
    </script>

	{{ HTML::script('private/js/modules/standard.js') }}

	<script type="text/javascript">
		if(typeof pageSetUp === 'function'){pageSetUp();}
		if(typeof runFormValidation === 'function') {
			loadScript("{{ asset('private/js/vendor/jquery-form.min.js'); }}", runFormValidation);
		} else {
			loadScript("{{ asset('private/js/vendor/jquery-form.min.js'); }}");
		}
	</script>

@stop

