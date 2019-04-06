@if($messages = Notify::get())
	<div class="container">
		@foreach($messages as $level=>$messages2)
			@foreach($messages2 as $message)
				<div class="alert-{{ $level }} alert" role="alert">{{ $message }}<button type="button" class="close" data-dismiss="alert">×</button></div>
			@endforeach
		@endforeach
	</div>
@endif