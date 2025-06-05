namespace App\Filament\Exports;

use App\Models\TotalIncome;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;

class ProductExporter implements FromQuery
{
    use Exportable;

    public function query()
    {
        // Query data yang akan diekspor
        return TotalIncome::query();
    }
}