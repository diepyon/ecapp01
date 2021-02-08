<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Cart extends Model
{
    protected $fillable = [
        'stock_id', 'user_id',
    ];

    public function showCart()
    {
        $user_id = Auth::id();//ログインしているユーザーのIDを格納
        $data['my_carts'] = $this->where('user_id', $user_id)->get();
        //ログインしているユーザーのIDとuser_idカラムが同じであるデータを出力する
        //Cartテーブル見たらイメージが湧きやすい
        //id○番の人のカートのなかみ　みたいな

        $data['count']=0;//繰り返し回数カウンターを０からスタート
        $data['sum']=0;//合計金額も０からスタート
       
        foreach ($data['my_carts'] as $my_cart) {//foreachは要素数の数だけ処理を繰り返す
            $data['count']++;//1ずつアップ（繰り返し回数）これがないと繰り返し処理が走らないらしい？？？そうなん？？個数を数えてビューファイル に表示するだけでは？
            $data['sum'] += $my_cart->stock->fee;
            //ログインしているユーザーのIDとuser_idカラムが同じであるレコードの商品情報の金額部分
            //stockはbelongtoメソッドでcartテーブルとstockテーブルを超えて取得できるようにしている。
            //繰り返すたびにsumに数字が足されていく
        }
        return $data;
    }
    public function stock()
    {
        return $this->belongsTo('\App\Models\Stock');
        //cartsテーブルはstocksテーブルに従属する関係であることを表します。リレーションです
    }

    public function addCart($stock_id)
    {
        $user_id = Auth::id();
        $cart_add_info = Cart::firstOrCreate(['stock_id' => $stock_id,'user_id' => $user_id]);
        //cartテーブルのstock_idとuser_idが全く同じレコードが存在しないか確認して保存
        //（stock_idカラムとuser_idカラムに登録される値の組み合わせで既に同じものがないかを確認して保存してくれている。）
 
        if ($cart_add_info->wasRecentlyCreated) {//->既に同じ組み合わせのレコードが存在しないかを調べてから保存してくれる（wasRecentlyCreated）
            $message = 'カートに追加しました';
        } else {
            $message = 'カートに登録済みです';
        }
        return $message;
    }
    public function deleteCart($stock_id)
    {
        $user_id = Auth::id();
        $delete = $this->where('user_id', $user_id)->where('stock_id', $stock_id)->delete();
        //このモデルのテーブル（cartテーブル）におけるログインユーザーIDと同じuser_idカラムかつ、ポストされてきたstock_idとstock_idカラムが同じレコードを削除

        if ($delete>0) {//deleteメソッドは削除したレコード数を返すので、０じゃないなら削除されたレコードがある
            $message ="消せたで";
        } else {
            $message = "消されへんかったわ";
        }
        return $message;//このメッセージが関数の実行結果だ！！
    }
       public function checkoutCart()
   {
       $user_id = Auth::id(); 
       $checkout_items=$this->where('user_id', $user_id)->get();
       //このモデルのテーブル（cartテーブル）におけるログインユーザーIDと同じuser_idカラムのレコードをいったん取得

       $this->where('user_id', $user_id)->delete();
       //このモデルのテーブル（cartテーブル）におけるログインユーザーIDと同じuser_idカラムのレコードを削除（returnで実行結果を使うわけではないのでインスタンス化しなくていい）

       return $checkout_items;     
   }
}
