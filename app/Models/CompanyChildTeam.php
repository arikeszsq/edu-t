<?php

namespace App\Models;


use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CompanyChildTeam extends Model
{

    protected $table = 'company_child_team';

    public function teacher()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function companychild()
    {
        return $this->hasOne(CompanyChild::class, 'id', 'child_id');
    }

    public static function getTeamAllUserIds($team_id)
    {
        return CompanyChildTeam::query()
            ->where('child_id', $team_id)
            ->groupBy('user_id')
            ->pluck('user_id')->toArray();
    }

    public static function getTeamInviteNum($activity_id, $team_id)
    {
        $team_user_ids = self::getTeamAllUserIds($team_id);
        return UserActivityInvite::query()
            ->where('activity_id', $activity_id)
            ->whereIn('parent_user_id', $team_user_ids)
            ->orWhereIn('A_user_id', $team_user_ids)
            ->count();
    }

    public static function getTeamSuccessNum($activity_id, $team_id)
    {
        $team_user_ids = self::getTeamAllUserIds($team_id);
        return UserActivityInvite::query()
            ->where('activity_id', $activity_id)
            ->where('has_pay', 1)
            ->whereIn('parent_user_id', $team_user_ids)
            ->orWhereIn('A_user_id', $team_user_ids)
            ->count();
    }


    public static function getUserInviteNum($activity_id, $user_id)
    {
        return UserActivityInvite::query()
            ->where('activity_id', $activity_id)
            ->where('parent_user_id', $user_id)
            ->orWhere('A_user_id', $user_id)
            ->count();
    }

    public static function getUserSuccessNum($activity_id, $user_id)
    {
        return UserActivityInvite::query()
            ->where('activity_id', $activity_id)
            ->where('has_pay', 1)
            ->where('parent_user_id', $user_id)
            ->orWhere('A_user_id', $user_id)
            ->count();
    }


}
