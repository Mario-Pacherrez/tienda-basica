@extends('store.template.main')

@section('content')
	<div class="container text-center">
		<div class="page-header">
			<h1><i class="fa fa-shopping-cart" aria-hidden="true"></i> Detalle del Producto</h1>
		</div>
		<div class="row">
			<div class="col-md-6">
				<div class="product-block">
					<img src="{{ asset('images/products/'.$product->image) }}">
				</div>
			</div>
			<div class="col-md-6">
				<div class="product-block">
					<h3>{{ $product->name }}</h3>
					<hr>
					<div class="product-info panel">
						<p>{{ $product->description }}</p>
						<h3>
							<span class="label label-success">
								Precio: ${{ number_format($product->price, 2) }}
							</span>
						</h3>
						<p>
							<a class="btn btn-warning btn-block" href="{{ route('cart-add', $product->slug) }}">La quiero</a>
						</p>
					</div>
				</div>
			</div>
		</div>
		<hr>
		<p>
			<a class="btn btn-primary" href="{{ route('home') }}">
				<i class="fa fa-chevron-circle-left" aria-hidden="true"></i> Regresar
			</a>
		</p>
	</div>
@endsection()