<?php

namespace App\Http\Middleware;

use App\Services\AccountService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class ResolveCurrentAccount
{
    public function __construct(private readonly AccountService $accountService) {}

    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user) {
            $account = $this->accountService->ensureCurrentAccount($user);

            $user->setRelation('currentAccount', $account);

            $request->attributes->set('currentAccount', $account);

            View::share('currentAccount', $account);
            View::share('userAccounts', $user->accounts()
                ->withCount('members')
                ->orderBy('name')
                ->get());
        }

        return $next($request);
    }
}
