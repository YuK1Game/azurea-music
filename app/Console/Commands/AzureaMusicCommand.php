<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Services\Music\{
    MusicXML,
    ScorePart,
};

use App\Services\Azurea\MusicXML as AzureaMusicXML;

use App\Services\Azurea\{
    ScorePart as AzureaScorePart,
};

class AzureaMusicCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'music:azurea';

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
     * @return int
     */
    public function handle()
    {
        $filename = resource_path('musicxml/Ojamajo_Carnival_Ojamajo_Doremi_Opening.mxl');

        $musicXml = new MusicXML($filename);
        $azureaMusicXml = new AzureaMusicXML($musicXml);
        $azureaMusicXml->exportCode();

        return 0;
    }
}
