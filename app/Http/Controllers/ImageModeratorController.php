<?php

namespace App\Http\Controllers;

use App\Models\ImageModerator;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Google\Cloud\Vision\V1\Feature\Type;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Google\Cloud\Vision\V1\Likelihood;
use Google\Cloud\Vision\VisionClient;
use Illuminate\Support\Facades\Storage;
use Google\Cloud\Storage\StorageClient;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class ImageModeratorController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $imageModerators = ImageModerator::all();

        return $this->success($imageModerators, "images");
    }

    public function calculateSensitivity($imageName){
        putenv('GOOGLE_APPLICATION_CREDENTIALS=' . base_path() . '/key.json');
        $vision = new VisionClient(['keyFile' => json_decode(file_get_contents(base_path() . "/key.json"), true)]);
        if (!Storage::exists('/temp_images')) {
            Storage::makeDirectory('temp_images');
        }
        $pic = fopen(storage_path('app/public/temp_images/' . $imageName), 'r');
        
        $image = $vision->image($pic, 
            ['SAFE_SEARCH_DETECTION']);
        $result = $vision->annotate($image);

        $safeSearch = $result->safeSearch();
        unlink(storage_path('app/public/temp_images/' . $imageName));

        
        // foreach ($safeSearch->info() as $key => $value) {
        //     # code...
        //     $values[] = $value;
        // }
        // $probability = 'VERY_LOW';
        // if(in_array(['VERY_LIKELY', 'LIKELY', 'POSSIBLE'], $values)){
        //     $probability = 'LOW';
        // }elseif(in_array(['VERY_LIKELY', 'LIKELY'], $values)){
        //     $probability = 'MEDIUM';
        // }elseif(in_array(['VERY_LIKELY'], $values)){
        //     $probability = 'HIGH';
        // }else{
        //     $probability = 'VERY_LOW';
        // }
        
        
        return $safeSearch->info();
    }

    public function reportImage(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'image' => 'required|max:10000|mimes:png,jpg,jpeg'
        ]);

        if($validator->fails()){
            return $this->error('Error', Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
        }
        $imgName = $request->user_id . date('Y-m-d') . time() . '-' . $request->image->getClientOriginalName();
        
        $request->image->storeAs('temp_images', $imgName, 'public');

        $probabilities = $this->calculateSensitivity($imgName);

        // unlink(storage_path('app/public/temp_images/' . $request->image->getClientOriginalName()));

        
        $imageModerator = ImageModerator::create([
            'user_id' => $validator->validated()['user_id'],
            'adult' => $probabilities['adult'],
            'spoof' => $probabilities['spoof'],
            'medical' => $probabilities['medical'],
            'violence' => $probabilities['violence'],
            'racy' => $probabilities['racy'],
        ]);

        $imageModerator
                ->addMediaFromRequest('image')
                ->toMediaCollection('image');

        return $this->success($imageModerator, "saved image probabilities");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
