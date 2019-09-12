<?php

namespace App\Mail;

use App\Models\Visitor;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MeetingUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public $visitor;
    public $old_date;

    /**
     * Create a new message instance.
     *
     * @param Visitor $visitor
     * @param String $old_date
     */
    public function __construct(Visitor $visitor, String $old_date)
    {
        $this->visitor = $visitor;
        $this->old_date = $old_date;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.meeting.update')
            ->with([
                'visitor' => $this->visitor,
                'old_date' => $this->old_date
            ])
            ->subject('Your appointment was rescheduled');
    }
}
