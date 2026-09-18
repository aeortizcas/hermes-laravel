<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\CatPeso;
use App\Models\City;
use App\Models\Client;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Department;
use App\Models\MenuItem;
use App\Models\PaymentMethod;
use App\Models\ProductCategory;
use App\Models\Provider;
use App\Models\ShippingRate;
use App\Models\Subscription;
use App\Models\TrackingCompany;
use App\Models\Unit;
use App\Models\Video;
use App\Models\Weight;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

/**
 * CRUDs ligeros del admin (catálogos legacy). Ahora es schema-driven:
 * cada entidad declara sus campos y el frontend renderiza el formulario
 * y la tabla de forma genérica (igual que los CRUDs model/view del legacy).
 */
class CatalogController extends Controller
{
    /**
     * Definición de cada catálogo.
     *  - model     : clase Eloquent
     *  - estado    : columna de estado (null si no tiene)
     *  - fields    : [key, label, type (text|number|select|date), options?]
     *  - columns   : columnas a mostrar en la tabla (por defecto = fields)
     */
    private function definitions(): array
    {
        $currencies = fn () => Currency::query()->orderBy('moneda')->get()
            ->map(fn ($c) => ['value' => (int) $c->id, 'label' => (string) $c->moneda])->values();
        $units = fn () => Unit::query()->orderBy('sunidadmedida')->get()
            ->map(fn ($c) => ['value' => (int) $c->id, 'label' => (string) $c->sunidadmedida])->values();
        $companies = fn () => TrackingCompany::query()->orderBy('scompany')->get()
            ->map(fn ($c) => ['value' => (int) $c->id, 'label' => (string) $c->scompany])->values();
        $countries = fn () => Country::query()->orderBy('sPais')->get()
            ->map(fn ($c) => ['value' => (int) $c->id, 'label' => (string) $c->sPais])->values();
        $cities = fn () => City::query()->orderBy('sCiudad')->get()
            ->map(fn ($c) => ['value' => (int) $c->id, 'label' => (string) $c->sCiudad])->values();
        $categories = fn () => ProductCategory::query()->orderBy('name')->get()
            ->map(fn ($c) => ['value' => (int) $c->id, 'label' => (string) $c->name])->values();
        $clients = fn () => Client::query()->orderBy('name')->get()
            ->map(fn ($c) => ['value' => (int) $c->id, 'label' => (string) $c->name])->values();

        return [
            'categorias' => [
                'title' => 'Categorías de producto',
                'model' => ProductCategory::class,
                'estado' => 'estado',
                'fields' => [
                    ['key' => 'name', 'label' => 'Nombre', 'type' => 'text'],
                ],
            ],
            'empresas' => [
                'title' => 'Empresas de envío',
                'model' => TrackingCompany::class,
                'estado' => 'estado',
                'fields' => [
                    ['key' => 'scompany', 'label' => 'Nombre', 'type' => 'text'],
                ],
            ],
            'pagos' => [
                'title' => 'Métodos de pago',
                'model' => PaymentMethod::class,
                'estado' => 'estado',
                'fields' => [
                    ['key' => 'pago', 'label' => 'Método', 'type' => 'text'],
                ],
            ],
            'ciudades' => [
                'title' => 'Ciudades',
                'model' => City::class,
                'estado' => 'sEstado',
                'fields' => [
                    ['key' => 'sCiudad', 'label' => 'Ciudad', 'type' => 'text'],
                    ['key' => 'iddepartament', 'label' => 'Departamento', 'type' => 'select', 'options' => $this->departmentsOptions()],
                ],
            ],
            'bancos' => [
                'title' => 'Bancos',
                'model' => Bank::class,
                'estado' => 'estado',
                'fields' => [
                    ['key' => 'banco', 'label' => 'Banco', 'type' => 'text'],
                    ['key' => 'idmoneda', 'label' => 'Moneda', 'type' => 'select', 'options' => $currencies],
                ],
            ],
            'monedas' => [
                'title' => 'Monedas',
                'model' => Currency::class,
                'estado' => 'estado',
                'fields' => [
                    ['key' => 'moneda', 'label' => 'Moneda', 'type' => 'text'],
                ],
            ],
            'unidades' => [
                'title' => 'Unidades de medida',
                'model' => Unit::class,
                'estado' => 'estado',
                'fields' => [
                    ['key' => 'sunidadmedida', 'label' => 'Unidad', 'type' => 'text'],
                ],
            ],
            'pesos' => [
                'title' => 'Pesos de envío',
                'model' => Weight::class,
                'estado' => 'estado',
                'fields' => [
                    ['key' => 'npeso', 'label' => 'Peso', 'type' => 'number'],
                    ['key' => 'idunidadmedida', 'label' => 'Unidad', 'type' => 'select', 'options' => $units],
                    ['key' => 'idtracking_emp', 'label' => 'Empresa', 'type' => 'select', 'options' => $companies],
                    ['key' => 'ncoste', 'label' => 'Costo', 'type' => 'number'],
                ],
            ],
            'catpeso' => [
                'title' => 'Categorías de peso',
                'model' => CatPeso::class,
                'estado' => 'estado',
                'fields' => [
                    ['key' => 'scatpeso', 'label' => 'Categoría', 'type' => 'text'],
                ],
            ],
            'proveedores' => [
                'title' => 'Proveedores',
                'model' => Provider::class,
                'estado' => 'sEstado',
                'fields' => [
                    ['key' => 'sProveedor', 'label' => 'Proveedor', 'type' => 'text'],
                    ['key' => 'sCodigo', 'label' => 'Código', 'type' => 'text'],
                    ['key' => 'sTelefono', 'label' => 'Teléfono', 'type' => 'text'],
                    ['key' => 'sEmail', 'label' => 'Email', 'type' => 'text'],
                    ['key' => 'sDireccion', 'label' => 'Dirección', 'type' => 'text'],
                    ['key' => 'idpais', 'label' => 'País', 'type' => 'select', 'options' => $countries],
                ],
            ],
            'paises' => [
                'title' => 'Países',
                'model' => Country::class,
                'estado' => 'sEstado',
                'fields' => [
                    ['key' => 'sPais', 'label' => 'País', 'type' => 'text'],
                ],
            ],
            'departamentos' => [
                'title' => 'Departamentos',
                'model' => Department::class,
                'estado' => 'sEstado',
                'fields' => [
                    ['key' => 'sDepartamento', 'label' => 'Departamento', 'type' => 'text'],
                    ['key' => 'idpais', 'label' => 'País', 'type' => 'select', 'options' => $countries],
                ],
            ],
            'protenvios' => [
                'title' => 'Tarifas de envío',
                'model' => ShippingRate::class,
                'estado' => 'estado',
                'fields' => [
                    ['key' => 'idmunicipio', 'label' => 'Ciudad', 'type' => 'select', 'options' => $cities],
                    ['key' => 'idunidadmedida', 'label' => 'Unidad', 'type' => 'select', 'options' => $units],
                    ['key' => 'idcia', 'label' => 'Empresa', 'type' => 'select', 'options' => $companies],
                    ['key' => 'precio', 'label' => 'Precio base', 'type' => 'number'],
                    ['key' => 'p1', 'label' => 'Desde peso', 'type' => 'number'],
                    ['key' => 'p2', 'label' => 'Hasta peso', 'type' => 'number'],
                ],
            ],
            'menu_items' => [
                'title' => 'Ítems de menú',
                'model' => MenuItem::class,
                'estado' => null,
                'fields' => [
                    ['key' => 'name', 'label' => 'Nombre', 'type' => 'text'],
                    ['key' => 'link', 'label' => 'Enlace', 'type' => 'text'],
                    ['key' => 'category_id', 'label' => 'Categoría', 'type' => 'select', 'options' => $categories],
                ],
            ],
            'videos' => [
                'title' => 'Videos de YouTube',
                'model' => Video::class,
                'estado' => null,
                'fields' => [
                    ['key' => 'video_id', 'label' => 'ID del video', 'type' => 'text'],
                    ['key' => 'title', 'label' => 'Título', 'type' => 'text'],
                    ['key' => 'start_time', 'label' => 'Inicio (seg)', 'type' => 'number'],
                    ['key' => 'end_time', 'label' => 'Fin (seg)', 'type' => 'number'],
                ],
            ],
            'suscripciones' => [
                'title' => 'Suscripciones',
                'model' => Subscription::class,
                'estado' => null,
                'fields' => [
                    ['key' => 'user_id', 'label' => 'Cliente', 'type' => 'select', 'options' => $clients],
                    ['key' => 'membership_type', 'label' => 'Tipo', 'type' => 'select', 'options' => [
                        ['value' => 'personal', 'label' => 'Personal'],
                        ['value' => 'business', 'label' => 'Empresarial'],
                    ]],
                    ['key' => 'start_date', 'label' => 'Inicio', 'type' => 'date'],
                    ['key' => 'end_date', 'label' => 'Fin', 'type' => 'date'],
                ],
            ],
            'clientes' => [
                'title' => 'Clientes',
                'model' => Client::class,
                'estado' => 'sEstado',
                'fields' => [
                    ['key' => 'name', 'label' => 'Nombre', 'type' => 'text'],
                    ['key' => 'email', 'label' => 'Email', 'type' => 'text'],
                ],
                'readonly' => true,
            ],
        ];
    }

