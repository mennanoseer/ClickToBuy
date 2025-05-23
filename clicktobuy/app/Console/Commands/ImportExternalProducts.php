<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ExternalProductService;

class ImportExternalProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:import {count=20 : Number of products to import}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import products from external API';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(ExternalProductService $productService)
    {
        $count = $this->argument('count');
        $this->info("Importing {$count} products from external API...");
        
        $imported = $productService->importProductsFromAPI($count);
        
        $this->info("Successfully imported {$imported} products!");
        
        return Command::SUCCESS;
    }
}
