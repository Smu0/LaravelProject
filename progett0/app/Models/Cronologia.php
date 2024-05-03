<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cronologia extends Model
{
    public function getId(){
        return $this->attributes["id"];
    }
    public function setId($id){
        $this->attributes['id'] = $id;
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
    public function getUser(){
        return $this->user;
    }
    public function setUser($user){
        $this->user = $user;
    }
    public function setUserId($user_id){
        $this->attributes['user_id'] = $user_id;
    }

    public function product(){
        return $this->belongsTo(Product::class);
    }
    public function getProduct(){
        return $this->product;
    }
    public function setProduct($product){
        $this->product = $product;
    }
    public function setProductId($product_id){
        $this->attributes['product_id'] = $product_id;
    }

    public function getUpdateAt(){
        return $this->attributes['updated_at'];
    }
    public function setUpdateAt($updateAt){
        $this->attributes["updateAt"] = $updateAt;
    }

    
}
