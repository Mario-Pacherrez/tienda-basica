@extends('store.template.main')

@section('content')

<div class="container-fluid">
	@include('store.template.partials.slider')
</div>

<div class="container text-center">
	<div id="products">
		@foreach($products as $product)
			<div class="product white-panel">
				<h3>{{ $product->name }}</h3>
				<hr>
				<img src="{{ asset('images/products/'.$product->image) }}" alt="Producto">
				<div class="product-info panel">
					<p>{{ $product->extract }}</p>
					<h3>
						<span class="label label-success">
							Precio: ${{ number_format($product->price, 2) }}
						</span>
					</h3>
					<p>
						<a class="btn btn-warning" href="{{ route('cart-add', $product->slug) }}">
							<i class="fa fa-cart-plus" aria-hidden="true"></i> La quiero
						</a>
						<a class="btn btn-primary" href="{{ route('product-detail', $product->slug) }}">
							<i class="fa fa-chevron-circle-right" aria-hidden="true"></i> Leer más
						</a>
					</p>
				</div>
			</div>
		@endforeach
	</div>
</div>
@endsection()