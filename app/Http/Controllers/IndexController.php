<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


use App\Services\Azurea\V2\Music as AzureaMusic;
use App\Services\Azurea\V2\Notes\Duration;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class IndexController extends Controller
{
    public function index(Request $request)
    {
        return response()->view('index');
    }

    public function post(Request $request)
    {
        $parts = collect();
        $errors = collect();

        try {
            if ($file = collect($request->file)->first()) {
                if (preg_match('/\.mxl$/', $file->getClientOriginalName()) === 1) {
                    $azureaMusic = new AzureaMusic($file->getPathname());
                    $parts = $azureaMusic->getCodes();
                }
            }
        } catch (\Exception $e) {
            $errors->push($e->getMessage());
        }

        return response()->view('index', [
            'parts' => $parts,
            'errors' => $errors,
        ]); 
    }

}
