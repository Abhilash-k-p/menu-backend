<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {

        $menus = Cache::remember('menus_hierarchy', 3600, function () {
            $depth = Menu::max('depth') ?? 0;
            // Fetch root Menus and load relationships recursively
            return Menu::rootMenus()->with(
                [$this->buildRecursiveRelationship('children', $depth),
                    $this->buildRecursiveRelationship('parent', $depth)
                ])->get();
        });

        return response()->json($menus);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:menus,id',
            'depth' => 'nullable|integer'
        ]);

        $menu = Menu::create($validatedData);

        Cache::forget('menus_hierarchy'); // Clear cached hierarchy on creation
        $pathToNode = $this->pathToNode($menu->id);

        return response()->json(compact('menu', 'pathToNode'), 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $menu = Menu::findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $menu->update($validatedData);

        Cache::forget('menus_hierarchy'); // Clear cached hierarchy on update
        $pathToNode = $this->pathToNode($menu->id);

        return response()->json(compact('menu', 'pathToNode'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        $menu = Menu::findOrFail($id);

        // Collect the path to the node before deletion
        $pathToNode = $this->pathToNode($menu->id);
        $pathToNode = array_shift($pathToNode); // remove current deleting node from path

        $menu->delete();

        Cache::forget('menus_hierarchy'); // Clear cached hierarchy on deletion
        return response()->json(compact('pathToNode'), 204);
    }

    /**
     * get path to given node
     *
     * @param string $id
     * @return array
     */
    public function pathToNode(string $id): array
    {
        $menu = Menu::findOrFail($id);
        $path[] = $menu->id;
        while ($menu->parent_id) {
            $menu = Menu::find($menu->parent_id);
            $path[] = $menu->id;
        }
        return array_reverse($path);
    }

    /**
     * return children relation
     *
     * @param string $relation
     * @param int $depth
     * @return string
     */
    private function buildRecursiveRelationship(string $relation, int $depth): string
    {
        for ($i = 1; $i < $depth; $i++) {
            $relation .= '.' . $relation;
        }

        return $relation;
    }
}
