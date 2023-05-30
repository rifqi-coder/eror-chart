<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ImageStorage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AttendanceController extends Controller
{

    use ImageStorage;
    public function store(Request $request)
    {
        $request->validate([
            'long' => ['required'],
            'lat' => ['required'],
            'address' => ['required'],
            'type' => ['in:in,out', 'required'],
            'photo' => ['required']
        ]);

        $photo = $request->file('photo');
        $attendanceType = $request->type;
        $userAttendanceToday = $request->user()
            ->attendances()
            ->whereDate('created_at', Carbon::today())
            ->first();

        // is presence type equal with 'in' ?
        if ($attendanceType == 'in') {
            // is $userPresenceToday not found?
            if (! $userAttendanceToday) {
                $attendance = $request
                    ->user()
                    ->attendances()
                    ->create(
                        [
                            'status' => false
                        ]
                    );

                $attendance->detail()->create(
                    [
                        'type' => 'in',
                        'long' => $request->long,
                        'lat' => $request->lat,
                        'photo' => $this->uploadImage($photo, $request->user()->name, 'attendance'),
                        'address' => $request->address
                    ]
                );

                return response()->json(
                    [
                        'message' => 'Success'
                    ],
                    Response::HTTP_CREATED
                );
            }

            // else show user has been checked in
            return response()->json(
                [
                    'message' => 'User has been checked in',
                ],
                Response::HTTP_OK
            );
        }

        if ($attendanceType == 'out') {
            if ($userAttendanceToday) {

                if ($userAttendanceToday->status) {
                    return response()->json(
                        [
                            'message' => 'User has been checked out',
                        ],
                        Response::HTTP_OK
                    );
                }

                $userAttendanceToday->update(
                    [
                        'status' => true
                    ]
                );

                $userAttendanceToday->detail()->create(
                    [
                        'type' => 'out',
                        'long' => $request->long,
                        'lat' => $request->lat,
                        'photo' => $this->uploadImage($photo, $request->user()->name, 'attendance'),
                        'address' => $request->address
                    ]
                );

                return response()->json(
                    [
                        'message' => 'Success'
                    ],
                    Response::HTTP_CREATED
                );
            }

            return response()->json(
                [
                    'message' => 'Please do check in first',
                ],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
    }
}
