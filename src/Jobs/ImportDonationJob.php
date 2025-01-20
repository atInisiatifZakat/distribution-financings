<?php

declare(strict_types=1);

namespace Inisiatif\Distribution\Financings\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Inisiatif\Distribution\Financings\Models\Donation;
use Inisiatif\Distribution\Financings\Utils\Helper\SpoutHelper;
use Inisiatif\ModelShared\ModelShared;
use Inisiatif\Package\User\ModelRegistrar;
use OpenSpout\Reader\CSV\Options;
use OpenSpout\Reader\CSV\Reader;

final class ImportDonationJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private string $fileName;

    public function __construct(string $fileName)
    {
        $this->fileName = $fileName;
    }

    public function handle(): void
    {
        $option = new Options;
        $option->FIELD_DELIMITER = ';';
        $reader = new Reader($option);
        $reader->open(Storage::disk('temps')->path($this->fileName));

        foreach ($reader->getSheetIterator() as $sheet) {
            $spoutHelper = new SpoutHelper($sheet, 1);

            foreach ($sheet->getRowIterator() as $key => $row) {
                if ($key === 1) {
                    continue;
                }

                $dataMapped = $this->mappingArrayData($spoutHelper, $row->toArray());

                Donation::query()->updateOrCreate([
                    'identification_number' => $dataMapped['identification_number']
                ],[
                    'branch_id' => $dataMapped['branch_id'],
                    'donor_id' => $dataMapped['donor_id'],
                    'employee_id' => $dataMapped['employee_id'],
                    'transaction_date' => $dataMapped['transaction_date'],
                    'transaction_status' => $dataMapped['transaction_status'],
                    'amount' => $dataMapped['amount'],
                    'total_amount' => $dataMapped['total_amount'],
                    'donation_type' => $dataMapped['donation_type']
                ]);
            }
        }

        $reader->close();
    }

    private function mappingArrayData(SpoutHelper $spoutHelper, array $row): array
    {
        $dataMapped = $spoutHelper->rowWithFormattedHeaders($row);

        $employee = empty($dataMapped['employee_id']) ? null : ModelRegistrar::getEmployeeModel()->newQuery()->where('id', $dataMapped['employee_id'])->first();
        $dataMapped['employee_id'] = empty($employee) ? null : $employee->getKey();

        $branch = empty($dataMapped['branch_id']) ? null : ModelRegistrar::getBranchModel()->newQuery()->where('id', $dataMapped['branch_id'])->first();
        $dataMapped['branch_id'] = empty($branch) ? null : $branch->getKey();

        $donor = empty($dataMapped['donor_id']) ? null : ModelShared::getDonorModel()->newQuery()->where('id', $dataMapped['donor_id'])->first();
        $dataMapped['donor_id'] = empty($donor) ? null : $donor->getKey();

        return $dataMapped;
    }
}
