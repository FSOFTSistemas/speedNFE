<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmailXmlContador extends Mailable
{
    use Queueable, SerializesModels;

    private $sender;
    private $filePath;
    private $period;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($sender, $filePath, $period)
    {
        $this->sender = $sender;
        $this->filePath = $filePath;
        $this->period = $period;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'XMLS de ' . $this->sender->razao . ' - ' . date('m/Y', strtotime($this->period)),
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'mail.xml-contador',
            with: [
                'sender' => $this->sender,
                'period' => $this->period
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [
            Attachment::fromPath($this->filePath)->as('xmls.zip')->withMime('application/xml')
        ];
    }
}
