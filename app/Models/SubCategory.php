<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    use HasFactory;

    protected $table = 'sub_categories'; // match migration table name
    protected $guarded = [];

    public function category() {
        return $this->belongsTo(Category::class, 'category_id');
    }
    
}

// namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;

// class SubCategory extends Model
// {
//    use HasFactory;
//    protected $guarded =[];

//  public function category(){
//         return $this->belongsTo(Category::class, 'category_id' ,'id');
//     }
   
// }
