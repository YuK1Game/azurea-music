<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Services\Azurea\V2\Music as AzureaMusic;

class MusicCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'music:run';

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
        $filename = resource_path('musicxml/Sorairo_Days__Gurren_Lagann_OP__F.B._Piano_Anime.mxl');

        $azureaMusic = new AzureaMusic($filename);
        $codes = $azureaMusic->getCodes();

        return 0;
    }
}
