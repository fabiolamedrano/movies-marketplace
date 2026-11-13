<?php
// app/Http/Requests/Concerns/StripsHtml.php
namespace App\Http\Requests\Concerns;

trait StripsHtml
{
    /** @return list<string> campos de texto a limpiar */
    abstract protected function stripFields(): array;

    // se ejecuta antes de validar para que lo que se guarde ya este limpio
    protected function prepareForValidation(): void
    {
        $clean = [];

        foreach ($this->stripFields() as $field) {
            if (is_string($this->input($field))) {
                $clean[$field] = trim(strip_tags($this->input($field)));
            }
        }

        $this->merge($clean);
    }
}