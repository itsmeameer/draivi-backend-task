<?php

namespace App\Imports;

use App\Models\PriceList;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class PriceListImport implements ToModel, WithStartRow, WithValidation
{
    /**
     * The conversion rate to GBP.
     *
     * @var float
     */
    private $conversion_rate_to_gbp;

    /**
     * The starting row.
     *
     * @var int
     */
    private $starting_row;

    /**
     * Create a new instance.
     *
     * @param array $args The arguments.
     */
    public function __construct(array $args = [])
    {
        $this->conversion_rate_to_gbp = $args['conversion_rate_to_gbp'] ?? 0;
        $this->starting_row           = $args['starting_row'] ?? 5;
    }

    /**
     * Set the starting row.
     *
     * @return integer
     */
    public function startRow(): int
    {
        return $this->starting_row;
    }

    /**
     * Process the row.
     *
     * @param array $row_raw The raw row from the file.
     *
     * @return void
     */
    public function model(array $row_raw): ?PriceList
    {
        $row = array_values($row_raw);

        // Skip rows without a number or price.
        if (empty($row[0]) || empty($row[4])) {
            return null;
        }

        $price_list = PriceList::firstOrNew(['number' => $row[0]]);
        $price     = $row[4] ?? 0;

        $price_list->fill([
            'name'        => $row[1] ?? null,
            'bottle_size' => $row[3] ?? null,
            'price'       => $price,
            'price_gbp'   => $price * $this->conversion_rate_to_gbp,
        ]);

        return $price_list;
    }

    /**
     * Set the validation rules.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            '0' => 'required|numeric',
            '1' => 'nullable|string|max:255',
            '3' => 'nullable|string|max:255',
            '4' => 'nullable|numeric|between:0,999999.99',
        ];
    }
}
