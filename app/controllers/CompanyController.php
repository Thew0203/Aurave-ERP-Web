<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Company;

class CompanyController extends Controller
{
    private function company(): Company
    {
        return new Company();
    }

    public function index(): void
    {
        $list = $this->company()->all();
        $this->view('companies.index', ['pageTitle' => 'Companies', 'companies' => $list]);
    }

    public function create(): void
    {
        $this->view('companies.form', ['pageTitle' => 'Add Company', 'company' => null]);
    }

    public function store(): void
    {
        $name = trim((string) $this->input('name'));
        $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', trim((string) $this->input('slug', $name))));
        $slug = trim($slug, '-') ?: 'company-' . time();
        $this->company()->create([
            'name' => $name,
            'slug' => $slug,
            'email' => trim((string) $this->input('email')),
            'phone' => trim((string) $this->input('phone')),
            'address' => trim((string) $this->input('address')),
            'tax_id' => trim((string) $this->input('tax_id')),
            'is_active' => 1,
        ]);
        $this->redirect($this->baseUrl() . '/companies');
    }

    public function edit(string $id): void
    {
        $company = $this->company()->find((int) $id);
        if (!$company) {
            $this->redirect($this->baseUrl() . '/companies');
            return;
        }
        $this->view('companies.form', ['pageTitle' => 'Edit Company', 'company' => $company]);
    }

    public function update(string $id): void
    {
        $company = $this->company()->find((int) $id);
        if (!$company) {
            $this->redirect($this->baseUrl() . '/companies');
            return;
        }
        $this->company()->update((int) $id, [
            'name' => trim((string) $this->input('name')),
            'slug' => trim((string) $this->input('slug')),
            'email' => trim((string) $this->input('email')),
            'phone' => trim((string) $this->input('phone')),
            'address' => trim((string) $this->input('address')),
            'tax_id' => trim((string) $this->input('tax_id')),
            'is_active' => $this->input('is_active') ? 1 : 0,
        ]);
        $this->redirect($this->baseUrl() . '/companies');
    }
}
