<?php

declare(strict_types=1);

namespace AdminKit\Porto\Actions;

use AdminKit\Porto\DTO\AppendRowToFileDTO;
use Illuminate\Support\Str;

class AppendRowToFileArrayAction
{
    public function run(AppendRowToFileDTO $dto): int|bool
    {
        if (! file_exists($dto->destinationFilePath)) {
            return false;
        }

        $fileContent = file_get_contents($dto->destinationFilePath);

        // get content
        $content = Str::before(Str::after($fileContent, $dto->beforeAppendRow), $dto->AfterAppendRow);
        $content = rtrim($content);
        $content = ltrim($content, "\n");

        // set spaces
        $spaces = '';
        foreach (str_split($content) as $char) {
            if ($char === ' ') {
                $spaces .= ' ';
            } else {
                break;
            }
        }

        // check if exists
        if (Str::contains($content, $dto->appendRow)) {
            return true;
        }

        $trailingComma = ! str_ends_with($content, ',') ? ',' : '';

        $fileContent = str_replace(
            $content,
            $content.$trailingComma.PHP_EOL.$spaces.$dto->appendRow,
            $fileContent,
        );

        return file_put_contents($dto->destinationFilePath, $fileContent);
    }
}
