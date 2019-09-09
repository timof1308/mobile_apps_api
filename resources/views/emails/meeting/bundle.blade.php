<body>

<h2><i>Your meeting details</i></h2>

<h4>Hi {{ $meeting->user->name }},</h4>

<p>please find your meeting details below:</p>

Date: {{ $meeting->date }}<br>
Meeting Room: {{ $meeting->room->name }}<br>

<hr>

Expected visitors:
@foreach($meeting->visitors as $visitor)
    <p>{{$visitor->name}} ( {{$visitor->company->name}} ): {{$visitor->email}}</p>
@endforeach

Kind Regards,<br>
Visitor Management System

</body>
