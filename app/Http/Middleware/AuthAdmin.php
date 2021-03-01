<?php
namespace App\Http\Middleware;

use App\Exceptions\ErrorConstant;
use App\Library\Constant\Common;
use App\Models\Admin;
use Closure;
use Illuminate\Support\Facades\Auth;

/**
 * 驗證管理員狀態中間件
 * Class AuthAdmin
 * @package App\Http\Middleware
 */
class AuthAdmin
{
    /**
     * @param $request
     * @param Closure $next
     * @return mixed
     * @throws \Exception
     */
    public function handle($request, Closure $next)
    {
        $admin = Admin::query()->find(Auth::id(), ['status', 'deleted']);
        if (!is_null($admin)) {
            if ($admin->getAttribute('status') != Common::STATUS_NORMAL || $admin->getAttribute('deleted') != Common::NO_DELETE) {
                throw new \Exception('The administrator has been removed or disabled', ErrorConstant::ADMIN_ERROR);
            }
        }

        return $next($request);
    }
}