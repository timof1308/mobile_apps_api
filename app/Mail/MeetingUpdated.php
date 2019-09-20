<?php

namespace App\Mail;

use App\Models\Visitor;
use DateInterval;
use DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use phpDocumentor\Reflection\Types\Integer;

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
     * @param Integer $old_duration
     */
    public function __construct(Visitor $visitor, String $old_date, Integer $old_duration)
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
        $dt_start = new DateTime($this->visitor->meeting->date);
        // get end date time for meeting by adding meeting duration
        $dt_old_end = $dt_start->add(new DateInterval('PT' . $this->old_duration . 'M'));
        $dt_end = $dt_start->add(new DateInterval('PT' . $this->visitor->meeting->duration . 'M'));

        // format date time to string
        $s_old_start = $dt_old_start->format('Y-m-d H:i');
        $s_start = $dt_start->format('Y-m-d H:i');
        $s_old_end = $dt_old_end->format('Y-m-d H:i');
        $s_end = $dt_end->format('Y-m-d H:i');

        return $this->view('emails.meeting.update')
            ->with([
                'visitor' => $this->visitor,
                'old_date_start' => $s_old_start,
                'old_date_end' => $s_old_end,
                'date_start' => $s_start,
                'date_end' => $s_end
            ])
            ->subject('Your appointment was rescheduled');
    }
}
