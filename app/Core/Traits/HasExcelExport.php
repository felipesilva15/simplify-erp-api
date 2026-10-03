<?php

namespace App\Core\Traits;

use App\Core\Http\Requests\Core\ExportRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Disponibiliza a rota de exportação Excel para o controller.
 *
 * A rota de exportação recebe os mesmos parâmetros do método index
 * (filters, sorts, etc.) e ainda os parâmetros de exportação `format`
 * e `extension`:
 *
 *  - GET /{entities}/export                          → exportação padrão (xlsx)
 *  - GET /{entities}/export?format=summarized          → formato customizado
 *  - GET /{entities}/export?format=summarized&extension=csv
 *
 * O campo `format` informa o formato da exportação e o mapeamento
 * formato → classe deve ser definido no controller implementando
 * `exportClassForFormat()` com um `match`, por exemplo:
 *
 *     protected function exportClassForFormat(string $format): string
 *     {
 *         return match ($format) {
 *             'full' => PartnerExport::class,
 *             'summarized' => PartnerSummaryExport::class,
 *             'detailed' => PartnerDetailedExport::class,
 *             default => throw new InvalidArgumentException(
 *                 "Formato de exportação [{$format}] não suportado."
 *             ),
 *         };
 *     }
 *
 * O formato padrão é `full` (ajustável sobrescrevendo
 * `defaultExportFormat()`) e a extensão padrão é `xlsx`.
 */
trait HasExcelExport
{
    public function export(ExportRequest $request): BinaryFileResponse
    {
        $this->authorize('export', $this->exportModelClass());

        $params = $request->all();

        $format = $params['format'] ?? $this->defaultExportFormat();
        $extension = $params['extension'] ?? 'xlsx';

        $query = $this->exportQuery($params);

        $exportClass = $this->exportClassForFormat($format);

        return Excel::download(new $exportClass($query), $this->exportFileName($format, $extension));
    }

    protected function exportQuery(array $params): Builder
    {
        return $this->service->exportQuery($params);
    }

    /**
     * Retorna a classe de exportação responsável pelo formato informado.
     *
     * Deve ser implementado no controller definindo o mapeamento
     * formato → classe com um `match`.
     */
    abstract protected function exportClassForFormat(string $format): string;

    protected function defaultExportFormat(): string
    {
        return 'full';
    }

    protected function exportFileName(string $format, string $extension): string
    {
        $fileName = Str::kebab(Str::snake(class_basename($this->exportModelClass())));

        $baseName = $format === $this->defaultExportFormat()
            ? $fileName
            : "{$fileName}-{$format}";

        return "{$baseName}.{$extension}";
    }

    abstract protected function exportModelClass(): string;
}
