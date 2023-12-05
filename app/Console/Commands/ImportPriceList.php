<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PriceListImport;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;

class ImportPriceList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'price-list:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import price list from the Excel file';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $price_list_url  = env('PRICE_LIST_URL');
        $price_list_path = env('PRICE_LIST_LOCAL_PATH');

        $this->info('Fetching price list from the URL...');

        if (empty($price_list_url) || empty($price_list_path)) {
            $this->error('Please set PRICE_LIST_URL and PRICE_LIST_LOCAL_PATH in the .env file.');
            return;
        }

        $price_list_response = Http::get($price_list_url);
        if (! $price_list_response->successful()) {
            $this->error('File does not exist at the given URL or could not be accessed.');
            return;
        }

        $this->info('File exists, removing the old file (if it exists)...');

        $price_list_path = storage_path($price_list_path);

        if (file_exists($price_list_path)) {
            $try_to_delete = unlink($price_list_path);
            if (! $try_to_delete) {
                $this->error('Error in deleting the old file.');
                return;
            }
            $this->info('Old file deleted successfully.');
        }

        $this->info('Downloading the file...');

        $try_to_download = file_put_contents($price_list_path, $price_list_response->body());
        if (!$try_to_download) {
            $this->error('Error in downloading the file.');
            return;
        }

        $this->info('File downloaded successfully, validating...');

        $price_list_path = storage_path('files/backup.xlsx');

        if (! $this->isExcelFile($price_list_path)) {
            $this->error('The file is not an Excel file.');
            return;
        }

        $this->info('File is valid, getting the conversion rate...');
        
        try {
            $conversion_rate = $this->getConversionRate();
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            return;
        }

        $this->info('Conversion rate is ' . $conversion_rate . ', starting import...');

        try {
            $importer = new PriceListImport([
                'conversion_rate_to_gbp' => $conversion_rate,
                'starting_row'           => env('PRICE_LIST_STARTING_ROW') ?? 5,
            ]);
            Excel::import($importer, $price_list_path);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            return;
        }

        $this->info('Import completed successfully.');
    }

    /**
     * Check if the file is an Excel file based on MIME type
     *
     * @param string $path
     *
     * @return bool
     */
    private function isExcelFile($path): bool
    {
        $allowedMimeTypes = [
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-excel',
            'application/octet-stream',
        ];

        $fileMimeType = File::mimeType($path);

        return in_array($fileMimeType, $allowedMimeTypes, true);
    }

    /**
     * Get the conversion rate from the API
     *
     * @return float
     */
    private function getConversionRate(): float
    {

        $api_url       = env('PRICE_LIST_CURRENCY_API_URL');
        $api_key       = env('PRICE_LIST_CURRENCY_API_KEY');
        $from_currency = env('PRICE_LIST_CURRENCY_FROM');
        $to_currency   = env('PRICE_LIST_CURRENCY_TO');

        if (empty($api_url) || empty($api_key) || empty($from_currency) || empty($to_currency)) {
            throw new \Exception('Please set PRICE_LIST_CURRENCY_API_URL, PRICE_LIST_CURRENCY_API_KEY, PRICE_LIST_CURRENCY_FROM and PRICE_LIST_CURRENCY_TO in the .env file.');
        }

        $full_api_url = $api_url . '?access_key=' . $api_key . '&source=' . $from_currency;
        $response     = Http::get($full_api_url);

        if (! $response->successful()) {
            throw new \Exception('Failed to fetch conversion rate from API. Status code: ' . $response->status());
        }
        
        $data = $response->json();
        
        if (!isset($data['success']) || !$data['success']) {
            throw new \Exception('Failed to fetch conversion rate from API. Error: ' . $data['error']['info']);
        }

        if (!isset($data['quotes']) || !isset($data['quotes'][$from_currency . $to_currency])) {
            throw new \Exception('Invalid conversion rate data received from API.');
        }

        return $data['quotes'][$from_currency . $to_currency];
    }
}
