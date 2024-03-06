<?php

namespace App\Console\Api;

use App\Models\Simone;
use App\Simone\Question;
use App\Simone\Serie;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ScrapeSimon
{

    protected $headers = [
        'authority'          => 'api.envoituresimone.com',
        'accept'             => 'application/json, text/plain, */*',
        'accept-language'    => 'en,tr;q=0.9,fr;q=0.8,en-US;q=0.7,mg;q=0.6',
        'authorization'      => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIyNTk3MTI4Iiwic2NwIjoidXNlciIsImF1ZCI6bnVsbCwiaWF0IjoxNzA5NzI0NjM1LCJleHAiOjE3MTEwMjA2MzUsImp0aSI6IjJhZDllNWExLWRhOWEtNDgxNi04YjZjLTA5NzIwY2FkMTE1ZiJ9.FDS8KQmyZt6OZ441ZzRBn_WHtzEGQHGOU1bCaUkYZRM',
        'evs_auth_issued_at' => '1709728248257',
        'evs_auth_token'     => 'b57c8b2c26cae62c3bb6ee42be1c413ccf5ad0b03d6bdfc201fd54504ab2359f',
        'if-none-match'      => 'W/"3700b8b247e612bd4ef4f877c101017b"',
        'origin'             => 'https://app.envoituresimone.com',
        'referer'            => 'https://app.envoituresimone.com/',
        'sec-ch-ua'          => '"Chromium";v="122", "Not(A:Brand";v="24", "Google Chrome";v="122"',
        'sec-ch-ua-mobile'   => '?0',
        'sec-ch-ua-platform' => '"Windows"',
        'sec-fetch-dest'     => 'empty',
        'sec-fetch-mode'     => 'cors',
        'sec-fetch-site'     => 'same-site',
        'user-agent'         => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
        'x-app-version'      => '1.83.2'
    ];

    protected $baseUrl = "https://api.envoituresimone.com/api/v2/account/1777428655";


    public function __construct()
    {
    }


    public function save()
    {
        $arrays = array_chunk(Question::IDS, 50);

        foreach ($arrays as $array) {

            foreach ($array as $key => $id) {
                try {

                    $response = $this->getQuestion($id);

                    $data = $response->json()["data"] ?? null;

                    if ($response->ok() && $data) {
                        $data = json_encode($data);

                        Simone::create(["data" => $data]);

                        Log::info("creating: $data");
                    }
                } catch (\Throwable $th) {
                    Log::info($th->getMessage());
                }
            }
        }

        return "question generated";

        // return  $response;
    }

    private function getQuestion($id)
    {
        $url = "https://api.envoituresimone.com/api/v1/questions/$id";
        return Http::withHeaders($this->headers)->get($url);
    }

    private function getQuestionIDs()
    {
        $datas = [];

        foreach ($this->serieIDs() as $key => $id) {
            $response = Http::withHeaders($this->headers)->get("$this->baseUrl/series/$id");

            if ($response->ok()) {
                $response = $response->json();
                $questions = $response["data"]["relationships"]["questions"]["data"] ?? null;

                if (is_array($questions)) {
                    foreach ($questions as $key => $question) {
                        $datas[] = $question["id"];
                    }
                }
            }
        }

        return $datas;
    }

    private function serieIDs()
    {
        return array_map(function ($serie) {
            return $serie["id"];
        }, json_decode(Serie::JSON, true));
    }
}
