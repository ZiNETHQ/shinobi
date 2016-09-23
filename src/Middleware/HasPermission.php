<?php

namespace ZiNETHQ\SparkRoles\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use ZiNETHQ\SparkRoles\Models\Role;

class HasPermission
{
    /**
     * @var Illuminate\Contracts\Auth\Guard
     */
    protected $auth;

    /**
     * Create a new HasPermission instance.
     *
     * @param Guard $auth
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Run the request filter.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $closure
     * @param array|string             $permissions
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $permissions)
    {
        if ($this->auth->check()) {

            $team = $this->auth->user()->currentTeam;

            if(!$team) {
                return false;
            }

            if (! $team->can($permissions)  !$team->can($permissions)) {
                if ($request->ajax()) {
                    return response('Unauthorized.', 403);
                }

                abort(403, 'Unauthorized action.');
            }
        } else {
            $guest = Role::whereSlug('guest')->first();

            if ($guest) {
                if (! $guest->can($permissions)) {
                    if ($request->ajax()) {
                        return response('Unauthorized.', 403);
                    }

                    abort(403, 'Unauthorized action.');
                }
            }
        }


        return $next($request);
    }
}
