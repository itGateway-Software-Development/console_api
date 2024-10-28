<?php

namespace App\Modules\ServerManagement\ServerTypes\Models;

use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Modules\ServerManagement\OperationSystems\Models\OperationSystem;

class ServerType extends Model
{
    use HasFactory, HasSlug;

    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function operationSystems() {
        return $this->belongsToMany(OperationSystem::class);
    }
}
