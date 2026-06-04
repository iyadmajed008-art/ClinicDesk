<?php

declare(strict_types=1);

final class Paginator
{
    public function __construct(
        public readonly int $totalItems,
        public readonly int $perPage,
        public readonly int $currentPage
    ) {
    }

    public function offset(): int
    {
        return max(0, ($this->currentPage - 1) * $this->perPage);
    }

    public function totalPages(): int
    {
        return max(1, (int) ceil($this->totalItems / $this->perPage));
    }

    public function hasPrev(): bool
    {
        return $this->currentPage > 1;
    }

    public function hasNext(): bool
    {
        return $this->currentPage < $this->totalPages();
    }
}
