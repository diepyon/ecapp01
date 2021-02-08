<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Thanks extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mail_data) //編集
    {
        $this->mail_data = $mail_data; 
        //Thanksモデルにおけるmail_data(??なにこれ？変数)に引数$mail_data;の内容を格納
        //
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('mails.thanks',$this->mail_data) 
                //コントローラーから送られてきたメールの情報を本文になんかしてる？？そもそもmaiils.thanksとは？
                //marckdownもなんのこっちゃわからん
                    ->subject('Larashopでのご購入ありがとうございます'); //メールタイトル
    }
}
