<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordReset extends Mailable
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
                'Permintaan pengaturan ulang kata sandi untuk akun Interpretasi ID Anda. Ikuti tautan di bawah ini untuk mengatur kata sandi baru:',
                'https://interpretasi.id/account/reset/'.$token,
                'Tautan ini hanya berlaku 15 menit, jika Anda tidak ingin menyetel ulang sandi, abaikan email ini dan tidak ada tindakan yang akan diambil..'
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
        return $this->subject('Atur ulang kata sandi Interpretasi ID')
                    ->view('emails.reset');
    }
}
