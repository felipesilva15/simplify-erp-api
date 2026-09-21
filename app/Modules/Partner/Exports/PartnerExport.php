<?php

namespace App\Modules\Partner\Exports;

use App\Core\Exports\BaseExport;

class PartnerExport extends BaseExport
{
    public function headings(): array
    {
        return [
            'ID',
            'Tipo de parceiro',
            'Nome',
            'Apelido',
            'Tipo de pessoa',
            'Contribuinte',
            'Documento',
            'RG',
            'Órgão emissor',
            'Parceiro desde',
            'Inscrição estadual',
            'Inscrição municipal',
            'Inscrição Suframa',
            'Estado civíl',
            'CBO profissão',
            'Gênero',
            'Data de nascimento',
            'Nome do pai',
            'Documento do pai',
            'Nome da mãe',
            'Documento da mãe',
            'Tipo de chave PIX',
            'Chave PIX',
            'Observações'
        ];
    }

    public function map(mixed $row): array
    {
        return [
            $row->id,
            $row->partner_type_code,
            $row->name,
            $row->trade_name,
            $row->person_type?->label(),
            $row->taxpayer_type?->label(),
            $row->document_number,
            $row->identity_number,
            $row->identity_issuer,
            $row->partner_since?->format('d/m/Y'),
            $row->state_registration,
            $row->municipal_registration,
            $row->suframa_registration,
            $row->marital_status?->label(),
            $row->cbo,
            $row->gender?->label(),
            $row->birth_date?->format('d/m/Y'),
            $row->father_name,
            $row->father_document,
            $row->mother_name,
            $row->mother_document,
            $row->pix_type?->label(),
            $row->pix_key,
            $row->notes
        ];
    }
}