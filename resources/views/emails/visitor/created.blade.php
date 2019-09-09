<body>

<h2><i>Your meeting details</i></h2>

<h4>Dear {{ $visitor->name }},</h4>

<p>please find your meeting details below:</p>

<table>
    <tbody>
    <tr>
        <td>Date:</td>
        <td>{{ $visitor->meeting->date }}</td>
    </tr>
    <tr>
        <td>Meeting Host:</td>
        <td>{{ $visitor->meeting->user->name }}</td>
    </tr>
    <tr>
        <td>Meeting Room:</td>
        <td>{{ $visitor->meeting->room->name }}</td>
    </tr>
    </tbody>
</table>

<p>Please bring your attached QR Code with you to check in.</p>

Kind Regards,<br>
Visitor Management System

</body>
