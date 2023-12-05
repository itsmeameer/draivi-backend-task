# Draivi Backend Task
This is the backend task for Draivi build using Laravel and DataTables.

## Setup

 1. Make sure you have PHP and MySQL installed on your device
 2. Download the repository and run the command `composer install`
 3. Copy the `.env.example` to `.env`
 4. Set a `DB_DATABASE`, `DB_USERNAME` and `DB_PASSWORD`. Change other variables if needed. The example file already has some settings, they can be changed if needed.
 5. Set the API key for the Currency Layer API
 6. Run the command `php artisan key:generate` to generate an app key
 7. Run the command `php artisan migrate` to setup the database.

### Import File
Run the command `php artisan price-list:import` to download and import the file.

##### Potential Issue:
you might the error message "The file is not an Excel file." in the CLI. This is because the excel file is being protected by "[Imperva Incapsula](https://www.imperva.com/)". It detected and prevents any scripts or scrapers from downloading the file. There are some ways to bypass the security like using a headless browser. But this is out of scope for this project.
##### Solution:
You can download the file manually, place it in "storage/files/backup.xlsx" add the code `$price_list_path = storage_path('files/backup.xlsx');` in `app/Console/Commands/ImportPriceList.php` after line 71 to run the importer.

### Frontend
Run the command `php artisan serve` to start the server. The app's home will have a table showing the sortable/searchable pricelist.
