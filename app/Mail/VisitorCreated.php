<?php
namespace App\Mail;
use App\Models\Visitor;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;


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
     */
    public function build()
    {
        return $this->view('emails.visitor.created')
            ->with([
                'visitor' => $this->visitor
            ])
            ->subject('Your appointment')
            ->attach($this->attachment_path, array(
                'as' => 'QR-Code.png',
                'mime' => 'image/png'
            ));
    }
}
