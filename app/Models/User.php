<?php

namespace App\Models;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable,HasRoles;
    protected $table= 'users';
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $guarded=[];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function post()  {
        return $this->hasOne(Post::class);
        
    }

    // public function roles()  {
    //     return $this->belongsToMany(Role::class);
        
    // }

    public function UserOnline()
    {
        return cache()->has('user-is-online-' . $this->id);
    }

    public static function getpermissionGroups(){
        $permission_groups = DB::table('permissions')->select('group_name')->groupBy('group_name')->get();
        return $permission_groups;
    }//End method
    
    
    public static function getpermissionByGroupName($group_name){

        $permissions = DB::table('permissions')
                        ->select('name','id')
                        ->where('group_name',$group_name)
                        ->get();

                        return $permissions;
    } // End Method

    // public static function roleHasPermissions($role,$permissions){

    //     $hasPermission =  true;
    //     foreach ($permissions as  $permission) {
    //         if (!$role->hasPermissionTo($permission->name)) {
    //             $hasPermission = false;
    //         }
    //         return $hasPermission;
    //     }

    // }// End Method 

    public static function roleHasPermissions($role, $permissions)
{
    foreach ($permissions as $permission) {
        if (!$role->hasPermissionTo($permission->name)) {
            return false;
        }
    }
    return true;
}// end method 

}



// namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Foundation\Auth\User as Authenticatable;
// use Illuminate\Notifications\Notifiable;

// class User extends Authenticatable
// {
//     use HasFactory, Notifiable;

//     protected $fillable = ['name', 'email', 'password'];

//     protected $hidden = ['password', 'remember_token'];

//     protected function casts(): array
//     {
//         return [
//             'email_verified_at' => 'datetime',
//             'password' => 'hashed',
//         ];
//     }

//     public function posts()
//     {
//         return $this->hasMany(Post::class);
//     }

//     public function roles()
//     {
//         return $this->belongsToMany(Role::class);
//     }
// }
