<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Mail\Verify;

class TestController extends Controller
{
    public function index()
    {
        $details = [
            'title' => 'Interpretasi ID',
            'body' => [
                'Terima kasih telah mendaftar di Interpretasi ID! Kamu harus membuka tautan ini dalam 30 hari sejak pendaftaran untuk mengaktifkan akun.',
                'https://interpretasi.id/account/accept/13696762/5896646f67f0457e5db6efbe6db2c76f',
                'Bersenang-senang, dan jangan ragu untuk menghubungi kami dengan umpan balik Anda.'
            ],
        ];
           
            \Mail::to('dicky.rey97@gmail.com')->send(new Verify($details));
           
            return "sended.";
    }
}
