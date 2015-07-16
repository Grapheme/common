@extends('templates.'.AuthAccount::getStartPage())


@section('style')
<link rel="stylesheet" href="{{ link::path('css/redactor.css') }}" />
@stop


@section('content')
{{ Form::model($advice, array('url'=>link::auth($module['rest'].'/update/'.$advice->id), 'class'=>'smart-form', 'id'=>'advice-form', 'role'=>'form', 'method'=>'post')) }}

    <!-- Fields -->
	<div class="row margin-top-10">

        <!-- Form -->
        <section class="col col-6">
            <div class="well">
                <header>Изменить совет:</header>
                <fieldset>
                    <section>
                        <label class="label">Название</label>
                        <label class="input"> <i class="icon-append fa fa-list-alt"></i>
                            {{ Form::text('name', @$advice->name) }}
                        </label>
                    </section>

                    <section>
                        <label class="label">Описание</label>
                        <label class="input">
                            {{ Form::textarea('desc', @$advice->desc, array('class'=>'redactor redactor_150')) }}
                        </label>
                    </section>

                    @if (Allow::module('admin_tags'))
                    <section>
                        <label class="label">Теги</label>
                        <label class="input">
                            {{ ExtForm::tags('tags', @$advice->tags($module['name'])) }}
                        </label>
                    </section>
                    @endif

                    @if (Allow::module('admin_galleries'))
                    <section>
                        <label class="label">Галерея</label>
                        <label class="input">
                            {{ ExtForm::gallery('gallery', @$gallery) }}
                        </label>
                    </section>
                    @endif
                    
                </fieldset>
		    </div>
    	</section>
        <!-- /Form -->
   	</div>

	<div style="float:none; clear:both;"></div>
	
    <section class="col-6">
        <footer>
        	<a class="btn btn-default no-margin regular-10 uppercase pull-left btn-spinner" href="{{URL::previous()}}">
        		<i class="fa fa-arrow-left hidden"></i> <span class="btn-response-text">Назад</span>
        	</a>
        	<button type="submit" autocomplete="off" class="btn btn-success no-margin regular-10 uppercase btn-form-submit">
        		<i class="fa fa-spinner fa-spin hidden"></i> <span class="btn-response-text">Сохранить</span>
        	</button>
        </footer>
    </section>


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
	<script src="{{ link::path('js/vendor/redactor.min.js') }}"></script>
	<script src="{{ link::path('js/system/redactor-config.js') }}"></script>
@stop