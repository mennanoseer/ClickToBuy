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
    protected $signature = 'products:import 
                            {count=50 : Number of products to import} 
                            {--source=dummyjson : API source (dummyjson, fakestoreapi)}';

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
        $source = $this->option('source');
        
        $this->info("Importing {$count} products from {$source}...");
        
        $imported = $productService->importProductsFromAPI($count, $source);
        
        $this->info("Successfully imported {$imported} products from {$source}!");
        
        return Command::SUCCESS;
    }
}
