<?php

namespace App\Mail;

use App\Models\Meeting;
use DateInterval;
use DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;


class MeetingBundleCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $meeting;

    /**
     * Create a new message instance.
     *
     * @param Meeting $meeting
     */
    public function __construct(Meeting $meeting)
    {
        $this->meeting = $meeting;
    }

    /**
     * Build the message.
     *
     * @return $this
     * @throws \Exception
     */
    public function build()
    {
        // PREPARE MEETING DATE TIMES
        // date time for meeting date
        $dt_start = new DateTime($this->meeting->date);
        $dt_end = new DateTime($this->meeting->date);
        // get end date time for meeting by adding meeting duration (minutes)
        $dt_end->add(new DateInterval('PT' . $this->meeting->duration . 'M'));

        // format date time to string
        $s_start = $dt_start->format('Y-m-d H:i');
        $s_end = $dt_end->format('H:i');

        return $this->view('emails.meeting.bundle')
            ->with([
                'meeting' => $this->meeting,
                'date_start' => $s_start,
                'date_end' => $s_end
            ])
            ->subject('Your meeting information');
    }
}
