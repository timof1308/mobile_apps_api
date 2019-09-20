<body>

<h3><i>Your meeting details</i></h3>

<h4>Hi {{ $meeting->user->name }},</h4>

<p>please find your meeting details below:</p>

Date: {{ $date_start }} to {{ $date_end }}<br>
Meeting Room: {{ $meeting->room->name }}<br>

<hr>

Expected visitors:
<ol>
    @foreach($meeting->visitors as $visitor)
        <li>{{$visitor->name}} ( {{$visitor->company->name}} ): <a href="mailto:{{$visitor->email}}">{{$visitor->email}}</a>; <a href="tel:{{$visitor->tel}}">{{$visitor->tel}}</a></li>
    @endforeach
</ol>

Kind Regards,<br>
Visitor Management System

</body>
