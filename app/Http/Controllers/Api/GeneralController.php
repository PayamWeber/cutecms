<?php

namespace App\Http\Controllers\Api;

use App\City;
use App\Helpers\StatusCodes;
use App\Http\Controllers\Controller;
use App\Province;
use App\User;
use App\UserMeta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\Appointment\Entities\Appointment;
use Modules\Appointment\Entities\AppointmentUser;

class GeneralController extends Controller
{
	public function policies( Request $request )
	{
		return _api_response( true, [
			'policies' => get_option('policies'),
		] );
	}
}
