<?php

namespace App\Support\Dashboard;

class DocumentWidget extends Widget
{

    public function title(): string
    {
        return trans('Documents');
    }

    
    protected function view(): string
    {
        return 'dashboard/documents.twig';
    }

    protected function data(): array
    {
        $sql = "
            SELECT
                t.description AS type,
                COUNT(*) AS count
            FROM
                document d
            JOIN
                document_type t ON d.document_type_id = t.id
            GROUP BY
                t.description
            ORDER BY
                COUNT(*) DESC
        ";

        return $this->fetch($sql);
    }
}