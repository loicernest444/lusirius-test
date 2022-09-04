<?php

namespace App\Http\Controllers;


use App\Models\ImageReport;
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

class ImageReportController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $imageModerators = ImageReport::all();

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
        
        foreach ($safeSearch->info() as $key => $value) {
            # code...
            $values[] = $value;
        }
        $probability = 'VERY_LOW';
        if(in_array(['VERY_LIKELY', 'LIKELY', 'POSSIBLE'], $values)){
            $probability = 'LOW';
        }elseif(in_array(['VERY_LIKELY', 'LIKELY'], $values)){
            $probability = 'MEDIUM';
        }elseif(in_array(['VERY_LIKELY'], $values)){
            $probability = 'HIGH';
        }else{
            $probability = 'VERY_LOW';
        }
        
        
        return array_merge($safeSearch->info(), ['probability' => $probability]);
    }

    public function reportImage(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'image' => 'required',
            'callback' => 'sometimes|url'
        ]);

        if($validator->fails()){
            return $this->error('Error', Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
        }
        if($request->hasFile('image')){
            $imgName = $request->user_id . '-'. date('Y-m-d') . time() . '-' . $request->image->getClientOriginalName();
            
            $request->image->storeAs('temp_images', $imgName, 'public');
        } else {
            try {
                $b64image = base64_encode(file_get_contents($request->image));
            } catch (\Exception $ex) {
                if(str_contains($ex, "Invalid argument")){
                    $b64image = $request->image;
                }
            } catch(\Exception $ex1){
                throw $ex1;
            }
            
            $image_type = '.png';

            // preserve file extension if it's possible
            if(str_contains($b64image, ';base64,')){
                $image_parts=explode(";base64,",$b64image);
                $image_base64=base64_decode($image_parts[1]);
                if(str_contains($b64image, 'image/jpeg')){
                    $image_type = '.jpeg';
                }elseif(str_contains($b64image, 'image/jpg')){
                    $image_type = '.jpg';
                }
            }else {
                $image_base64=base64_decode($b64image);
            }
            
            $imgName = $request->user_id . '-'. date('Y-m-d') . time() . $image_type;
            $file= storage_path('app/public/temp_images/') . $imgName;
            file_put_contents($file,$image_base64);
        }

        try{
            $probabilities = $this->calculateSensitivity($imgName);
        }catch(\Exception $ex){
            
        }

        if(isset($probabilities) && is_array($probabilities)){
            $imageModerator = ImageReport::create([
                'user_id' => $validator->validated()['user_id'],
                'callback' => $validator->validated()['callback'] ?? null,
                'adult' => $probabilities['adult'],
                'spoof' => $probabilities['spoof'],
                'medical' => $probabilities['medical'],
                'violence' => $probabilities['violence'],
                'racy' => $probabilities['racy'],
                'probability' => $probabilities['probability'],
                'evaluated' => true
            ]);
        }else{
            $imageModerator = ImageReport::create([
                'user_id' => $validator->validated()['user_id'],
                'callback' => $validator->validated()['callback'] ?? null,
                'evaluated' => false
            ]);
        }
        
        if($request->hasFile('image')){
            $imageModerator
                ->addMediaFromRequest('image')
                ->toMediaCollection('image');
        }else{
            $imageModerator
                ->addMedia(storage_path('app/public/temp_images/') . $imgName)
                ->toMediaCollection('image');
        }
        try {
            unlink(storage_path('app/public/temp_images/' . $imgName));
        } catch (\Throwable $th) {
            //throw $th;
        }
        
        return $this->success($imageModerator, "saved image probabilities");
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function approveOrRejectReport(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'approve' => 'required|boolean'
        ]);

        if($validator->fails()){
            return $this->error('Error', Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
        }
        $imageReport = ImageReport::findOrFail($id);

        $imageReport->update([
            'approved' => $validator->validated()['approve']
        ]);

        if(!$validator->validated()['approve']){
            $imageReport->delete();
            return $this->success([], "image rejected!");
        }

        return $this->success($imageReport, "image approved!");
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
        $imageReport = ImageReport::findOrFail($id);
        $imageReport->forceDelete();

        return $this->success([], "image destroyed");
    }

    public function archive($id)
    {
        $imageReport = ImageReport::find($id);
        $imageReport->delete();

        return $this->success([], "image archived");
    }
}
