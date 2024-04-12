<?php

declare(strict_types=1);

namespace AdminKit\Porto\DTO;

use Spatie\LaravelData\Data;

class AddItemToFileArrayDTO extends Data
{
    public function __construct(
        public string $appendRow,
        public string $destinationFilePath,
        public string $beforeAppendRow = '[',
        public string $AfterAppendRow = '];',
    ) {
    }
}
