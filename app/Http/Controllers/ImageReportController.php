<?php

namespace App\Http\Controllers;


use App\Models\ImageReport;
use App\Traits\ApiResponser;
use Error;
use Illuminate\Http\Request;
use Google\Cloud\Vision\V1\Feature\Type;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Google\Cloud\Vision\V1\Likelihood;
use Google\Cloud\Vision\VisionClient;
use Illuminate\Support\Facades\Storage;
use Google\Cloud\Storage\StorageClient;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Http;

class ImageReportController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    /**
     * @OA\Get(
     *      path="/reports",
     *      operationId="getReportsList",
     *      tags={"Reports"},
     *      summary="Get list of reports",
     *      description="Returns list of reports",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/ReportResource")
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *     )
     */
    public function index()
    {
        $imageModerators = ImageReport::all();

        return $this->success($imageModerators, "reports");
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

        if(!isset($safeSearch)){
            return null;
        }
        
        foreach ($safeSearch->info() as $key => $value) {
            # code...
            $values[] = $value;
        }
        $probability = 'VERY_LOW';
        if(in_array('VERY_LIKELY', $values) 
           && in_array('LIKELY', $values) 
           && in_array('POSSIBLE', $values)){
            $probability = 'LOW';
            $probability_level = 0.25;
        }elseif(in_array('VERY_LIKELY', $values) && in_array('LIKELY', $values)){
            $probability = 'MEDIUM';
            $probability_level = 0.50;
        }elseif(in_array('VERY_LIKELY', $values)){
            $probability = 'HIGH';
            $probability_level = 0.90;
        }else{
            $probability = 'VERY_LOW';
            $probability_level = 0.10;
        }
        // 'VERY_UNLIKELY', 'UNLIKELY', 'POSSIBLE', 'LIKELY', 'VERY_LIKELY'
        
        return array_merge($safeSearch->info(), ['probability' => $probability, 'probability_level' => $probability_level]);
    }
    
    public function calculateProbabilityLevel($values){
        $prob = 1;
        foreach ($values as $key => $value) {
            $prob *= $this->calculateIndividualProbabilityLevel($value);
        }
        
        return $prob;
    }
    public function calculateIndividualProbabilityLevel($value){
        if($value == 'VERY_UNLIKELY') $probability_level = 1/5;
        elseif($value == 'UNLIKELY') $probability_level = 2/5;
        elseif($value == 'POSSIBLE') $probability_level = 3/5;
        elseif($value == 'LIKELY') $probability_level = 4/5;
        elseif($value == 'VERY_LIKELY') $probability_level = 0.9999;
        else $probability_level = 0.000001;

        return $probability_level;
    }

    /**
     * @OA\Post(
     *      path="/report-image",
     *      operationId="createReport",
     *      tags={"Reports"},
     *      summary="Create new report",
     *      description="Create new report",
     * 
     *      @OA\Parameter(
     *          name="user_id",
     *          description="User ID",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="image",
     *          description="The image to be reported (the link or byte)",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="callback",
     *          description="The Callback endpoint use to send report result when it's available",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="url"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/ImageReport")
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *     )
     */
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
                }else{
                    return $this->error('Sorry we cannot proceed your image', Response::HTTP_UNPROCESSABLE_ENTITY);
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
                try {
                    $image_base64=base64_decode($b64image);
                } catch (\Exception $ex) {
                    return $this->error('Sorry we cannot proceed your image', Response::HTTP_UNPROCESSABLE_ENTITY);
                }
                
            }
            
            $imgName = $request->user_id . '-'. date('Y-m-d') . time() . $image_type;
            $file= storage_path('app/public/temp_images/') . $imgName;
            file_put_contents($file,$image_base64);
            
        }

        try{
            $probabilities = $this->calculateSensitivity($imgName);
            if(!is_array($probabilities)){
                return $this->error('Sorry we cannot proceed your image', Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }catch(\Exception $ex){
            dd($ex);
            return $this->error('Sorry we cannot proceed your image', Response::HTTP_UNPROCESSABLE_ENTITY);
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
                'probability_level' => $probabilities['probability_level'],
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
     * @OA\Get(
     *      path="/reevaluate-report/{id}",
     *      operationId="revaluateReport",
     *      tags={"Reports"},
     *      summary="Revaluate existing report",
     *      description="Revaluate existing report",
     * 
     *      @OA\Parameter(
     *          name="id",
     *          description="Report ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/ImageReport")
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *     )
     */
    public function reevaluateExistingReport($id){
        $imageReport = ImageReport::findOrFail($id);
        if (!Storage::exists('/temp_images')) {
            Storage::makeDirectory('temp_images');
        }
        $ext = pathinfo($imageReport->image, PATHINFO_EXTENSION);
        $mediaItems = $imageReport->getFirstMedia('image');
        $fullPathOnDisk = $mediaItems->getPath();
        $imgName = date('Y-m-d') . time() . '.' . $ext;
        $file= storage_path('app/public/temp_images/') . $imgName;

        try{
            copy($fullPathOnDisk, $file);
        }catch(\Exception $copy){
            return $this->error("Sorry! We canot evaluate right now, please try again later.", Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
        try{
            $probabilities = $this->calculateSensitivity($imgName);
            if(!is_array($probabilities)){
                return $this->error('Sorry we cannot proceed your image', Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }catch(\Exception $ex){
            return $this->error("Sorry! We canot evaluate right now, please try again later.", Response::HTTP_SERVICE_UNAVAILABLE);
        }

        if(isset($probabilities) && is_array($probabilities)){
            $imageReport->update([
                'adult' => $probabilities['adult'],
                'spoof' => $probabilities['spoof'],
                'medical' => $probabilities['medical'],
                'violence' => $probabilities['violence'],
                'racy' => $probabilities['racy'],
                'probability' => $probabilities['probability'],
                'probability_level' => $probabilities['probability_level'],
                'evaluated' => true
            ]);
        }

        try {
            unlink($file);
        } catch (\Throwable $th) {
            //throw $th;
        }

        return $this->success($imageReport, "report evaluated!!!");
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    /**
     * @OA\Put(
     *      path="/approve-report/{id}",
     *      operationId="approveReport",
     *      tags={"Reports"},
     *      summary="Approve report",
     *      description="Approve report",
     * 
     *      @OA\Parameter(
     *          name="id",
     *          description="Report ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="approve",
     *          description="approval value",
     *          required=true,
     *          in="query",
     *          
     *          @OA\Schema(
     *              type="boolean",
     *              default=false,
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/ImageReport")
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *     )
     */
    public function approveOrRejectReport(Request $request, $id){
        $request->approve = filter_var($request->approve, FILTER_VALIDATE_BOOLEAN);
        
        $validator = Validator::make(['approve' => filter_var($request->approve, FILTER_VALIDATE_BOOLEAN)], [
            'approve' => 'required|boolean'
        ]);

        if($validator->fails()){
            return $this->error('Error', Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
        }
        $imageReport = ImageReport::find($id);
        if(!isset($imageReport)) return $this->error("Not found", Response::HTTP_NOT_FOUND);

        $imageReport->update([
            'approved' => $validator->validated()['approve']
        ]);

        if(isset($imageReport['callback'])){
            $guzzleRequest = Http::retry(3, 1000)->post($validator->validated()['callback'], [
                'id' => $imageReport['id'],
                'adult' => $imageReport['adult'],
                'spoof' => $imageReport['spoof'],
                'medical' => $imageReport['medical'],
                'violence' => $imageReport['violence'],
                'racy' => $imageReport['racy'],
                'probability' => $imageReport['probability'],
                'probability_level' => $imageReport['probability_level'],
                'approve' => $imageReport['approve']
            ]);
            //$response = $guzzleRequest->json();
        }

        // if(!$validator->validated()['approve']){
        //     $imageReport->delete();
        //     return $this->success([], "image rejected!");
        // }

        // $imageReport->delete();

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
    /**
     * @OA\Put(
     *      path="/update-report-callback/{id}",
     *      operationId="updateCallbackReport",
     *      tags={"Reports"},
     *      summary="Update callback endpoint",
     *      description="Update callback endpoint. Hit this to update the image report callback endpoint.",
     * 
     *      @OA\Parameter(
     *          name="id",
     *          description="Report ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="callback",
     *          description="callback link value",
     *          required=true,
     *          in="query",
     *          
     *          @OA\Schema(
     *              type="string",
     *              default="http://localhost/api/callback-test",
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/ImageReport")
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *     )
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'callback' => 'required|url'
        ]);

        if($validator->fails()){
            return $this->error('Error', Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
        }
        $imageReport = ImageReport::find($id);
        if(!isset($imageReport)) return $this->error("Not found", Response::HTTP_NOT_FOUND);

        $imageReport->update([
            'callback' => $validator->validated()['callback']
        ]);

        return $this->success($imageReport, "image approved!");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    /**
     * @OA\Get(
     *      path="/callback/{id}",
     *      operationId="callbackReport",
     *      tags={"Reports"},
     *      summary="Callback report. Hit this to get the image report moderation by providing the report id.",
     *      description="Callback report. Hit this to get the image report moderation by providing the report id.",
     * 
     *      @OA\Parameter(
     *          name="id",
     *          description="Report ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/ReportCallbackResource")
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *     )
     */
    public function callCallback($id)
    {
        $imageReport = ImageReport::find($id);
        if(!isset($imageReport)) return $this->error("Not found", Response::HTTP_NOT_FOUND);
        if(!isset($imageReport->callback)) return $this->error("Your entry dont have a callback endpoint.", Response::HTTP_NOT_FOUND);
        
        $guzzleRequest = Http::retry(3, 1000)->post($imageReport->callback, [
            'id' => $imageReport['id'],
            'adult' => $imageReport['adult'],
            'spoof' => $imageReport['spoof'],
            'medical' => $imageReport['medical'],
            'violence' => $imageReport['violence'],
            'racy' => $imageReport['racy'],
            'probability' => $imageReport['probability'],
            'probability_level' => $imageReport['probability_level'],
            'approve' => $imageReport['approve']
        ]);
        $response = $guzzleRequest->json();

        return $this->success($response, "Callback response");
    }
    

    /**
     * @OA\Post(
     *      path="/callback-test",
     *      operationId="callbackTester",
     *      tags={"Reports"},
     *      summary="Callback report test",
     *      description="Callback report test",
     * 
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *     )
     */
    public function callbackTester()
    {
        return $this->success(["message" => "Congrats! You reached the callback"], "Callback endpoint reached.");
    }

    /**
     * @OA\Delete(
     *      path="/destroy-image-report/{id}",
     *      operationId="destroyReport",
     *      tags={"Reports"},
     *      summary="Destroy report",
     *      description="Destroy report",
     * 
     *      @OA\Parameter(
     *          name="id",
     *          description="Report ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *     )
     */
    public function destroy($id)
    {
        $imageReport = ImageReport::withTrashed()->whereId($id)->first();
        if(!isset($imageReport)) return $this->error("Not found", Response::HTTP_NOT_FOUND);
        $imageReport->forceDelete();

        return $this->success([], "image destroyed");
    }

    /**
     * @OA\Delete(
     *      path="/archive-image-report/{id}",
     *      operationId="arhiveReport",
     *      tags={"Reports"},
     *      summary="Archive report",
     *      description="Archive report",
     * 
     *      @OA\Parameter(
     *          name="id",
     *          description="Report ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *     )
     */
    public function archive($id)
    {
        $imageReport = ImageReport::find($id);
        if(!isset($imageReport)) return $this->error("Not found", Response::HTTP_NOT_FOUND);
        $imageReport->delete();

        return $this->success([], "image archived");
    }
}
