<?php

namespace App\Mail;

use App\Models\Meeting;
use App\Models\Visitor;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;


class VisitorCheckedIn extends Mailable
{
    use Queueable, SerializesModels;

    public $visitor;

    /**
     * Create a new message instance.
     *
     * @param Visitor $visitor
     */
    public function __construct(Visitor $visitor)
    {
        $this->visitor = $visitor;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.visitor.checkin')
            ->with([
                'visitor' => $this->visitor
            ])
            ->subject('Visitor has checked in');
    }
}
