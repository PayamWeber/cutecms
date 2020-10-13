<?php

namespace App\Http\Controllers\Api\User;

use App\City;
use App\Helpers\StatusCodes;
use App\Http\Controllers\Controller;
use App\Province;
use App\User;
use App\UserMeta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\Appointment\Entities\Appointment;
use Modules\Appointment\Entities\AppointmentSpecialty;
use Modules\Appointment\Entities\AppointmentUser;
use Modules\Appointment\Transformers\Api\v1\DoctorAppointmentResourceCollection;
use Modules\Appointment\Http\Controllers\Api\Online\Appointment\AppointmentUserController as AUCAPI;

class UserController extends Controller
{
//    public function updateProfile( Request $request )
//    {
//        $request->merge( [
//            'citizen_code' => convertNumber( $request->get( 'citizen_code', '' ) ),
//        ] );
//        $request->validate( [
//            'first_name' => 'required',
//            'last_name' => 'required',
//            //			'citizen_code' => 'sometimes|numeric',
//            'province' => 'required|exists:provinces,name',
//            'city' => 'required|exists:cities,name',
//        ] );
//
//        $user       = \Auth::user();
//        $first_name = User::safeTitle( $request->first_name );
//        $last_name  = User::safeTitle( $request->last_name );
//
//        if ( ! $first_name || ! $last_name ) {
//            return _api_response( false, [ 'نام کاربری وارد شده معتبر نمیباشد' ] );
//        }
//
//        //		if ( UserMeta::whereNotIn( 'user_id', [ $user->id ] )->whereMetaKey( UserMeta::CITIZEN_CODE )->whereMetaValue( $request->citizen_code )->exists() ){
//        //			return _api_response( false, ['کد ملی وارد شده توسط کاربر دیگری ثبت شده است'], StatusCodes::USER_CITIZEN_CODE_ALREADY_EXISTS );
//        //		}
//
//        try {
//
//            return _api_response( true );
//        } catch ( \Throwable $exception ) {
//            return _api_response( false, [ $exception->getMessage() ], 500 );
//        }
//    }

    public function dashboardInfo( Request $request )
    {
        $user = \Auth::user();

        if ( ! $profile_info = $user->hasCompletedProfile() ) {
            return _api_response( false, [ 'پروفایل شما تکمیل نمیباشد' ], StatusCodes::USER_PROFILE_NOT_COMPLETE );
        }

        if ( $user->isDoctor() ) {
            if ( $user->isUnconfirmedDoctor() ) {
                return _api_response( false, [ 'درخواست شما هنوز تایید نشده است' ] );
            } else {
                $allAppointments        = $user->doctorAppointments;
                $nearest_appointments   = $allAppointments->where( 'created_at', ">=", Carbon::now() )->where( 'status', AppointmentUser::STATUS_OK )->where( 'visit', AppointmentUser::VISIT_PENDING )->values()->take( 3 )->sortBy( 'created_at' );
                $todayAppointmentsCount = $totalIncome = $user->doctorAppointments()->whereDate( 'created_at', Carbon::today() )->get()->count();
                $totalIncome            = $user->doctorTransactions->sum( "price" );
                $todayIncome            = $user->doctorTransactions()->whereDate( 'created_at', Carbon::today() )->get()->sum( "price" );
                $walletCharge           = $user->walletCharge;
                $todayDate              = jdate( "l d F Y" );
                return _api_response( true, [
                    'first_name' => $profile_info['first_name'],
                    'last_name' => $profile_info['last_name'],
                    'specialty' => $profile_info['specialty'],
                    'app_id' => $user->api_id,
                    'avatar' => $user->avatar,
                    'today_date' => $todayDate,
                    'today_appointments_count' => $todayAppointmentsCount,
                    'total_income' => $totalIncome,
                    'today_income' => $todayIncome,
                    'wallet_charge' => intval( $walletCharge ),
                    'nearest_appointments' => new DoctorAppointmentResourceCollection( $nearest_appointments ),
                ] );
            }
        } else {
            /** @var AppointmentUser $active_appointment_user */
            $active_appointment_user = $user->active_appointment_user_online;

            if ( ! $active_appointment_user || ! in_array( $active_appointment_user->type, Appointment::$VISIT_TYPE_ONLINE ) ) {
                $active_appointment_user = false;
            }

            if ( $active_appointment_user ) {
                $appoint_date = jalali_to_gregorian( $active_appointment_user->year, $active_appointment_user->month, $active_appointment_user->day );
                $appoint_time = explode( ':', $active_appointment_user->time_from );
                /** @var Carbon $appoint_date */
                $appoint_date        = Carbon::create( $appoint_date[ 0 ], $appoint_date[ 1 ], $appoint_date[ 2 ], $appoint_time[ 0 ], $appoint_time[ 1 ], $appoint_time[ 2 ] );
                $whole_minutes_until = now()->minutesUntil( $appoint_date )->count();
                $hours_until         = intval( $whole_minutes_until / 60 );
                $minutes_until       = $whole_minutes_until % 60;
            }

            $callable_appointment_user_online = $user->callable_appointment_user_online;

            return _api_response( true, [
                'first_name' => $profile_info[ 'first_name' ],
                'last_name' => $profile_info[ 'last_name' ],
                'avatar' => $user->avatar,
                'province' => optional( Province::find( $profile_info[ 'province' ] ) )->name,
                'city' => optional( City::find( $profile_info[ 'city' ] ) )->name,
                'appointment_users_count' => $user->appointmentUsers()->whereIn( 'type', Appointment::$VISIT_TYPE_ONLINE )->count(),
                'appointment_requests_count' => $user->appointmentRequests()->count(),
                'active_appointment_user' => boolval( $active_appointment_user ),
                'active_appointment_user_id' => $active_appointment_user ? $active_appointment_user->id : 0,
                'active_appointment_user_info' => $active_appointment_user ? $this->generateAppointmentUserData( $active_appointment_user ) : 0,
                'doctor_entered_room' => $callable_appointment_user_online ? $callable_appointment_user_online->room_entered : false,
                'hours_until' => $hours_until ?? null,
                'minutes_until' => $minutes_until ?? null,
            ] );
        }
    }

}
