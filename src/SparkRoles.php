<?php
namespace ZiNETHQ\SparkRoles;

use ZiNETHQ\SparkRoles\Models\Role;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Collection;
use Laravel\Spark\Spark;

class SparkRoles
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
     * Checks if model has the given permissions.
     *
     * @param array|string $permissions
     *
     * @return bool
     */
    public function can($permissions)
    {
        if ($this->auth->check()) {
            return $this->getModels()->contains(function ($value, $key) {
                return $value->can($permissions);
            });
        } else {
            $guest = Role::whereSlug('guest')->first();

            if ($guest) {
                return $guest->can($permissions);
            }
        }

        return false;
    }

    /**
     * Checks if model has at least one of the given permissions.
     *
     * @param array $permissions
     *
     * @return bool
     */
    public function canAtLeast($permissions)
    {
        if ($this->auth->check()) {
            return $this->getModels()->contains(function ($value, $key) {
                return $value->canAtLeast($permissions);
            });
        } else {
            $guest = Role::whereSlug('guest')->first();

            if ($guest) {
                return $guest->canAtLeast($permissions);
            }
        }

        return false;
    }

    /**
	 * Checks if model is assigned the given role.
	 *
	 * @param  string $slug
	 * @return bool
	 */
    public function is($role)
    {
        if ($this->auth->check()) {
            return $this->getModels()->contains(function ($value, $key) {
                return $value->isRole($role);
            });
        } else {
            if ($role === 'guest') {
                return true;
            }
        }

        return false;
    }

    private function getModels() {
        $userTraits = class_uses(Spark::userModel());
        $teamTraits = class_uses(Spark::teamModel());
        $currentTeam = $this->auth->user()->currentTeam;
        $models = [];

        if($userTraits && in_array('CanHaveRoles', $userTraits)) {
            $models[] = $this->auth->user();
        }

        if($teamTraits && in_array('CanHaveRoles', $teamTraits) && $currentTeam) {
            $models[] = $currentTeam;
        }

        return collect($models);
    }
}
