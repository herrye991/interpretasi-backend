<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Verify extends Mailable
{
    use Queueable, SerializesModels;

    public $details;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($token)
    {
        $this->details = [
            'title' => 'Interpretasi ID',
            'body' => [
                'Terima kasih telah mendaftar di Interpretasi ID! Kamu harus membuka tautan ini dalam 1 hari sejak pendaftaran untuk mengaktifkan akun.',
                'https://interpretasi.id/account/accept/'.$token,
                'Bersenang-senang, dan jangan ragu untuk menghubungi kami dengan umpan balik Anda.'
            ],
        ];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Konfirmasi akun Interpretasi ID')
                    ->view('emails.verify');
    }
}
