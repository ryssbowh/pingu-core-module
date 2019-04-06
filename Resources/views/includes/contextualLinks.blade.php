@if( $links = ContextualLinks::get() )
	<div class="contextualLinks">
		<ul>
			@foreach( $links as $name => $details )
				<li @if($name ==  ContextualLinks::getActiveLink()) class="active"@endif><a href="{{ $details['url'] }}">{{ $details['title'] }}</a></li>
			@endforeach
		</ul>
	</div>
@endif
