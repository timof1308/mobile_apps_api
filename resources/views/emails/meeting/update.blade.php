<body>

<h3><i>Meeting was updated</i></h3>

<h4>Dear {{ $visitor->name }},</h4>

<p>We would like to inform you that the scheduled meeting for {{ $old_date_start }} has been rescheduled.</p>

<p>Old: <strike>From {{ $old_date_start }} to {{ $old_date_end }}</strike></p>
<p><span style="color: red">New</span>: <b><u>From {{ $old_date_start }} to {{ $old_date_end }}</u></b></p>

Apologies for the inconvenience. <br>
Kind Regards,<br>
Visitor Management System

</body>
