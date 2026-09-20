<?php

namespace App\Core\Traits;

use App\Core\Exports\BaseExport;
use App\Core\Http\Requests\Core\ListRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Disponibiliza a rota padrão de exportação Excel para o controller.
 *
 * A rota de exportação recebe os mesmos parâmetros do método index
 * (filters, sorts, etc.), garantindo que a planilha seja gerada com base
 * nos filtros aplicados na consulta.
 *
 * Rotas:
 *  - GET /{entities}/export              → exportação padrão
 *  - GET /{entities}/export/{exportType} → exportação customizada
 *
 * Ambas as rotas aceitam o parâmetro de query `extension` para escolher o
 * formato do arquivo entre as extensões suportadas (padrão `xlsx`). Ex.:
 *
 *  - GET /users/export?extension=csv
 *  - GET /users/export/summary?extension=xls
 *
 * Para exportações customizadas, implemente no controller um método
 * `customExport{Type}` recebendo a query filtrada e retornando uma
 * instância de BaseExport, por exemplo:
 *
 *     protected function customExportSummary(Builder $query): BaseExport
 *     {
 *         return new MySummaryExport($query);
 *     }
 *
 * As extensões permitidas podem ser ajustadas sobrescrevendo
 * `allowedExportExtensions()`.
 */
trait HasExcelExport
{
    public function export(ListRequest $request, ?string $exportType = null): BinaryFileResponse
    {
        $this->authorize('export', $this->exportModelClass());

        $extension = $this->resolveExtension($request);

        $query = $this->exportQuery($request->all());

        $export = $this->resolveExport($exportType, $query);

        return Excel::download($export, $this->exportFileName($exportType, $extension));
    }

    protected function exportQuery(array $params): Builder
    {
        return $this->service->exportQuery($params);
    }

    protected function resolveExport(?string $exportType, Builder $query): BaseExport
    {
        if ($exportType === null || $exportType === '') {
            return new ($this->defaultExportClass())($query);
        }

        $customMethod = 'customExport'.Str::studly($exportType);

        if (! method_exists($this, $customMethod)) {
            throw new InvalidArgumentException(
                "Método de exportação customizada [{$customMethod}] não definido no controller [".static::class.']'
            );
        }

        $export = $this->{$customMethod}($query);

        if (! $export instanceof BaseExport) {
            throw new InvalidArgumentException(
                "O método [{$customMethod}] do controller [".static::class."] deve retornar uma instância de ".BaseExport::class.'.'
            );
        }

        return $export;
    }

    protected function allowedExportExtensions(): array
    {
        return ['xlsx', 'xls', 'csv'];
    }

    protected function resolveExtension(ListRequest $request): string
    {
        $extension = Str::lower(trim((string) $request->input('extension', 'xlsx')));

        if (! in_array($extension, $this->allowedExportExtensions(), true)) {
            throw new InvalidArgumentException(
                "Extensão [{$extension}] não suportada para exportação. Extensões permitidas: ".implode(', ', $this->allowedExportExtensions()).'.'
            );
        }

        return $extension;
    }

    protected function exportFileName(?string $exportType, string $extension): string
    {
        $fileName = Str::kebab(Str::snake(class_basename($this->exportModelClass())));

        $baseName = $exportType ? "{$fileName}-{$exportType}" : $fileName;

        return "{$baseName}.{$extension}";
    }

    abstract protected function exportModelClass(): string;

    abstract protected function defaultExportClass(): string;
}