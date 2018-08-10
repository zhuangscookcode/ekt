<?php
/**
 * Created by PhpStorm.
 * User: symm1
 * Date: 2016/04/26
 * Time: 15:53
 */

namespace App;

use DB;
use Illuminate\Support\Facades\Mail;

class FuncMail
{
    //mail send function
    public static function sendTo( $user, $subject, $view, $data){

        Mail::send($view, $data, function($message) use($user, $subject){
            $message->to($user)
                ->subject($subject);
        });

    }

    //mail send function
    public static function sendToWithCC( $user, $CCuser, $subject, $view, $data){

        Mail::send($view, $data, function($message) use($user, $CCuser, $subject){
            $message->to($user)->cc($CCuser)
                ->subject($subject);
        });

    }
}