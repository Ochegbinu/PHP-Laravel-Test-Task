<?php

namespace App\Http\Controllers\API;

use App\Events\BrtCreated;
use App\Http\Controllers\BaseAPIController;
use App\Models\Brt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BrtController extends BaseAPIController
{

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reserved_amount' => 'required|numeric|min:0',
            'status' => 'required|in:active,expired',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $brt = Brt::create([
            'user_id' => Auth::id(), 
            'brt_code' => Str::uuid()->toString(), 
            'reserved_amount' => $request->reserved_amount,
            'status' => $request->status,
        ]);

        return $this->success_response($brt);
    }

    public function index()
    {
        $brts = Auth::user()->brts;
        return $this->success_response($brts);
    }

    public function show($id)
    {
        $brt = Brt::where('user_id', Auth::user()->id)->findOrFail($id);
        return $this->success_response($brt);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'reserved_amount' => 'numeric|min:0',
            'status' => 'in:active,expired',
        ]);

        if ($validator->fails()) {
            return $this->error_response($validator->errors(), 422);
        }

        $brt = Brt::where('user_id', Auth::user()->id)->findOrFail($id);
        $brt->update($request->only(['reserved_amount', 'status']));

        return $this->success_response($brt);
    }

    public function destroy($id)
    {
        $brt = Brt::where('user_id', Auth::user()->id)->findOrFail($id);
        $brt->delete();

        return $this->success_response('BRT deleted successfully');
    }
    // public function store(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'reserved_amount' => 'required|numeric|min:0',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json($validator->errors(), 422);
    //     }

    //     $brt = Brt::create([
    //         'user_id' => auth()->id(),
    //         'brt_code' => 'BRT-' . strtoupper(Str::random(8)),
    //         'reserved_amount' => $request->reserved_amount,
    //     ]);

    //     // Trigger real-time notification
    //     event(new BrtCreated($brt));

    //     return response()->json($brt, 201);
    // }


}
