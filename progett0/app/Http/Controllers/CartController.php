<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\Order;
use App\Models\Item;
use App\Models\Cronologia;
use Illuminate\Support\Facades\Input;


class CartController extends Controller
{
    //lettura prodotti da file di sessione aggiornamento totale da pagare
    public function index(Request $request) {
        
        $total = 0;
        $productsDetail = [];

        $productsInSession = $request->session()->get("products"); //legge i prodotti dal file di sessione
        
        if($productsInSession!=null){

            //legge i dati dei prodotti dal db passando solo attributo pk 
            $productsDetail = Product::findMany(array_keys($productsInSession)); 
         
            
               //calcoliamo totale passando alla funzione sia i prodotti nel carrello 
            //che quelli nel file di sessione
            $total = Product::calcTotal($productsDetail,$productsInSession);
            
        }

        $lastViewedProducts = Cronologia::orderBy("id", "desc")
            ->where("user_id", Auth::user()->getId())
            ->take(4)
            ->get();

        $listOfSuggestedRows = array();

        foreach($lastViewedProducts as $cronologia){
            $suggestedRowsByCronologia = Product::where("specie", $cronologia->getProduct()->getSpecie())->where("id","!=", $cronologia->getProduct()->getId())->limit(3)->get();
            if(!CartController::check($listOfSuggestedRows, $suggestedRowsByCronologia)){
                $listOfSuggestedRows[] = $suggestedRowsByCronologia;
            }
        }


        $viewData['title'] = 'Carrello - online store';
        $viewData['subtitle'] = 'Prodotti nel carrello';
        $viewData['total'] = $total;
        $viewData['balanceUpdate'] = Auth::user()->getBalance() - $total;
        $viewData['products'] = $productsDetail;
        $viewData['suggested'] = $listOfSuggestedRows;

        return view('cart.index')->with('viewData',$viewData);

    }

    public function check($listOfSuggestedRows, $suggestedRowsByCronologia){
        if($listOfSuggestedRows != null){
            foreach($listOfSuggestedRows as $suggestedRows){
                foreach($suggestedRows as $suggestedRow){
                    foreach($suggestedRowsByCronologia as $row){
                        if($suggestedRow->getId() == $row->getId()){
                            $suggestedRowsByCronologia->pull($suggestedRowsByCronologia->search($row));
                        }
                    }
                }
            }
        }
        return false;
    }

     //aggiornamento file di sessione con aggiunta prodotto
    public function add(Request $request , $id){

        //leggiamo i prodotti in sessione
        $productsInSession = $request->session()->get('products');
        
        $qta = $request->input('qta');
        //aggiorniamo le quantità per il prodotto di cui riceviamo id
        $productsInSession[$id] = $qta;
        
        //aggiornamento della sessione
        $request ->session()->put('products',$productsInSession);

        return redirect()->route('cart.confirm', ['id' => $id, 'qta' => $qta]);

    }

     //delete all products
    public function delete (Request $request){
        
         $request->session()->forget('products');
         return back();
    }
     //TODO eliminare solo un prodotto
    public function deleteProduct (Request $request , $id){
    
         $request->session()->forget('products.' . $id);
        
        return back();

    }

     public function purchase(Request $request){
     
        $productsInSession = $request->session()->get("products");
      
        $userId = Auth::user()->getId();
        $order = new Order();
        $order->setUserId($userId);

        //calcolo totale
        $productsDetail = Product::findMany(array_keys($productsInSession)); 
        $total = Product::calcTotal($productsDetail,$productsInSession);
        $order->setTotal($total);

        //salvataggio sul db dell'ordine
        $order -> save();

        //aggiorna tabella items
        foreach($productsDetail as $product){
            $item = new Item();
            $item->setQuantity($productsInSession[$product->getId()]);
            $item->setPrice($product->getPrice());
            $item->setOrderId($order->getId());
            $item->setProductId($product->getId());
            $item->save();

        }

        //aggiorna portafoglio dello user se la scelta del pagamento è stata portafoglio virtuale
        $string = $request->tipologia;
        
        if($string == 'Portafoglio virtuale')
        {

            $balanceUpdate = Auth::user()->getBalance() - $total;

            if($balanceUpdate<0)
            {
                return redirect()->route('myaccount.balance');
            }
            Auth::user()->setBalance($balanceUpdate);
            Auth::user()->save();
            
            $viewData['strPagamento'] = "Portafoglio virtuale aggiornato con successo!";
            $viewData['balance'] = "Il tuo portafoglio ammonta a ".$balanceUpdate." euro";
        }
        else
        {
            $viewData['strPagamento'] = "Ricordati di preparare il contante giusto per la data di consegna";
            $viewData['balance'] = "";
        }
        
        //svuota file di sessione
        $request->session()->forget('products');

        $viewData['title'] = 'Ordine - E-commerce';
        $viewData['subtitle'] = 'Riepilogo ordine';
        $viewData['order'] = $order;
       

        return view("cart.purchase")->with("viewData" , $viewData);
    }
    public function confirm($id, $qta) {
    
        $viewData = [];

        $product = Product::findOrFail($id);
    
        $suggestedBySpecieAmbiente = Product::where("specie",$product->getSpecie())->where("ambiente", $product->getAmbiente())->where("id","!=", $product->getId())->limit(6)->get();
        $suggestedBySpecie = Product::where("specie",$product->getSpecie())->where("ambiente","!=", $product->getAmbiente())->where("id","!=", $product->getId())->limit(6)->get();

        $viewData["title"] = $product['name']. " - Online Store";
        $viewData["subtitle"] = "Descrizione prodotto";
        $viewData["product"] = $product;
        $viewData["qta"] = $qta;
        $viewData["suggestedBySpecieAmbiente"] = $suggestedBySpecieAmbiente;
        $viewData["suggestedBySpecie"] = $suggestedBySpecie;
        
        return view('cart.confirm')->with("viewData",$viewData);
    }
}