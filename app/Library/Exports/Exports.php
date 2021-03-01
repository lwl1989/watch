<?php

namespace App\Library\Exports;


use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Exceptions\NoFilenameGivenException;
use Maatwebsite\Excel\Writer;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class Exports implements FromCollection,Responsable,WithColumnFormatting,ShouldAutoSize
{
    use Exportable;

    protected $header = [];
    protected $data;

    protected $size = [];
    public function __construct(array $data, array $header = [])
    {
        $this->header = $header;
        $this->data = $data;
    }

    public function collection()
    {
        $coll = new Collection();

        if(!empty($this->header)) {
            $index = 0;
            foreach ($this->header as $value) {
                $this->size[$index] = mb_strlen($value);
                $index++;
            }
            $coll->push($this->header);
        }

        foreach ($this->data as $item) {
            $index = 0;
            foreach ($item as $value) {
                if(isset($this->size[$index])) {
                    $len = mb_strlen($value);
                    if($len > $this->size[$index]) {
                        $this->size[$index] = $len;
                    }
                    $index ++;
                }
            }
            $coll->push($item);
        }

        return $coll;
    }


    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_NUMBER,
            'B' => NumberFormat::FORMAT_NUMBER,
        ];
    }

    public function download(string $fileName = null, string $writerType = null)
    {
        $fileName = $fileName ?? $this->fileName ?? null;

        if (null === $fileName) {
            throw new NoFilenameGivenException();
        }

        $excel = resolve(Excel::class);
//        /** @var Writer $writer */
//        $writer = $excel->getWriter();
//        var_dump($writer->getDelegate());
//        var_dump($writer->getSheetByIndex(2));
        //exit();
//        $iterator = $writer->getDelegate()->getWorksheetIterator();
//        foreach ($iterator as $value) {
//            foreach ($this->size as $key=>$v) {
//                $value->getColumnDimension($v)->setWidth(200);
//            }
//        }
//        //->getColumnDimension($key)->setAutoSize(true);
//        foreach ($this->size as $key=>$value) {
//            try {
//
//            }catch (\Exception $e) {
//                continue;
//            }
//        }
        return $excel->download($this, $fileName, $writerType ?? $this->writerType ?? null);
    }



}