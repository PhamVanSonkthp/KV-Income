<?php

namespace App\Models;

use App\Traits\DeleteModelTrait;
use App\Traits\StorageImageTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Facades\Excel;
use OwenIt\Auditing\Contracts\Auditable;

class Order extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    use DeleteModelTrait;
    use StorageImageTrait;

    protected $guarded = [];

    protected $casts = [
        'user_id' => 'integer',
        'payment_type_id' => 'integer',
        'branch_id' => 'integer',
        'create_by' => 'integer',
        'service_charge' => 'float',
        'tips' => 'float',
        'deposit' => 'float',
    ];

    // begin

    public function branch(){
        return $this->belongsTo(SystemBranch::class);
    }

    public function products(){
        return $this->hasMany(OrderProduct::class);
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function user_create(){
        return $this->belongsTo(User::class, 'create_by', 'id');
    }

    public function payment(){
        return $this->belongsTo(PaymentType::class, 'payment_type_id', 'id');
    }

    public function orderStatus(){
        return $this->belongsTo(OrderStatus::class);
    }

    public function waitingConfirm(){
        return $this->order_status_id == 1;
    }

    public function updateToShipping(){
        $this->update([
            'order_status_id' => 2
        ]);
    }

    public function paymentType(){
        return $this->belongsTo(PaymentType::class);
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
        $array['payment_type'] = $this->paymentType;
        $array['user'] = $this->user;
        return $array;
    }

    public function avatar($size = "100x100")
    {
       return Helper::getDefaultIcon($this, $size);
    }

    public function image()
    {
        return Helper::image($this);
    }

    public function images()
    {
        return Helper::images($this);
    }

    public function createdBy(){
        return $this->hasOne(User::class,'id','created_by_id');
    }

    public function searchByQuery($request, $queries = [])
    {
        return Helper::searchByQuery($this, $request, $queries);
    }

    public function storeByQuery($request)
    {
        $dataInsert = [
            'code' => $request->code,
            'user_id' => $request->staff,
            'service_charge' => $request->charge + ($request->deposit ?? 0),
            'tips' => $request->tips,
            'payment_type_id' => $request->payment,
            'note' => $request->note,
            'create_by' => auth()->id(),
            'deposit' => $request->deposit,
        ];

        $item = Helper::storeByQuery($this, $request, $dataInsert);

        return $this->findById($item->id);
    }

    public function updateByQuery($request, $id)
    {
        $dataUpdate = [
            'service_charge' => $request->charge ?? 0,
            'tips' => $request->tips ?? 0,
            'payment_type_id' => $request->payment,
            'note' => $request->note,
            'create_by' => auth()->id(),
            'deposit' => $request->deposit ?? 0,
        ];

        if(isset($request->created_at) && !empty($request->created_at)){
            $dataUpdate['created_at'] = $request->created_at;
        }

        $item = Helper::updateByQuery($this, $request, $id, $dataUpdate);
        return $this->findById($item->id);
    }

    public function deleteByQuery($request, $id, $forceDelete = false)
    {
        return Helper::deleteByQuery($this, $request, $id, $forceDelete);
    }

    public function deleteManyByIds($request, $forceDelete = false)
    {
        return Helper::deleteManyByIds($this, $request, $forceDelete);
    }

    public function findById($id){
        $item = $this->find($id);
        return $item;
    }

}
