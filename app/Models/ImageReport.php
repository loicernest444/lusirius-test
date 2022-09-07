<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @OA\Schema(
 *     title="ImageReport",
 *     description="ImageReport model",
 *     @OA\Xml(
 *         name="ImageReport"
 *     )
 * )
 */
class ImageReport extends Model implements HasMedia
{
    use HasFactory, SoftDeletes;
    use InteractsWithMedia;

    /**
     * @OA\Property(
     *     title="User ID",
     *     description="User ID",
     *     format="int64",
     *     example=1
     * )
     *
     * @var integer
     */
    private $user_id;

    /**
     * @OA\Property(
     *     title="Image",
     *     description="Image link or byte",
     *     example="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT3FX_qrHfguSoEBB293hEFgrd1rPOdTSLY2Q&usqp=CAU"
     * )
     *
     * @var string
     */
    private $image;

    /**
     * @OA\Property(
     *     title="Callback",
     *     description="The Callback endpoint use to send report result when it's available",
     *     example="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT3FX_qrHfguSoEBB293hEFgrd1rPOdTSLY2Q&usqp=CAU"
     * )
     *
     * @var \Url
     */
    private $callback;

    protected $appends = ['image'];

    protected $fillable = ['user_id', 'adult', 'spoof', 'medical', 'violence', 'racy', 'probability', 'callback', 'approved', 'evaluated', 'probability_level'];

    protected $hidden = ['media'];


    public function getImageAttribute($value){
        return $this->getFirstMediaUrl('image');
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this
            ->addMediaConversion('preview')
            ->fit(Manipulations::FIT_CROP, 300, 300)
            ->nonQueued();
    }
}
