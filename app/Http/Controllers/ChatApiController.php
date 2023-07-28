<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Http\Request;

class ChatApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // __invoke => Método que se ejecuta cuando se invoca la clase
    public function __invoke()
    {
        $chats = auth()->user()->chats()->paginate();
        return response()->json($chats);
    }
}
