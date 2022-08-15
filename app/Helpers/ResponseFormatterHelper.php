<?php

namespace App\Helpers;

class ResponseFormatterHelper {

    public static function generateResponseOnlyBook(...$datas)
    {
        $response = [];
        foreach($datas as $data)
        {
            foreach($data as $transaction)
            {
                if($transaction){
                    array_push($response, ['book_id' => $transaction->book_id, 'book_title' => $transaction->book->title]);
                }
            }
        }

        return $response;
    }

}
