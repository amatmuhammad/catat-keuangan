<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;

abstract class Controller
{
    /**
     * Akun yang sedang aktif untuk request ini.
     */
    protected function currentAccount(Request $request): Account
    {
        /** @var Account|null $account */
        $account = $request->attributes->get('currentAccount');

        return $account ?? $request->user()->currentAccount;
    }
}
