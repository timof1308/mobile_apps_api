<?php

class iCalendar
{
    static private $content = 'BEGIN:VCALENDAR
VERSION:2.0
METHOD:§§METHOD§§
PRODID:-//dhbw//mobileapps//vms//EN
BEGIN:VEVENT
UID:§§ID§§-vms.wwi17sca@gmail.com
DTSTAMP:§§TIMESTAMP§§
ORGANIZER:MAILTO:§§HOST_MAIL§§
DTSTART;TZID=CET:§§START§§
DTEND;TZID=CET:§§END§§
LOCATION:DHBW Mannheim, Germany
SUMMARY:§§SUMMARY§§
DESCRIPTION:§§DESCRIPTION§§
SEQUENCE:§§SEQUENCE§§
END:VEVENT
END:VCALENDAR';

    /**
     * Create new calendar entry for meeting
     * Method = REQUEST
     * SEQUENCE = 0
     *
     * @param int $id Meeting Id
     * @param String $host Host's name
     * @param String $host_mail Host's email address
     * @param String $summary Meeting summary / heading
     * @param String $description Meeting description / detailed information
     * @param DateTime $start Meeting start
     * @param DateTime $end Meeting End
     * @return string
     * @throws Exception
     */
    public static function new_calender_entry(int $id, String $host, String $host_mail, String $summary, String $description, DateTime $start, DateTime $end)
    {
        return self::parse_calender_entry($id, $host, $host_mail, $summary, $description, $start, $end, "REQUEST", 0);
    }

    /**
     * Update existing calendar entry for meeting
     * method = REQUEST
     * sequence = 1
     *
     * @param int $id Meeting Id
     * @param String $host Host's name
     * @param String $host_mail Host's email address
     * @param String $summary Meeting summary / heading
     * @param String $description Meeting description / detailed information
     * @param DateTime $start Meeting start
     * @param DateTime $end Meeting End
     * @return string
     * @throws Exception
     */
    public static function update_calender_entry(int $id, String $host, String $host_mail, String $summary, String $description, DateTime $start, DateTime $end)
    {
        return self::parse_calender_entry($id, $host, $host_mail, $summary, $description, $start, $end, "REQUEST", 1);
    }

    /**
     * Cancel existing calendar entry
     * method = CANCEL
     * sequence = 1
     *
     * @param int $id Meeting Id
     * @param String $host Host's name
     * @param String $host_mail Host's email address
     * @param String $summary Meeting summary / heading
     * @param String $description Meeting description / detailed information
     * @param DateTime $start Meeting start
     * @param DateTime $end Meeting End
     * @return string
     * @throws Exception
     */
    public static function cancel_calender_entry(int $id, String $host, String $host_mail, String $summary, String $description, DateTime $start, DateTime $end)
    {
        return self::parse_calender_entry($id, $host, $host_mail, $summary, $description, $start, $end, "CANCEL\nSTATUS:CANCELLED", 1);
    }

    /**
     * Get content by parsing variables into text
     *
     * @param int $id Meeting Id
     * @param String $host Host's name
     * @param String $host_mail Host's email address
     * @param String $summary Meeting summary / heading
     * @param String $description Meeting description / detailed information
     * @param DateTime $start Meeting start
     * @param DateTime $end Meeting End
     * @param String $method Calendar integration
     * @param int $sequence Sequence state
     * @return string
     * @throws Exception
     */
    private static function parse_calender_entry(int $id, String $host, String $host_mail, String $summary, String $description, DateTime $start, DateTime $end, String $method, int $sequence)
    {
        $content = iCalendar::$content;
        // replace parmeters
        $content = preg_replace('/\.*§§METHOD§§\.*/m', $method, $content);
        $content = preg_replace('/\.*§§ID§§\.*/m', $id, $content);
        $content = preg_replace('/\.*§§SUMMARY§§\.*/m', $summary, $content);
        $content = preg_replace('/\.*§§DESCRIPTION§§\.*/m', $description, $content);
        $content = preg_replace('/\.*§§START§§\.*/m', $start->format('Ymd\THis'), $content);
        $content = preg_replace('/\.*§§END§§\.*/m', $end->format('Ymd\THis'), $content);
        $content = preg_replace('/\.*§§HOST§§\.*/m', $host, $content);
        $content = preg_replace('/\.*§§HOST_MAIL§§\.*/m', $host_mail, $content);
        $content = preg_replace('/\.*§§SEQUENCE§§\.*/m', $sequence, $content);

        // prepare current timestamp
        $now_formatted = (new DateTime())->format('Ymd\THis');
        // replace parameter
        $content = preg_replace('/\.*§§TIMESTAMP§§\.*/m', $now_formatted, $content);

        $fp = fopen(base_path("storage/files/meeting_" . $id . ".ics"), 'w');
        fwrite($fp, $content);
        fclose($fp);

        return base_path("storage/files/meeting_" . $id . ".ics");
    }
}


