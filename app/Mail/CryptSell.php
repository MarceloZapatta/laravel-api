<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CryptSell extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @var double
     */
    public $cryptQuantity;

    /**
     * @var double
     */
    public $amount;

    /**
     * Create a new message instance.
     *
     * @param  \App\Order  $order
     * @return void
     */
    public function __construct($cryptQuantity, $amount)
    {
        $this->cryptQuantity = $cryptQuantity;
        $this->amount = $amount;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.crypt.sell');
    }
}