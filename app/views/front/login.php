
<div>
	@if(){
		<div>
			@foreach($users as $user){
				<a>{{ $user->name }}</a>
			}
		</div>
	}else{
		not found any user!
	}



	@if()
		<div>
			@foreach($users as $user)
				@code

				@end
				<a>{{ $user->name }}</a>
			@end
		</div>
	@else
		not found any user!
	@end



	@if()
		<div>
			@foreach($users as $user)
				@code

				$a = 12;

				@endcode
				<a>{{ $user->name }}</a>
			@endforeach
		</div>
	@else
		not found any user!
	@endif
</div>