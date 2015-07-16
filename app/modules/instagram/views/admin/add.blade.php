@extends('templates.'.AuthAccount::getStartPage())


@section('style')
@stop


@section('content')

    <h1>Инстаграм</h1>

    @include($module['tpl'].'/menu')

    {{ Form::open(array('url'=>link::auth($module['rest'].'/add'), 'role'=>'form', 'class'=>'smart-form', 'id'=>'insta-form', 'method'=>'post')) }}

	<div class="row margin-top-10">
        <!-- Form -->
        <section class="col col-6">
            <div class="well">
                <header>
                    Список ключевых слов, по одному на строку (хэштеги без #)
                </header>

                <fieldset>
                    <section>
                        {{--<label class="label">Список ключевых слов, по одному на строку (хэштеги без #)</label>--}}
                        <label class="textarea">
                            {{ Form::textarea('hashtags', $hashtags) }}
                        </label>
                    </section>
                </fieldset>

                <footer>
                	<a class="btn btn-default no-margin regular-10 uppercase pull-left btn-spinner" href="{{ link::previous() }}">
                		<i class="fa fa-arrow-left hidden"></i> <span class="btn-response-text">Назад</span>
                	</a>
                	<button type="submit" autocomplete="off" class="btn btn-success no-margin regular-10 uppercase btn-form-submit">
                		<i class="fa fa-spinner fa-spin hidden"></i> <span class="btn-response-text">Добавить</span>
                	</button>
                </footer>
    		</div>
    	</section>
    	<!-- /Form -->
   	</div>

    {{ Form::close() }}

@stop


@section('scripts')
    <script>
    var essence = 'advice';
    var essence_name = 'совет';
	var validation_rules = {
		name: { required: true }
	};
	var validation_messages = {
		name: { required: 'Укажите название' }
	};
    </script>

	<script src="{{ url('js/modules/48hours.js') }}"></script>
	<script src="{{ link::path('js/vendor/jquery.ui.datepicker-ru.js') }}"></script>
	<script type="text/javascript">
		if(typeof pageSetUp === 'function'){pageSetUp();}
		if(typeof runFormValidation === 'function'){
			loadScript("{{ asset('js/vendor/jquery-form.min.js'); }}",runFormValidation);
		}else{
			loadScript("{{ asset('js/vendor/jquery-form.min.js'); }}");
		}

        loadScript("{{ asset('js/plugin/bootstrap-tags/bootstrap-tagsinput.min.js') }}");
        loadScript("{{ asset('js/plugin/superbox/superbox.min.js') }}");
        loadScript("{{ asset('js/modules/gallery.js') }}");
        
	</script>

@stop
