<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use Illuminate\Support\Facades\Mail; //追記
use App\Mail\Thanks;//追記

class ShopController extends Controller
{
    public function index() //追加
    {
        $stocks = Stock::Paginate(6); //Eloquantで検索
        return view('shop', compact('stocks')); //追記変更
    }
    public function myCart(Cart $cart)
    {
        $data = $cart->showCart();
        return view('mycart', $data);
        //配列$dataをビューファイル に渡す
    }
 

    public function addMycart(Request $request, Cart $cart)//cartに商品を追加するメソッド
    {
        //カートに追加の処理
       $stock_id=$request->stock_id;//$stock_idはページ上からpostされたstock_idの番号です
       $message = $cart->addCart($stock_id);//

       //追加後の情報を取得
        $data = $cart->showCart();//cartモデルにおけるshowcartの実行結果を格納
    
         return view('mycart', $data)->with('message', $message); //追記
    }
    public function deleteCart(Request $request, Cart $cart) //カートの中身を消すメソッド
    {
        //カートの中身を削除する処理(
       $stock_id=$request->stock_id;//$stock_idはフォームから送信されてきたid
       $message = $cart->deleteCart($stock_id);//CartモデルにおけるdeleteCartメソッドを発動（引数はフォームからポストされたstock_id）

        //削除後のカートの内容を変数に格納
        $data = $cart->showCart();

        return view('mycart', $data)->with('message', $message); //追記

        //配列$dataをビューファイル->メソッド実行結果を格納した$messageも渡す（$data['message']=$message;と同じ意味）
    }
    public function checkout(Request $request, Cart $cart)//決済完了後にカートの中身を消すメソッド
    {
        $user = Auth::user();
        $checkout_info = $cart->checkoutCart();//インスタンス化　$checkout_infoにcartモデルにおけるチェックアウトメソッドを格納
        $mail_data['user']=$user->name; //連想配列$mail_dataのuserキーにログインユーザーのnameカラムの丈夫を格納（詳しくはusersテーブルを見ろ）
        $mail_data['checkout_items']=$cart->checkoutCart(); //連想配列$mail_dataのcheckout_itemsrキーにCartモデルのcheckoutCart()メソッド実行結果を格納
        Mail::to($user->email)->send(new Thanks($mail_data));//ログインユーザーのメールアドレスにthanksメールを送信　引数には連想配列$mail_dataの情報を持たせている。
        return view('checkout');
    }
}
