@extends(Helper::acclayout())



@section('content')

    <!--<h1>Marlboro - Ya.Disk Token</h1>-->

    @if (Allow::action($module['group'], 'update_token', true, false))
        <a href="" class="spoiler-btn">Токен</a>
        <div class="spoiler-body">
          <input type="text" value="{{ $token }}" class="input-lg" />
          <br>
          <a href="https://oauth.yandex.ru/authorize?response_type=code&client_id={{ Config::get('site.marlboro.app_id') }}" class="btn btn-default" target="_blank">Обновить токен</a>
        </div>
    @endif

    <div class="clear"></div>

    <br/>

    @if (Allow::action($module['group'], 'read_disk', true, false))
      <center>
        <form action="{{ URL::route('marlboro.read') }}" method="GET" class="form-inline" target="_blank">

            <label><span class="label">Город:</span>
            <span class="select-holder">
            {{ Form::select('city', $cities, null, ['class' => 'form-control text-center']) }}  
            </span>
            </label>
            <br>
            <label><span class="label">От:</span>
            {{ Form::text('from', date('d.m.Y', time()-60*60*24*7), ['class' => 'datepicker text-center']) }}
            </label>
            <br>
            <label><span class="label">До:</span>
            {{ Form::text('to', date('d.m.Y'), ['class' => 'datepicker']) }}
            </label>
            <br>
            <center style="text-align: right;">
              <button type="submit" class="btn btn-default">Получить CSV</button>
            </center>

        </form>
      </center>
{{--        <a href="{{ URL::route('marlboro.read') }}" class="btn btn-default" target="_blank">Получить CSV</a>--}}
    @endif

    <div class="clear"></div>

@stop


@section('scripts')
    <style>
      @font-face {
        font-family: 'museo_sans_cyrl100';
        src: url('/uploads/fonts/museosanscyrl-100-webfont.eot');
        src: url('/uploads/fonts/museosanscyrl-100-webfont.eot?#iefix') format('embedded-opentype'),
             url('/uploads/fonts/museosanscyrl-100-webfont.woff') format('woff'),
             url('/uploads/fonts/museosanscyrl-100-webfont.ttf') format('truetype'),
             url('/uploads/fonts/museosanscyrl-100-webfont.svg#museo_sans_cyrl100') format('svg');
        font-weight: normal;
        font-style: normal;
      }

      @font-face {
          font-family: 'museo_sans_cyrl700';
          src: url('/uploads/fonts/museosanscyrl-700-webfont.eot');
          src: url('/uploads/fonts/museosanscyrl-700-webfont.eot?#iefix') format('embedded-opentype'),
               url('/uploads/fonts/museosanscyrl-700-webfont.woff') format('woff'),
               url('/uploads/fonts/museosanscyrl-700-webfont.ttf') format('truetype'),
               url('/uploads/fonts/museosanscyrl-700-webfont.svg#museo_sans_cyrl700') format('svg');
          font-weight: normal;
          font-style: normal;
      }

      @font-face {
          font-family: 'uni_sansheavy_caps';
          src: url('/uploads/fonts/unisansheavycaps-webfont.eot');
          src: url('/uploads/fonts/unisansheavycaps-webfont.eot?#iefix') format('embedded-opentype'),
               url('/uploads/fonts/unisansheavycaps-webfont.woff') format('woff'),
               url('/uploads/fonts/unisansheavycaps-webfont.ttf') format('truetype'),
               url('/uploads/fonts/unisansheavycaps-webfont.svg#uni_sansheavy_caps') format('svg');
          font-weight: normal;
          font-style: normal;
      }
      
      
      
      body {
        background: url(/uploads/img/marlboro/bg.png);
        background-position: center bottom;
      }
      #main {
        margin-left: 0;
        background: url(/uploads/img/marlboro/deco.png) no-repeat;
        background-position: center bottom;
        background-position: center 97%;
        background-size: 40%;
      }
      #header *{
        color: black !important;
      }
      
      #header {
        background-color: transparent !important;
      }
      #content .label {
        color: #e7332e !important;
        width: 50px;
        text-align: left;
        font-family: 'museo_sans_cyrl100' !important;
        display: inline-block;
      }
      
      #content button {
        position: relative;
        left: -3px;
      }
      
      #content button, #content .btn {
        border: none;
        background: none;
        padding: 0;
        margin: 0;
        font-family: 'museo_sans_cyrl700' !important;
        font-size: 14px;
        color: #ee1c24;
        font-weight: normal;
        text-transform: uppercase;
        letter-spacing: 0.3em;
        border: 3px solid #ee1c24;
        padding: 20px 50px;
        border-radius: 50px;
        margin-top: 10px;
        background: #fcfcfc;
      }
      #content a.btn {
        font-size: 10px;
        padding: 12px 30px;
        margin-top: 10px;
      }
      
      #logo-group {
        display: none;
      }
      
      #logout {
        display: none;
      }
      
      #content .form-inline {
        display: inline-block;
        text-align: left;
        margin-left: -53px;
      }
      
      #content * {
        font-family: 'museo_sans_cyrl100' !important;
      }
      #content .select-holder{
        position: relative;
        display: inline-block;
      }
      
      #content .select-holder:after{
        content: '▾';
        display: block;
        position: absolute;
        top: 0;
        right: 0;
        color: #424242;
        background: transparent;
        width: 20px;
        margin-top: 0px;
        padding: 10px 0px;
        pointer-events: none;
      }
      #content input, #content select{
        position: relative;
        border: 1px solid #5b5b5b;
        /* padding-left: 14px; */
        /* border: none; */
        background: none;
        padding: 0;
        margin: 0;
        font-family: "museo_sans_cyrl100" !important;
        font-size: 18px;
        color: #424242;
        height: 40px;
        padding: 0 30px;
        text-align: center;
        width: 260px;
        border-radius: 0;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        
      }
      #left-panel {
        display: none;
      }
      
      .spoiler-btn {
        color: #ee1c24;
      }
      
      .spoiler-body {
        display: none;
      }
    </style>
    <script>
        $(function() {
          $('.spoiler-btn').click(function(e){
            e.preventDefault();
            $(this).next('.spoiler-body').slideToggle();
          });
        });
        $('.datepicker').each(function() {

            $this = $(this);
            var dataDateFormat = $this.attr('data-dateformat') || 'dd.mm.yy';

            $this.datepicker({
                dateFormat : dataDateFormat,
                prevText : '<i class="fa fa-chevron-left"></i>',
                nextText : '<i class="fa fa-chevron-right"></i>',
            });
        })
    </script>
@stop

