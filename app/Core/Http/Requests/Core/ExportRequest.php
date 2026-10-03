<?php

namespace App\Core\Http\Requests\Core;

/**
 * @OA\Schema(
 *     schema="ExportRequest",
 *     type="object",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/ListRequest"),
 *         @OA\Schema(
 *
 *             @OA\Property(
 *                 property="format",
 *                 type="string",
 *                 example="full",
 *                 description="Formato da exportação (full, summarized, detailed etc.)"
 *             ),
 *             @OA\Property(
 *                 property="extension",
 *                 type="string",
 *                 example="xlsx",
 *                 description="Extensão do arquivo (xlsx, xls, csv)"
 *             )
 *         )
 *     }
 * )
 */
class ExportRequest extends ListRequest
{
    public function rules(): array
    {
        return [
            ...parent::rules(),
            'format' => 'nullable|string',
            'extension' => 'nullable|string|in:xlsx,xls,csv',
        ];
    }
}
