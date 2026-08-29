<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminPermission
{
    public function handle(Request $request, Closure $next, ?string $permission=null)
    {
        $user=$request->user();
        if (!$user || !$user->roles()->whereIn('code',[
            'SUPER_ADMIN','CONTENT_ADMIN','PRODUCT_ADMIN','ORDER_ADMIN','FULFILMENT_ADMIN',
            'CUSTOMER_SUPPORT','MARKETING_ADMIN','RESET_ADMIN','ANALYST'
        ])->exists()) abort(403);

        if ($permission && !$user->hasRole('SUPER_ADMIN') && !$user->hasPermission($permission)) abort(403);
        return $next($request);
    }
}
