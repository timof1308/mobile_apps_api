<?php


namespace App\Http\Controllers;


use App\Models\Visitor;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get current dashboard numbers
     *
     * @return JsonResponse
     */
    public function getLiveData()
    {
        // get visitor count who have been checked in but not checked out
        $active = Visitor::whereHas('meeting', function (Builder $query) {
            $query->whereDate('date', Carbon::today());
        })
            ->whereNotNull('check_in') // checked in
            ->whereNull('check_out') // NOT checked out
            ->count();

        // get visitor count who have not been check in and out
        $planned = Visitor::whereHas('meeting', function (Builder $query) {
            $query->whereDate('date', Carbon::today());
        })
            ->whereNull('check_in') // NOT checked in
            ->whereNull('check_out') // NOT checked out
            ->count();

        // count excepted visitor's companies
        $companies = Visitor::whereHas('meeting', function (Builder $query) {
            $query->whereDate('date', Carbon::today());
        })
            ->distinct('company_id')
            ->count('company_id');

        // get total visitor count for today's meetings
        $total = Visitor::whereHas('meeting', function (Builder $query) {
            $query->whereDate('date', Carbon::today());
        })->count();

        // prepare response array
        $response = array(
            'active' => $active,
            'planned' => $planned,
            'total' => $total,
            'companies' => $companies
        );
        return response()->json($response, 200);
    }

    /**
     * Get visitor count for week of selected date
     *
     * @param Request $request
     * @param $date
     * @return JsonResponse
     */
    public function getWeekData(Request $request, $date)
    {
        $date_parsed = Carbon::create($date);
        $week_start = Carbon::create($date)->startOfWeek();
        $week_end = Carbon::create($date)->endOfWeek();

        // prepare array to return
        $response = array();

        $day = $week_start;
        for ($i = 0; $i < 7; $i++) { // 1 week / 7 days
            // apply format
            $current = (Carbon::parse($day))->locale('de')->isoFormat('YYYY-MM-DD');

            // get total visitor count for current day's meetings
            $total = Visitor::whereHas('meeting', function (Builder $query) use ($current) {
                $query->whereDate('date', Carbon::parse($current));
            })->count();

            // add visitor count to date in array
            $response[$current] = $total;
            // add one day
            $day = (Carbon::parse($day))->addDay();
        }

        return response()->json($response, 200);
    }

    /**
     * Get visitors assigned to date the meeting has been planned
     *
     * @param Request $request
     * @param $date String to filter for date
     * @return JsonResponse
     */
    public function getVisitorData(Request $request, $date)
    {
        $response = Visitor::whereHas('meeting', function (Builder $query) use ($date) {
            $query->whereDate('date', Carbon::parse($date));
        })
            ->with(array('meeting', 'meeting.room', 'meeting.user', 'company'))
            ->get();

        return response()->json($response, 200);
    }

    /**
     * Get visitor count for each company
     *
     * @return JsonResponse
     */
    public function getCompanyData()
    {
        // query to get visitor count for each company
        $response = DB::select("SELECT c.name AS name, count(c.id)
FROM companies c
         JOIN visitors v on c.id = v.company_id
         JOIN meetings m on v.meeting_id = m.id
GROUP BY c.id
ORDER BY 1;");
        return response()->json($response, 200);
    }
}
