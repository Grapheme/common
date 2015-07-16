@extends(Helper::acclayout())


@section('content')

    <h1>Инстаграм</h1>

    @include($module['tpl'].'/menu')

	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<div class="ajax-notifications custom">
				<div class="alert alert-transparent">
					Выберите желаемое действие
				</div>
			</div>
		</div>
	</div>

@stop


@section('scripts')

	<script type="text/javascript">
		if(typeof pageSetUp === 'function'){pageSetUp();}
		if(typeof runFormValidation === 'function'){
			loadScript("{{ asset('js/vendor/jquery-form.min.js'); }}", runFormValidation);
		}else{
			loadScript("{{ asset('js/vendor/jquery-form.min.js'); }}");
		}
	</script>

@stop

