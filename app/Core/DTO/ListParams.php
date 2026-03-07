<?php

namespace App\Core\DTO;

class ListParams {
    public function __construct(
        public ?array $filters,
        public ?string $sorts,
        public ?int $page,
        public ?int $per_page
    ) { }

    public function toArray(): array {
        return [
            'filters' => $this->filters,
            'sorts' => $this->sorts,
            'page' => $this->page,
            'per_page' => $this->per_page
        ];
    }
}