<body>

<h3><i>Meeting was updated</i></h3>

<h4>Dear {{ $visitor->name }},</h4>

<p>We would like to inform you that the scheduled meeting for {{ $old_date }} has been rescheduled.</p>

<p>The new time will be <b><u><span style="color: red">{{ $visitor->meeting->date }}</span></u></b></p>

Apologies for the inconvenience. <br>
Kind Regards,<br>
Visitor Management System

</body>
