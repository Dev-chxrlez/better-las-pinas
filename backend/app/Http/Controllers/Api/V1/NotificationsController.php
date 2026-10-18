<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class NotificationsController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $items=$request->user()->notifications()->latest()->paginate(25);
        return response()->json(['success'=>true,'message'=>'Notifications.','data'=>['items'=>$items->getCollection()->map(fn($item)=>['id'=>$item->id,'data'=>$item->data,'read_at'=>$item->read_at?->toIso8601String(),'created_at'=>$item->created_at?->toIso8601String()]),'pagination'=>['current_page'=>$items->currentPage(),'last_page'=>$items->lastPage(),'total'=>$items->total()]]]);
    }
}
