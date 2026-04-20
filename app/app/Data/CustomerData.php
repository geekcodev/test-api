<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\Customer;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\Regex;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;


#[MapName(SnakeCaseMapper::class)]
class CustomerData extends Data
{
    public function __construct(
        #[IntegerType]
        public int     $id,
        #[StringType, Regex('/^[А-Яа-яёЁ\s\-]+$/u')]
        public string  $name,
        #[Email]
        public string  $email,
        #[StringType]
        public ?string $phone,
    )
    {
    }
}
