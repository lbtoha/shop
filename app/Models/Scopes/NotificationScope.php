<?php

namespace App\Models\Scopes;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class NotificationScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // Check for client authentication
        /**
         * @var \App\Models\User $client
         */
        $client = request()->user('client');
        if ($client) {
            $builder->whereMorphedTo('notifiable', $client);
        }

        $auth = request()->user('admin');
        if ($auth) {
            // Admin can see all notifications related to Admin
            $builder->whereMorphedTo('notifiable', Admin::class);

            return;
        }
    }
}
