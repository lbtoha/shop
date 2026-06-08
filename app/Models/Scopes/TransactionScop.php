<?php

namespace App\Models\Scopes;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class TransactionScop implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $is_admin = Auth::guard('admin')->check();

        if (! $is_admin) {
            $user = Auth::guard('client')->user();

            if ($user) {
                $builder->where('holder_type', User::class)
                    ->where('holder_id', $user->id);
            }
        }
    }
}
