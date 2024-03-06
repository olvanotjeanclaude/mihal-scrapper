<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessSimone;
use App\Models\Simone;
use App\Simone\Question;
use App\Simone\Serie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SimoneController extends Controller
{
    public function questions()
    {
        return Simone::paginate();
        dd(count(Question::IDS));
    }

    public function question($id){
        return Simone::findOrFail($id);
    }
}
