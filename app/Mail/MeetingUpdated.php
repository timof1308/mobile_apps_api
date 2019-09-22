<?php

namespace App\Mail;

use App\Models\Visitor;
use DateInterval;
use DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MeetingUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public $visitor;
    public $old_date;
    public $old_duration;

    /**
     * Create a new message instance.
     *
     * @param Visitor $visitor
     * @param String $old_date
     * @param int $old_duration
     */
    public function __construct(Visitor $visitor, String $old_date, $old_duration)
    {
        $this->visitor = $visitor;
        $this->old_date = $old_date;
        $this->old_duration = $old_duration;
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
        $dt_old_start = new DateTime($this->old_date);
        $dt_old_end = new DateTime($this->old_date);
        $dt_start = new DateTime($this->visitor->meeting->date);
        $dt_end = new DateTime($this->visitor->meeting->date);
        // get end date time for meeting by adding meeting duration (minutes)
        $dt_old_end->add(new DateInterval('PT' . $this->old_duration . 'M'));
        $dt_end->add(new DateInterval('PT' . $this->visitor->meeting->duration . 'M'));

        // format date time to string
        $s_old_start = $dt_old_start->format('Y-m-d H:i');
        $s_start = $dt_start->format('Y-m-d H:i');
        $s_old_end = $dt_old_end->format('H:i');
        $s_end = $dt_end->format('H:i');

        return $this->view('emails.meeting.update')
            ->with([
                'visitor' => $this->visitor,
                'old_date_start' => $s_old_start,
                'old_date_end' => $s_old_end,
                'date_start' => $s_start,
                'date_end' => $s_end
            ])
            ->attach(base_path("storage/files/meeting_" . $this->visitor->meeting->id . ".ics"), array(
                'as' => 'meeting.ics',
                'mime' => 'text/calendar'
            ))
            ->subject('Your appointment was rescheduled');
    }
}