    private function departmentsOptions(): array
    {
        return Department::query()->orderBy('sDepartamento')->get()
            ->map(fn ($c) => ['value' => (int) $c->id, 'label' => (string) $c->sDepartamento])->values()->all();
    }

    public function index(string $entity): Response
    {
        $def = $this->definitions()[$entity] ?? abort(404);

        $items = $def['model']::query()
            ->orderByDesc('id')
            ->limit(300)
            ->get()
            ->map(fn ($row) => $this->serialize($row, $def))
            ->values();

        $fields = collect($def['fields'])->map(function ($f) {
            if (isset($f['options']) && is_callable($f['options'])) {
                $f['options'] = $f['options']();
            }

            return $f;
        })->values();

        return Inertia::render('Admin/Catalog/Index', [
            'entity' => $entity,
            'title' => $def['title'],
            'fields' => $fields,
            'items' => $items,
            'readonly' => $def['readonly'] ?? false,
        ]);
    }

    public function store(Request $request, string $entity): RedirectResponse
    {
        $def = $this->definitions()[$entity] ?? abort(404);

        if ($def['readonly'] ?? false) {
            abort(403);
        }

        $data = $this->validated($request, $def);
        if ($def['estado']) {
            $data[$def['estado']] = 1;
        }

        $def['model']::query()->create($data);

        return redirect()->back()->with('success', 'Elemento creado.');
    }

