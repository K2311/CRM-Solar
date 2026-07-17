<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CampaignMail extends Mailable
{
    use SerializesModels;

    public function __construct(
        public string $mailSubject,
        public string $body,
        public string $senderName = 'Solar CRM',
    ) {}

    public function build()
    {
        return $this->subject($this->mailSubject)
                    ->html($this->body);
    }
}
