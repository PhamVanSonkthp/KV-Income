<?php

namespace App\Models;

use App\Traits\DeleteModelTrait;
use App\Traits\StorageImageTrait;
use App\Traits\UserTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use UserTrait;
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;
    use \Awobaz\Compoships\Compoships;
    use DeleteModelTrait;
    use StorageImageTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $guarded = [
//        'is_admin',
    ];

//    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'id' => 'integer',
        'user_status_id' => 'integer',
        'is_admin' => 'integer',
        'gender_id' => 'integer',
        'user_type_id' => 'integer',
        'create_by_user' => 'integer',
    ];

    // begin

    public function userType(){
        return $this->hasOne(UserType::class,'id','user_type_id');
    }

    public function user(){
        return $this->hasOne(User::class, 'id', 'create_by_user');
    }

    public function branch(){
        return $this->belongsTo(SystemBranch::class);
    }

    public function order(){
        return $this->hasMany(Order::class);
    }

    // end

    public function getTableName()
    {
        return Helper::getTableName($this);
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['image_path_avatar'] = $this->avatar();
        $array['path_images'] = $this->images;
        $array['branch'] = $this->branch;
        return $array;
    }

    public function avatar($size = "100x100"){
        $image = $this->image;
        if (!empty($image)){
            return Formatter::getThumbnailImage($image->image_path,$size);
        }

        return config('_my_config.default_avatar');
    }

    public function image(){
        return $this->hasOne(SingleImage::class,'relate_id','id')->where('table' , $this->getTable());
    }

    public function images(){
        return $this->hasMany(Image::class,'relate_id','id')->where('table' , $this->getTable())->orderBy('index');
    }

    public function gender()
    {
        return $this->belongsTo(GenderUser::class);
    }

    public function role()
    {
        return $this->hasOne(Role::class, 'id', 'role_id');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id');
    }

    public function checkPermissionAccess($permissionCheck)
    {
        if (optional(auth()->user())->is_admin == 2) return true;

        $roles = optional(auth()->user())->roles;
        foreach ($roles as $role) {
            $permissions = $role->permissions;
            if ($permissions->contains('key_code', $permissionCheck)) {
                return true;
            }
        }
        return false;
    }

    public function isAdmin(){
        return auth()->check() && optional(auth()->user())->is_admin == 2;
    }


    public function isEmployee(){
        return auth()->check() && optional(auth()->user())->is_admin != 0;
    }

    public function searchByQuery($request, $queries = [])
    {
        return Helper::searchByQuery($this, $request, $queries);
    }

    public function storeByQuery($request)
    {
        $dataInsert = [
            'name' => $request->name,
            'password' => Hash::make($request->password),
            'create_by_user' => auth()->id(),
        ];

        if(!empty($request->code)){
            $dataInsert['code'] = $request->code;
        }

        if(!empty($request->phone)){
            $dataInsert['phone'] = $request->phone;
        }

        if(!empty($request->start)){
            $dataInsert['start'] = Helper::convert_date_to_db($request->start);
        }

        if(!empty($request->birthday)){
            $dataInsert['date_of_birth'] = Helper::convert_date_to_db($request->birthday);
        }

        if(!empty($request->address)){
            $dataInsert['address'] = $request->address;
        }

        if($this->isAdmin() || $this->isEmployee()){
            $dataInsert['role_id'] = json_encode($request->admin_group);
        }

        if ($this->isAdmin()){
//            $dataInsert['role_id'] = $request->role_id ?? 0;
            $dataInsert['is_admin'] = $request->is_admin ?? 0;
        }

        if($request->is_admin == 0){
            $dataInsert['branch_id'] = $request->branch;
            $dataInsert['role_id'] = 0;
        }else{
            $dataInsert['branch_id'] = json_encode($request->branch);
        }


        $item = Helper::storeByQuery($this, $request, $dataInsert);

        if (!empty($request->is_admin && $request->is_admin == 1 && isset($request->admin_group))){
            $item->roles()->attach($request->admin_group);
        }

        return $this->findById($item->id);
    }

    public function updateByQuery($request, $id)
    {
        $item = User::find($id);

        if($request->is_admin == 1){
            $dataUpdate = [
                'name' => $request->name == '' ? $item->name : $request->name,
                'branch_id' => $request->branch == '' ? $item->branch_id : json_encode($request->branch),
                'phone' => $request->phone == '' ? $item->phone : $request->phone,
            ];
        }else{
            $dataUpdate = [
                'id' => $request->id_new == '' ? $item->id : $request->id_new,
                'code' => $request->code == '' ? $item->code : $request->code,
                'name' => $request->name == '' ? $item->name : $request->name,
                'phone' => $request->phone == '' ? $item->phone : $request->phone,
                'start' => Helper::convert_date_to_db_no_time($request->start) == '' ? $item->start : Helper::convert_date_to_db_no_time($request->start),
                'date_of_birth' => Helper::convert_date_to_db_no_time($request->birthday) == '' ? $item->date_of_birth : Helper::convert_date_to_db_no_time($request->birthday),
                'address' => $request->address == '' ? $item->address : $request->address,
                'branch_id' => $request->branch == '' ? $item->branch_id : $request->branch,
            ];
        }
        

        if($this->isAdmin() || $this->isEmployee()){
            $dataUpdate['role_id'] = json_encode($request->admin_group);
            $dataUpdate['is_admin'] = $request->is_admin ?? 0;
        }


        if (!empty($request->password)) {
            $dataUpdate['password'] = Hash::make($request->password);
        }

        if($request->id_new == $id || is_null($request->id_new)){
            $item = Helper::updateByQuery($this, $request, $request->id, $dataUpdate);
        }else{
            User::find($id)->forceDelete();
            $item = Helper::storeByQuery($this, $request, $dataUpdate);
        }


        if ($item->is_admin != 0 && isset($request->admin_group)){
            $item->roles()->sync($request->admin_group);
        }

        return $item;
    }

    public function deleteByQuery($request, $id, $forceDelete = false)
    {
        return Helper::deleteByQuery($this, $request, $id, $forceDelete);
    }

    public function deleteManyByIds($request, $forceDelete = false)
    {
        return Helper::deleteManyByIds($this, $request, $forceDelete);
    }

    public function findById($id)
    {
        $item = $this->find($id);
//        $item->gender;
//        $item->role;
        return $item;
    }
}
