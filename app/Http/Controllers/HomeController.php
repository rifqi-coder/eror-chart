<?php
 
namespace App\Http\Controllers;
 
use App\Attendance;
use App\Charts\AttendanceChart;
use App\User;
use Illuminate\Http\Request;
 
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
 
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $chart = new AttendanceChart();
        $chart->labels(['Today']);
        $chart->dataset('In', 'bar', [Attendance::countAttendance(false)]);
        $chart->dataset('Out', 'bar', [Attendance::countAttendance(true)]);
        $chart->dataset('Total User', 'bar', [User::where('is_admin', false)->count()]);
        return view('home', compact('chart'));
    }
}