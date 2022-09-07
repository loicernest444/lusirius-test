<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     title="ImageCallbackReport",
 *     description="ImageCallbackReport model",
 *     @OA\Xml(
 *         name="ImageCallbackReport"
 *     )
 * )
 */
class ImageCallbackReport extends Model
{
    use HasFactory;

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
}
