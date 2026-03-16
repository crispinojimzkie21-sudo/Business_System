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
    public $salesReport;

    /**
     * Create a new message instance.
     */
    public function __construct($sale, $items, $salesReport = null)
    {
        $this->sale = $sale;
        // Ensure items is always an array
        $this->items = is_array($items) ? $items : json_decode($items, true);
        $this->salesReport = $salesReport;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Sale Receipt - ' . config('app.name'),
            from: new \Illuminate\Mail\Mailables\Address(
                config('mail.from.address', 'noreply@yourdomain.com'),
                config('mail.from.name', 'Business System')
            ),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.sale-receipt',
            with: [
                'sale' => $this->sale,
                'items' => $this->items,
                'salesReport' => $this->salesReport,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}

