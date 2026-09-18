<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Page;
use App\Models\PagePermission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Páginas del sistema y niveles (permisos usuario->página).
 * Legacy: controller/PageController.php + NivelesController.php (user_page_permissions).
 */
class PagesController extends Controller
{
    public function pages(): Response
    {
        return Inertia::render('Admin/Pages/Index', [
            'pages' => Page::query()->orderBy('name')->get()
                ->map(fn (Page $p) => ['id' => (int) $p->id, 'name' => (string) $p->name, 'description' => (string) ($p->description ?? '')])
                ->values(),
        ]);
    }

    public function storePage(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:pages,name'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        Page::query()->create($data);

        return redirect()->back()->with('success', 'Página creada.');
    }

    public function updatePage(Request $request, int $id): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:pages,name,'.$id],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        Page::query()->findOrFail($id)->update($data);

        return redirect()->back()->with('success', 'Página actualizada.');
    }

    public function destroyPage(int $id): RedirectResponse
    {
        Page::query()->findOrFail($id)->delete();
        PagePermission::query()->where('page_id', $id)->delete();

        return redirect()->back()->with('success', 'Página eliminada.');
    }

    public function niveles(): Response
    {
        return Inertia::render('Admin/Pages/Niveles', [
            'permissions' => PagePermission::query()->with(['user', 'page'])->orderBy('id')->get()
                ->map(fn (PagePermission $p) => [
                    'id' => (int) $p->id,
                    'user_id' => (int) $p->user_id,
                    'user_name' => (string) ($p->user?->name ?? ''),
                    'page_id' => (int) $p->page_id,
                    'page_name' => (string) ($p->page?->name ?? ''),
                    'access_type' => (string) $p->access_type,
                ])
                ->values(),
            'users' => Client::query()->orderBy('name')->get()
                ->map(fn ($u) => ['value' => (int) $u->id, 'label' => (string) $u->name])->values(),
            'pages' => Page::query()->orderBy('name')->get()
                ->map(fn ($p) => ['value' => (int) $p->id, 'label' => (string) $p->name])->values(),
        ]);
    }

    public function storeNivel(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'user_id' => ['required', 'integer'],
            'page_id' => ['required', 'integer'],
            'access_type' => ['required', 'in:allow,deny'],
        ]);

        PagePermission::query()->updateOrCreate(
            ['user_id' => $data['user_id'], 'page_id' => $data['page_id']],
            ['access_type' => $data['access_type']]
        );

        return redirect()->back()->with('success', 'Permiso asignado.');
    }

    public function destroyNivel(int $id): RedirectResponse
    {
        PagePermission::query()->findOrFail($id)->delete();

        return redirect()->back()->with('success', 'Permiso eliminado.');
    }
}