    public function update(Request $request, string $entity, int $id): RedirectResponse
    {
        $def = $this->definitions()[$entity] ?? abort(404);

        if ($def['readonly'] ?? false) {
            abort(403);
        }

        $data = $this->validated($request, $def);
        $def['model']::query()->findOrFail($id)->update($data);

        return redirect()->back()->with('success', 'Elemento actualizado.');
    }

    public function toggle(string $entity, int $id): RedirectResponse
    {
        $def = $this->definitions()[$entity] ?? abort(404);

        $row = $def['model']::query()->findOrFail($id);

        if ($def['estado']) {
            $row->update([$def['estado'] => 0]);
        } else {
            $row->delete();
        }

        return redirect()->back()->with('success', 'Elemento desactivado.');
    }

    private function validated(Request $request, array $def): array
    {
        $rules = [];
        foreach ($def['fields'] as $field) {
            if (($field['type'] ?? 'text') === 'number') {
                $rules[$field['key']] = ['nullable', 'numeric'];
            } else {
                $rules[$field['key']] = ['nullable', 'string', 'max:255'];
            }
        }

        return $request->validate($rules);
    }

    private function serialize($row, array $def): array
    {
        $out = ['id' => (int) $row->id];
        foreach ($def['fields'] as $field) {
            $out[$field['key']] = $row->{$field['key']} ?? null;
        }
        if ($def['estado']) {
            $out['estado'] = (int) ($row->{$def['estado']} ?? 1);
        }

        return $out;
    }
}