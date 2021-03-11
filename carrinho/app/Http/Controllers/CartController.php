<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Order;
use App\Product;
use App\ProductOrder;
use App\DiscountCoupon;

class CarrinhoController extends Controller
{
    function __construct(){
        //obrigatoriedadade para o login
        $this->middleware('auth');
    }


public function index(){
    $request = Order::where([
        'status'=>'RE', //Reservado
        'user_id' => Auth::id()
    ])->get();
    return view('carrinho.index', compact('request'));
}
//Adicionar ao Carrinho os Produtos
public function add(){

    $this->middleware('VerifyCsfToken');

    $req = Request();
    $idproduct = $req->input('id');

    $product = Product::find($idproduct);

//Tratamento de erro     
    if(empty($product->id)){
        $req->session()->flash('Mensagem-falha', 'Produto não encontrado em nossa loja!');
        return redirect()->route('carrinho.index');
    }

    $iduser = Auth::id();

    $idorder = Order::consultId([
        'user_id' => $iduser,
        'status'  =>'RE' //Reservado
    ]);

    if(empty($idorder)){
        $order_new = Order::create([
            'user_id'=> $iduser,
            'status' => 'RE' // Reservado
        ]);

        $idorder = $order_new->id;
    }

    ProductOrder::create([
        'order_id' => $idorder,
        'product_id'=>$idproduct,
        'status'=>'RE' //Reservado
    ]);

    $req->session()->flash('Mensagem-sucesso', 'Produto adicionado ao carrinho com sucesso!');
    return redirect()->route('carrinho.index');

}

//Remover do carrinho os Produtos
public function remove(){
      $this->middleware('VerifyCsrfToken');

      $req = Request();

    $idorder            =  $req->input('order_id');
    $idproduct          =  $req->input('product_id');
    $remove_only_item   =  (boolean)$req->input('item');
    $iduser             =  Auth::id();

    $idorder = Order::consultId([
        'id'=>$idorder,
        'user_id'=>$iduser,
        'status'=>'RE' //Reservado
    ]);
//Tratamento de erro 
    if(empty($idorder)){
        $req->session()->flash('Mensagem-falha','Pedido não foi encontrado!');
        return redirect()->route('carrinho.index');
    }

    $where_product = [
        'order_id' =>$idorder,
        'product_id'=>$idproduct
    ];

    $product = ProductOrder::where($where_product)->orderBy('id', 'desc')->first();

//Tratamento de erro 
    if(empty($product->id)){
        $req->session()->flash('Mensagem-falha','Produto não foi encontrado no carrinho!');
        return redirect()->route('carrinho.index');
    }

    if($remove_only_item){
        $where_product['id'] = $product->id;
    }
    ProductOrder::where($where_product)->delete();

    $check_order = ProductOrder::where([
        'order_id'=>$product->order_id
    ])->exists();

//Tratamento de erro 
    if(!$check_order){
        Order::where([
            'id' =>$product->order_id
        ])->delete();
    }
    $req->session()->flash('Mensagem-sucesso','Produto removido do carrinho com sucesso!');
    return redirect()->route('carrinho.index');
}

//Concluir compra do carrinho
public function conclude(){

        $this->middleware('VerifyCsrfToken');

        $req = Request();
        $idorder = $req->input('order_id');
        $iduser = Auth::id();

    $check_order = Order::where([
        'id'       =>     $idorder,
        'user_id'  =>     $iduser,
        'status'   =>     'RE'//Reservado
    ])->exists();

//Tratamento de erro 
    if(!$check_order){
        $req->session()->flash('Mensagem-falha','Pedido não foi encontrado!');
        return redirect()->route('carrinho.index');
    }

    $check_products = ProductOrder::where([
        'order_id' =>$idorder
    ])->exists();

//Tratamento de erro 
    if(!$check_products){
        $req->session()->flash('Mensagem-falha', 'Os produtos inclusos no pedido não foram encontrados!');
        return redirect()->route('carrinho.index');
    }

    OrderProduct::where([
       'order_id' => $idorder
    ])->update([
        'status' =>'PA' //Pago
    ]);

    Order::where([
        'id' => $idorder
     ])->update([
         'status' =>'PA' //Pago
     ]);

$req->session()->flash('Mensagem-sucesso', 'Compra foi concluída com sucesso!');

return redirect()->route('carrinho.compras');

}

//Comprar no carrinho
public function buy(){

    $shopping = Order::where([
        'status' => 'PA', //Pago
        'user_id' => Auth::id()
    ])->orderBy('updated_at', 'desc')->get();

    $canceled = Order::where([
        'status' =>'CA', //Cancelado
        'user_id' =>Auth::id()
    ])->orderBy('updated_at','desc')->get();

return view('carrinho.compras', compact('compras', 'Cancelados!'));

}

//Cancelamento no carrinho
public function cancel(){

    $this->middleware('VerifyCsrfToken');

    $req = Request();
    $idproduct      =  $req->input('order_id');
    $idsorder_prod  =  $req->input('id');
    $iduser         =  Auth::id();

//Tratamento de erro 
    if(empty($idsorder_prod)){
        $req->session()->flash('Mensagem-falha','Nenhum item foi selecionado para o cancelamento!');
        return redirect()->route('carrinho.compras');
    }

   $check_order = Order::where([
       'id'        =>  $idorder,
       'user_id'   =>  $iduser,
       'status'    => 'PA' //Pago
   ])->exists();

//Tratamento de erro
   if(!$check_order){
       $req->session()->flash('Mensagem-falha','Pedido não foi encontrado para o cancelamento!');
       return redirect()->route('carrinho.compras');
   }

$check_products = ProductOrder::where([
    'order_id' =>$idorder,
    'status' => 'PA' //Pago
])->whereIn('id', $idsorder_prod)->exists();

//Tratamento de erro
if(!$check_products){
    $req->session()->flash('Mensagem-falha', 'Produtos do pedido não foram encontrados!');
    return redirect()->route('carrinho.compras');
}

ProductOrder::where([
    'order_id'   =>  $idorder,
    'status'     =>  'PA' //Pago
])->whereIn('id', $idsorder_prod)->update([
    'status'     =>  'CA' //Cancelado
]);

$check_order_cancel = ProductOrder::where([
    'order_id'   =>  $idorder,
    'status'     =>  'PA' //Pago
])->exists();


if(!$check_order_cancel){
    Order::where([
        'id'     => $idorder
    ])->update([
        'status' => 'CA' //Cancelado
    ]);

    $req->session()->flash('Mensagem-sucesso','Compras canceladas com sucesso!');

}else{
    $req->session()->flash('Mensagem-sucesso','Item(ns) da Compra foram cancelados com sucesso!');
}
return redirect()->route('carrinho.compras');

}

//Desconto do carrinho
public function discount(){

    $this->middleware('VerifyCsrfToken');

    $req = Request();
    $idorder    =  $req->input('order_id');
    $coupon     =  $req->input('coupon');
    $iduser     =  Auth::id();
    
//Tratamento de erro    
   if(empty($coupon)){
       $req->session()->flash('Mensagem-falha','Cupom inválido!');
       return redirect()->route('carrinho.index');
   }

$coupon = DiscountCoupon::where([
     'locator' =>$coupon,
     'active' => 'S'
])->where('dthr_validity', '>', date('Y-m-d H:i:s'))->first();

//Tratamento de erro 
if(empty($coupon->id)){
    $req->session()->flash('Mensagem-falha','Cupom de desconto não foi encontrado!');
    return redirect()->route('carrinho.index');
}

$check_order = Order::where([
    'id'       =>  $idorder,
    'user_id'  =>  $iduser,
    'status'   =>  'RE' //Reservado
])->exists();

//Tratamento de erro 
if(!$check_order){
    $req->session()->flash('Mensagem-falha','Pedido não foi encontrado para a validação!');
    return redirect()->route('carrinho.index');
}

$products_order = ProductOrder::where([
    'order_id'     =>  $idorder,
    'status'       =>  'RE' //Reservado
])->get();

//Tratamento de erro 
if(empty($products_order)){
    $req->session()->flash('Mensagem-falha','Produtos do pedido não foram encontrados!');
    return redirect()->route('carrinho.index');
}

//Aplicando Cupom no(s) produtos
$applied_discount = false;
foreach ($products_order as $product_order){
  
    switch ($coupon->discount_mod) {
        case 'porc':
            $value_discount = ($product_order->value*$coupon->discount) / 100;
            break;
        
        default:
            $value_discount = $coupon->discount;
            break;
    }

$value_discount = ($value_discount > $product_order->value) ? $product_order->value : number_format($value_discount, 2);

    switch ($coupon->limit_mod) {
        case 'qtd':
            $qtd_order = ProductOrder::whereIn('status',['PA', 'RE'])->where([
                  'coupon_discount_id' => $coupon->id
                ])->count();

            if($qtd_order >= $coupon->limit){
                continue;
            }
            break;
        
        default:
             $value_ckc_discount = ProductOrder::whereIn('status',['PA','RE'])->where([
                 'coupon_discount_id' =>$coupon->id
             ])->sum('discount');

             if(($value_ckc_discount + $value_discount) > $coupon->limit){
                 continue;
             }
            
            break;
    }
     
    $product_order->coupon_discount_id = $coupon->id;
    $product_order->discount  = $value_discount;
    $product_order->update();

    $applied_discount = true;

}

if( $applied_discount ) {
    $req->session()->flash('Mensagem-sucesso', 'Cupom aplicado com sucesso!');
} else {
    $req->session()->flash('Mensagem-falha', 'Cupom esgotado!');
}
return redirect()->route('carrinho.index');

}


}
