<?php

namespace App\Modules\ServerManagement\OperationSystems\Models;

use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Modules\ServerManagement\Versions\Models\Version;
use App\Modules\ServerManagement\ServerTypes\Models\ServerType;

class OperationSystem extends Model
{
    use HasFactory, HasSlug;

    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function serverTypes() {
        return $this->belongsToMany(ServerType::class);
    }

    public function versions() {
        return $this->hasMany(Version::class);
    }
}
