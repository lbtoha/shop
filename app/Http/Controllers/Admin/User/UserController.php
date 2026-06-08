<?php

namespace App\Http\Controllers\Admin\User;

use App\Enums\NotificationType;
use App\Enums\UserStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Models\User;
use App\Notifications\UserAutoNotification;
use App\Services\ModalIndexQuey;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        adminUserHasPermission(permission: 'read');

        $tab_buttons = [
            [
                'label' => __('All Users'),
                'link' => route('admin.users.index'),
            ],
            [
                'label' => __('Active Users'),
                'count' => User::where('status', UserStatusEnum::ACTIVE->value)->count(),
                'link' => route('admin.users.index', ['type' => 'active']),
            ],
            [
                'label' => __('Inactive Users'),
                'count' => User::where('status', UserStatusEnum::INACTIVE->value)->count(),
                'link' => route('admin.users.index', ['type' => 'inactive']),
            ],
            [
                'label' => __('Banned Users'),
                'count' => User::where('status', UserStatusEnum::BANNED->value)->count(),
                'link' => route('admin.users.index', ['type' => 'banned']),
            ],
            [
                'label' => __('Email Verified'),
                'count' => User::whereNotNull('email_verified_at')->count(),
                'link' => route('admin.users.index', ['type' => 'email_verified']),
            ],
            [
                'label' => __('Phone Verified'),
                'count' => User::whereNotNull('phone_verified_at')->count(),
                'link' => route('admin.users.index', ['type' => 'phone_verified']),
            ],
        ];

        $users = ModalIndexQuey::get(User::query()->when(request('type'), function ($query, $type) {
            if ($type === 'active') {
                $query->where('status', UserStatusEnum::ACTIVE->value);
            } elseif ($type === 'inactive') {
                $query->where('status', UserStatusEnum::INACTIVE->value);
            } elseif ($type === 'email_verified') {
                $query->whereNotNull('email_verified_at');
            } elseif ($type === 'phone_verified') {
                $query->whereNotNull('phone_verified_at');
            } elseif ($type === 'banned') {
                $query->where('status', UserStatusEnum::BANNED->value);
            }
        }));

        $columns = [
            [
                'label' => __('Name'),
                'key' => 'first_name',
                'header_class' => 'lg:w-[350px]',
                'render' => function ($user) {
                    return '<a href="'.route('admin.users.edit', $user->id).'" class="flex max-lg:justify-end items-center gap-3">
                                <img src="'.placeAvatar($user->avatar, $user->full_name).'" width="32" height="32"
                                    class="rounded-full max-md:hidden object-cover w-8 h-8" alt="'.$user->full_name.'" />
                                <div>
                                    <p class="s-text mb-1 font-medium">'.$user->full_name.'</p>
                                    <span class="text-xs">@'.$user->username.'</span>
                                </div>
                            </a>';
                },
            ],
            [
                'label' => __('Email-Mobile'),
                'key' => 'email',
                'header_class' => 'lg:w-[200px]',
                'render' => function ($user) {
                    return '<p class="s-text font-medium">'.protectOnDemo($user->email).'</p>
                            <span class="text-xs">'.protectOnDemo($user->phone).'</span>';
                },
            ],
            [
                'label' => __('Joined At'),
                'key' => 'created_at',
                'is_sortable' => true,
                'render' => function ($user) {
                    return '<p class="s-text font-medium">'.$user->created_at->format('Y-m-d H:i A').'</p>
                            <span class="text-xs">'.Carbon::parse($user->created_at)->diffForHumans().'</span>';
                },
            ],
            [
                'label' => __('Status'),
                'key' => 'status',
                'is_sortable' => true,
                'render' => function ($user) {
                    $status = $user->status;
                    if ($status instanceof UserStatusEnum) {
                        return '<span class="status '.$status->color().' capitalize">'.__($status->label()).'</span>';
                    }

                    return '<span class="status text-gray-400 capitalize">'.__('Unknown').'</span>';
                },
            ],
            [
                'label' => __('Action'),
                'render' => function ($user) {
                    $action_buttons = [
                        [
                            'label' => __('Details'),
                            'icon' => 'ph ph-eye',
                            'type' => 'link',
                            'href' => route('admin.users.edit', $user->id),
                        ],
                        [
                            'label' => __('Delete'),
                            'icon' => 'ph ph-trash',
                            'type' => 'delete',
                            'href' => route('admin.users.destroy', $user->id),
                        ],
                    ];

                    return view('admin.components.table-action', compact('action_buttons'))->render();
                },
            ],
        ];

        return view('admin.pages.user-manage.users', compact('tab_buttons', 'users', 'columns'));
    }

    public function edit(User $user)
    {
        return view('admin.pages.user-manage.user-details', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, User $user)
    {
        try {
            DB::beginTransaction();

            $user->update($request->validated());

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json(['message' => $th->getMessage()], 400);
        }

        return response()->json([
            'message' => __('User updated successfully'),
        ]);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->back()->withSuccess(__('User deleted successfully'));
    }

    public function deactivate(Request $request, User $user)
    {
        try {
            DB::beginTransaction();
            $user->status = $user->status == UserStatusEnum::INACTIVE ? UserStatusEnum::ACTIVE : UserStatusEnum::INACTIVE;
            $user->save();

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with('error', $th->getMessage());
        }

        return redirect()->back()->withSuccess(__('User '.$user->status->value.' successfully'));
    }

    public function banAccount(Request $request, User $user)
    {
        try {
            DB::beginTransaction();
            $user->status = $user->status == UserStatusEnum::BANNED ? UserStatusEnum::ACTIVE : UserStatusEnum::BANNED;
            $user->save();

            $user->notify(new UserAutoNotification(
                $user->status === UserStatusEnum::ACTIVE ? NotificationType::ACCOUNT_BANNED : NotificationType::ACCOUNT_ACTIVE,
            ));

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with('error', $th->getMessage());
        }

        return redirect()->back()->withSuccess(__('User '.$user->status->value.' successfully'));
    }

    public function signInUser(User $user)
    {
        $token = $user->createToken(self::TOKEN_NAME)->plainTextToken;

        return redirect()->away(config('application_info.frontend_url').'?token='.$token);
    }
}
