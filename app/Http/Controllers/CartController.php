<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;

class CartController extends Controller
{
    public function __construct()
    {
        //Se crea la variable de sesión.
        if(!\Session::has('cart')){ //Si no existe la variable de sesión
            \Session::put('cart', array()); //La creo la variable de sesión y guardo un arreglo vacío
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // Show cart
    public function show()
    {
        $cart = \Session::get('cart'); //Obtener la variable de sesión
        $total = $this->total();
        return view('store.cart', compact('cart', 'total')); //Devolver la variable
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Product $product, $quantity)
    {
        $cart = \Session::get('cart');
        $cart[$product->slug]->quantity = $quantity;
        \Session::put('cart', $cart);
        return redirect()->route('cart-show');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    // Add item
    public function add(Product $product)
    {
        $cart = \Session::get('cart'); //Obtenemos la variable de seión
        $product->quantity = 1; //Agrego la propiedad de cantidad y lo predetermino con 1
        $cart[$product->slug] = $product; //Se guarda toda la información del objeto $product, en el array $cart en la posición determinada por el slug. El slug funcionará como índice.
        \Session::put('cart', $cart); //Se actualiza la variable de sesión, ya que los cambios solo existen en la variable local

        return redirect()->route('cart-show');
    }

    // Delete item
    public function delete(Product $product)
    {
        $cart = \Session::get('cart');
        unset($cart[$product->slug]); //Eliminar elementos de arrays y variables
        \Session::put('cart', $cart); //Actualizar nuestra variable de sesión
        return redirect()->route('cart-show'); //Redireccionar al método show
    }

    // Trash cart
    public function trash()
    {
        \Session::forget('cart');
        return redirect()->route('cart-show');
    }

    //Total
    public function total()
    {
        $cart = \Session::get('cart');
        $total = 0;
        foreach ($cart as $item) {
            $total += $item->price * $item->quantity;
        }
        return $total;
    }

    //Detalle del pedido
    public function orderDetail()
    {
        if(count(\Session::get('cart')) <= 0){
            return redirect()->route('home');
        }
        $cart = \Session::get('cart');
        $total = $this->total();
        return view('store.order-detail', compact('cart', 'total'));
    }
}