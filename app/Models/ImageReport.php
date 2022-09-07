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
     *     title="ID",
     *     description="ImageReport ID",
     *     format="int64",
     *     example=1
     * )
     *
     * @var integer
     */
    private $id;

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
     *     example="http://127.0.0.1/api/reports"
     * )
     *
     * @var \Url
     */
    private $callback;

    /**
     * @OA\Property(
     *     title="Adult",
     *     description="The adult probability",
     *     example="UNLIKELY"
     * )
     *
     * @var string
     */
    private $adult;

    /**
     * @OA\Property(
     *     title="spoof",
     *     description="The spoof probability",
     *     example="UNLIKELY"
     * )
     *
     * @var string
     */
    private $spoof;

    /**
     * @OA\Property(
     *     title="medical",
     *     description="The medical probability",
     *     example="UNLIKELY"
     * )
     *
     * @var string
     */
    private $medical;

    /**
     * @OA\Property(
     *     title="violence",
     *     description="The violence probability",
     *     example="UNLIKELY"
     * )
     *
     * @var string
     */
    private $violence;

    /**
     * @OA\Property(
     *     title="racy",
     *     description="The racy probability",
     *     example="UNLIKELY"
     * )
     *
     * @var string
     */
    private $racy;

    /**
     * @OA\Property(
     *     title="approved",
     *     description="Show if the report is approved or not",
     *     example=true
     * )
     *
     * @var string
     */
    private $approved;

    /**
     * @OA\Property(
     *     title="evaluated",
     *     description="Show if the report is evaluated or not",
     *     example=false
     * )
     *
     * @var string
     */
    private $evaluated;

    /**
     * @OA\Property(
     *     title="probability",
     *     description="Show the probability of image to be a sensitive content",
     *     example="HIGH"
     * )
     *
     * @var string
     */
    private $probability;
    
    /**
     * @OA\Property(
     *     title="probability_level",
     *     description="Show the probability level (in peecentage) of image to be a sensitive content",
     *     example="0.5"
     * )
     *
     * @var string
     */
    private $probability_level;

    /**
     * @OA\Property(
     *     title="Created at",
     *     description="Created at",
     *     example="2022-09-06 17:50:45",
     *     format="datetime",
     *     type="string"
     * )
     *
     * @var \DateTime
     */
    private $created_at;

    /**
     * @OA\Property(
     *     title="Updated at",
     *     description="Updated at",
     *     example="2022-09-06 17:50:45",
     *     format="datetime",
     *     type="string"
     * )
     *
     * @var \DateTime
     */
    private $updated_at;

    /**
     * @OA\Property(
     *     title="Deleted at",
     *     description="Deleted at",
     *     example="2022-09-06 17:50:45",
     *     format="datetime",
     *     type="string"
     * )
     *
     * @var \DateTime
     */
    private $deleted_at;

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
