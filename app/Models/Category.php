<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'is_directory', 'level', 'path'];

    protected $casts = ['is_directory' =>'boolean'];


    protected static function boot()
    {
        parent::boot();

        static::creating(function (Category $category)
        {
            // 如果创建的是一个根类目
            if(is_null($category->parent_id)){
                $category->level = 0;
                $category->path = '-';

            }else{
                // 将层级设为父类目的层级 + 1
                $category->level = $category->parent->level +1 ;
                // 将 path 值设为父类目的 path 追加父类目 ID 以及最后跟上一个 - 分隔符
                $category->path = $category->parent->path.$category->parent_id.'-';
            }

        });


    }


    public function parent()
    {
        return $this->belongsTo(Category::class);
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }


    /**
     * 定一个一个访问器，获取所有祖先类目的 ID 值
     * @return array
     */
    public function getPathIdsAttribute()
    {
        // trim($str, '-') 将字符串两端的 - 符号去除
        // explode() 将字符串以 - 为分隔切割为数组
        // 最后 array_filter 将数组中的空值移除
        $path = trim($this->path, '-');
        $path = explode('-', $path);
        return array_filter($path);
    }

    /**
     *  定义一个访问器，获取所有祖先类目并按层级排序
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getAncestorsAttribute()
    {
        return Category::query()
            ->whereIn('id', $this->getPathIdsAttribute())
            ->orderBy('level')
            ->get();
    }

    public function getFullNameAttribute()
    {
        return $this->getAncestorsAttribute()
            ->pluck('name')
            ->push($this->name)
            ->implode('-');
    }



}
