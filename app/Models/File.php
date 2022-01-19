<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class File extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'path',
        'thumbpath',
        'type',
        'extension'
    ];

    /**
     * File relation to any other model
     */
    public function fileable()
    {
        return $this->morphTo();
    }

    /**
     * Get the url to the file 
     * 
     * @return string|null
     */
    public function url(): ?string
    {
        if(empty($this->path)) return null;

        /** @var Storage */
        $publicStorage = Storage::disk('public');

        if($publicStorage->missing($this->path)) return null;

        return $publicStorage->url($this->path);
        
    }    
    
    /**
     * Get a url to the files thumbnail
     * 
     * @return string|null
     */
    public function thumbUrl(): ?string
    {
        /** @var Storage */
        $publicStorage = Storage::disk('public');

        if($publicStorage->exists($this->thumbpath)){
            return $publicStorage->url($this->thumbpath);
        }

        return null;
        
    }

    /**
     * Delete file and its thumbnail from storage
     * 
     * @return bool
     */
    public function deleteFromStorage() : bool
    {
        return $this->deleteOriginal() && $this->deleteThumbnail();
    }

    /**
     * Delete the fil from the databas if xists
     * 
     * @return bool
     */
    public function deleteOriginal() : bool
    {
        /** @var Storage */
        $publicStorage = Storage::disk('public');

        if($publicStorage->exists($this->path)){

            return $publicStorage->delete($this->path);

        }
        
        return $publicStorage->missing($this->path);
    }

    /**
     * Delete file thumbnail from the database if it exists
     * 
     * @return bool
     */
    public function deleteThumbnail() : bool
    {
        /** @var Storage */
        $publicStorage = Storage::disk('public');

        if(is_null($this->thumbpath)) return true;

        if($publicStorage->exists($this->thumbpath)){

            return $publicStorage->delete($this->thumbpath);

        }

        return $publicStorage->missing($this->thumbpath);
    }
}
