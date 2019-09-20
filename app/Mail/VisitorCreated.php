<?php

namespace App\Mail;

use App\Models\Visitor;
use DateInterval;
use DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;


class VisitorCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $visitor;
    public $attachment_path;

    /**
     * Create a new message instance.
     *
     * @param Visitor $visitor
     * @param String $attachment_path
     */
    public function __construct(Visitor $visitor, String $attachment_path)
    {
        $this->visitor = $visitor;
        $this->attachment_path = $attachment_path;
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
        $dt_start = new DateTime($this->visitor->meeting->date);
        $dt_end = new DateTime($this->visitor->meeting->date);
        // get end date time for meeting by adding meeting duration (minutes)
        $dt_end->add(new DateInterval('PT' . $this->visitor->meeting->duration . 'M'));

        // format date time to string
        $s_start = $dt_start->format('Y-m-d H:i');
        $s_end = $dt_end->format('H:i');

        return $this->view('emails.visitor.created')
            ->with([
                'visitor' => $this->visitor,
                'date_start' => $s_start,
                'date_end' => $s_end
            ])
            ->subject('Your appointment')
            ->attach($this->attachment_path, array(
                'as' => 'QR-Code.png',
                'mime' => 'image/png'
            ));
    }
}
