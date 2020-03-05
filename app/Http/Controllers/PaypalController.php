<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Foundation\Bus\DispatchesCommands;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;

use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\ExecutePayment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\Transaction;

use App\Order;
use App\OrderItem;

class PaypalController extends Controller
{
	//Variable que va a contener todas las configuraciones del entorno que vamos a utilizar
    private $_api_context;

    public function __construct()
    {
    	//Setup Paypal api context
    	$paypal_conf = \Config::get('paypal'); //Usaremos nuestro archivo paypal
    	$this->_api_context = new ApiContext(new OAuthTokenCredential($paypal_conf['client_id'], $paypal_conf['secret'])); //Se usan los datos
    	$this->_api_context->setConfig($paypal_conf['settings']);
    }

    //Función donde se va a configurar todo lo que se enviará a Paypal 
    public function postPayment()
    {
    	$payer = new Payer(); //Se crea el objeto del pagador o cliente
    	$payer->setPaymentMethod('paypal'); //Se configura la variable de tipo paypal

    	$items = array(); //Se crea un arreglo
    	$subtotal = 0; //Iniciar nuestro subtotal en 0
    	$cart = \Session::get('cart'); //Obtener toda la información de nuestro carrito

    	if(count($cart) === 0){
    		return redirect()->route('home')->with('message', 'El carrito esta vació.');
    	}

    	$currency = 'USD'; //Configurar la moneda

    	foreach ($cart as $producto) { //Recorremos todo nuestro carrito
    		$item = new Item(); //Por cada producto, vamos a crear un objeto llamado item
    		$item->setName($producto->name)
    			 ->setCurrency($currency)
    			 ->setDescription($producto->extract)
    			 ->setQuantity($producto->quantity)
    			 ->setPrice($producto->price);

    		$items[] = $item; //Se agrega todo esto al arreglo
    		$subtotal += $producto->quantity * $producto->price; //Vamos ir obteniendo nuestro subtotal
    	}

    	$item_list = new ItemList();
    	$item_list->setItems($items);

    	$details = new Details();
    	$details->setSubtotal($subtotal)
    			->setShipping(100); //Costo por envio

    	$total = $subtotal + 100;

    	$amount = new Amount();
    	$amount->setCurrency($currency)
    		   ->setTotal($total)
    		   ->setDetails($details);

    	$transaction = new Transaction();
    	$transaction->setAmount($amount)
    				->setItemList($item_list)
    				->setDescription('Pedido de prueba en mi Laravel App Store');

    	$redirect_urls = new RedirectUrls();
    	$redirect_urls->setReturnUrl(\URL::route('payment.status')) //Si procede con la compra
    				  ->setCancelUrl(\URL::route('payment.status')); //Si cancela la compra

    	$payment = new Payment();
    	$payment->setIntent('Sale')
    			->setPayer($payer)
    			->setRedirectUrls($redirect_urls)
    			->setTransactions(array($transaction));

    	try{
    		$payment->create($this->_api_context); //Se hará la conexión a través del API a Paypal, para que valide nuestro objeto.
    	} catch(\PayPal\Exception\PayPalConnectionException $ex){ //Si hay algún problema se va a lanzar una excepción del tipo mencionado.
    		if(\Config::get('app.debug')){ //Si tenemos habilidado nuestro debug nos mostrará los mensajes de errores.
    			echo "Exception: ".$ex->getMessage() . PHP_EOL;
    			$err_data = json_decode($ex->getData(), true);
    			exit;
    		} else {
    			die('Ups! Algo salió mal.'); //Sino de todas formas nos mostrará un mensaje de que algo salio mal.
    		}
    	}

    	//Si todo salio bien, paypal nos devolverá la información
    	foreach ($payment->getLinks() as $link) {
    		if($link->getRel() == 'approval_url' ){
    			$redirect_urls = $link->getHref();
    			break;
    		}
    	}

    	//Dentro de la respuesta que da Paypal, viene otro dato
    	//Add payment ID to session
    	\Session::put('paypal_payment_id', $payment->getId()); //Obtenemos el id para darle seguimiento a la sesión del usuario y lo guardamos en una variable de sesión.

    	if(isset($redirect_urls)){ //Si todo salio bien, obtendremos esta variable y redireccionamos al usuario a esa url
    		//redirect to paypal
    		return \Redirect::away($redirect_urls);
    	}
    	return \Redirect::route('cart-show')->with('message', 'Ups! Error desconocido.'); //Si hubo algún problema, entonces solo lo redireccionamos al carrito con ese mensaje.
    	//dd('auqi');
    }

    //Método al cual nos da respuesta Paypal
    public function getPaymentStatus()
    {
    	//Get the payment ID before session clear
    	$payment_id = \Session::get('paypal_payment_id'); //Obteniene esa id de seguimiento y la guardamos en una variable

    	//Clear the session payment ID
    	\Session::forget('paypal_payment_id'); //Eliminamos esa variable de sesión porque ya no se va a usar.

    	//Y dentro de las respuestas de nos da Paypal, nos viene estos datos.
    	$payerId = \Input::get('PayerID');
    	$token = \Input::get('token');

    	if(empty($payerId) || empty($token)){
    		return \Redirect::route('home')->with('message', 'Hubo un problema al intentar pagar con Paypal.');

    		//dd($payerId);
    		//dd($token);
    	}

    	$payment = Payment::get($payment_id, $this->_api_context); //Si todo sale bien, obtenemos de nuestro contexto el objeto Payment

    	$execution = new PaymentExecution(); //Creamos un nuevo objeto
    	$execution->setPayerId(\Input::get('PayerID')); //Le configuramos el valor que nos devolvio Paypal

    	//Execute the payment
    	$result = $payment->execute($execution, $this->_api_context); //Ejecutar la compra, se realiza la transacción completa.

    	if($result->getState() == 'approved' ){ //si nos devuelve el estado approved
    		//Aquí es donde tomaría la información del pedido y guardaría esa información en la base de datos.
    		$this->saveOrder();

    		\Session::forget('cart');

    		return \Redirect::route('home')->with('message', 'Compra realizada de forma correcta.');
    	}

    	return \Redirect::route('home')->with('message', 'La compra fue cancelada.');
    }

    protected function saveOrder()
    {
    	$subtotal = 0;
    	$cart = \Session::get('cart');
    	$shipping = 100;

    	foreach ($cart as $producto) {
    		$subtotal += $producto->quantity * $producto->price;
    	}

    	$order = Order::create([
    		'subtotal' => $subtotal,
    		'shipping' => $shipping,
    		'user_id' => \Auth::user()->id
    	]);

    	foreach ($cart as $producto) {
    		$this->saveOrderItem($producto, $order->id);
    	}
    }

    protected function saveOrderItem($producto, $order_id)
    {
    	OrderItem::create([
    		'price' => $producto->price,
    		'quantity' => $producto->quantity,
    		'product_id' => $producto->id,
    		'order_id' => $order_id
    	]);
    }
}