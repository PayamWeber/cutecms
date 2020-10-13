<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Capability;

class CheckCapability
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  \Closure                 $next
	 *
	 * @return mixed
	 */
	public function handle( $request, Closure $next )
	{
		$route                      = $request->route()->action[ 'as' ] ?? '';
		$capability                 = Capability::where( 'route', 'LIKE', "%\"$route\"%" )
			->orWhere( 'route', $route )
			->first();
		$capability                 = $capability[ 'name' ] ?? '';
		$GLOBALS[ 'current_route' ] = $route;

		if ( $capability && ! is_user_can( $capability ) )
			return response( 'شما اجازه دسترسی به این صفحه را ندارید' );

		return $next( $request );
	}
}
