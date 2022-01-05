<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'path',
        'thumbpath',
        'type',
        'extension'
    ];

    /**
     * Get the url to the file 
     * 
     * @return string|null
     */
    public function url(): ?string
    {
        if(empty($this->path)) return null;

        /** @var Filesystem */
        $publicStorage = Storage::disk('public');

        return $publicStorage->url($this->path);
        
    }
}
