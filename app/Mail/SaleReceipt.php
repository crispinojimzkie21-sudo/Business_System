<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SaleReceipt extends Mailable
{
    use Queueable, SerializesModels;

    public $sale;
    public $items;

    /**
     * Create a new message instance.
     */
    public function __construct($sale, $items = [])
    {
        $this->sale = $sale;
        $this->items = is_array($items) ? $items : (json_decode($items, true) ?: []);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Receipt #" . ($this->sale->transaction_id ?? "N/A") . " - Manliquid Store",
            from: new \Illuminate\Mail\Mailables\Address(
                config("mail.from.address", "manliquidstore@gmail.com"),
                config("mail.from.name", "Manliquid Store")
            ),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: "emails.sale-receipt",
            with: [
                "sale" => $this->sale,
                "items" => $this->items,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}