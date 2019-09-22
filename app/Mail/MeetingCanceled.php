<?php

namespace App\Mail;

use App\Models\Visitor;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;


class MeetingCanceled extends Mailable
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
        return $this->view('emails.meeting.cancel')
            ->with([
                'visitor' => $this->visitor
            ])
            ->attach(base_path("storage/files/meeting_" . $this->visitor->meeting->id . ".ics"), array(
                'as' => 'meeting.ics',
                'mime' => 'text/calendar'
            ))
            ->subject('Your appointment was canceled');
    }
}
