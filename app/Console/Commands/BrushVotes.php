<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;

class BrushVotes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'BrushVotes:index';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $client = new Client();
        $response = $client->request('POST', 'https://www.hubpd.com/zcms/vote/result', [
            'form_params' => [
                'ID' => '351',
                'SiteID' => '147',
                'Subject_365' => '1304',
                'Subject_365' => '1305'
            ]
        ]);
        print_r($response);
    }
}
