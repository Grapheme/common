<?

    $count_unapproved = Instagram::where('status', 0)->count();
    $count_approved = Instagram::where('status', 1)->count();
    $count_banned = Instagram::where('status', 2)->count();

?>

	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<div class="margin-bottom-25 margin-top-10 ">

                <? $link = link::auth2($module['rest'].'/add'); ?>
				<a class="btn btn-primary" href="{{ $link }}">
                    <? if($_SERVER['REQUEST_URI'] == $link) { echo '<i class="fa fa-check"></i> '; } ?>
                    Добавить
                </a>

                <? $link = link::auth($module['rest'].'/list?view=unapproved'); ?>
				<a class="btn btn-warning" href="{{ $link }}">
                    <? if($_SERVER['REQUEST_URI'] == $link) { echo '<i class="fa fa-check"></i> '; } ?>
                    Ожидают одобрения ({{ $count_unapproved }})
                </a>

                <? $link = link::auth($module['rest'].'/list?view=approved'); ?>
				<a class="btn btn-success" href="{{ $link }}"> 
                    <? if($_SERVER['REQUEST_URI'] == $link) { echo '<i class="fa fa-check"></i> '; } ?>
                    Одобренные ранее ({{ $count_approved }})
                </a>

                <? $link = link::auth($module['rest'].'/list?view=banned'); ?>
				<a class="btn btn-default" href="{{ $link }}"> 
                    <? if($_SERVER['REQUEST_URI'] == $link) { echo '<i class="fa fa-check"></i> '; } ?>
                    Отклоненные ({{ $count_banned }})
                </a>

			</div>
		</div>
	</div>
