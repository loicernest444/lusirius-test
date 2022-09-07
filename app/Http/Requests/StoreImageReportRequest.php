<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *      title="Store Image Report request",
 *      description="Store Image Report request body data",
 *      type="object",
 *      required={"user_id","image"}
 * )
 */
class StoreImageReportRequest extends FormRequest
{
    /**
     * @OA\Property(
     *      title="user_id",
     *      description="ID of the user who send report",
     *      example="1"
     * )
     *
     * @var string
     */
    public $user_id;

    /**
     * @OA\Property(
     *      title="image",
     *      description="The image to be reported (the link or byte)",
     *      example="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT3FX_qrHfguSoEBB293hEFgrd1rPOdTSLY2Q&usqp=CAU"
     * )
     *
     * @var string
     */
    public $image;

    /**
     * @OA\Property(
     *      title="callback",
     *      description="The Callback endpoint use to send report result when it's available",
     *      example="http://127.0.0.1/api/callback"
     * )
     *
     * @var string
     */
    public $callback;


    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            //
        ];
    }
}
