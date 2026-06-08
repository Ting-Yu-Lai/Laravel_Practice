<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use App\Services\MenuService;
use App\Http\Requests\Menu\StoreRequest;
use App\Http\Requests\Menu\UpdateRequest;
use Illuminate\Http\JsonResponse;

class MenuController extends Controller
{
    public function __construct(
        protected MenuService $menuService,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $data = $this->menuService->getAllAvailableMenus();
        return response()->json(
            [
                'code' => 200,
                'status' => 'success',
                'data' => $data 
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     * 
     * @param StoreRequest $request 包含菜單創建所需資料的請求對象，經過驗證後可用於創建新菜單。
     * @return JsonResponse 包含創建結果的 JSON 響應對象，通常包含狀態碼、訊息和新創建菜單的資料。
     */
    public function store(StoreRequest $request): JsonResponse
    {
        //
        $validatedData = $request->validated();

        $menu = $this->menuService->storeMenu($validatedData);

        return response()->json(
            [
                'code' => 200,
                'status' => 'success',
                'data' => $menu
            ]
        );
    }

    /**
     * Display the specified resource.
     * 
     * @param int $id 菜單的唯一標識符，通常是資料庫中的主鍵。
     * @return JsonResponse 包含菜單資料的 JSON 響應對象，如果找到對應的菜單，返回菜單資料；如果未找到，返回錯誤訊息。
     */
    public function show(int $id)
    {
        $menu = $this->menuService->getAvailableMenusById($id);

        return response()->json(
            [
                'code' => 200,
                'status' => 'success',
                'data' => $menu
            ]
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Menu $menu)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     * 
     * @param UpdateRequest $request 包含菜單更新所需資料的請求對象，經過驗證後可用於更新現有菜單。
     * @return JsonResponse 包含更新結果的 JSON 響應對象，通常包含狀態碼、訊息和更新後的菜單資料。
     */
    public function update(UpdateRequest $request, int $id): JsonResponse
    {
        $validatedData = $request->validated();
        $menu = $this->menuService->updateMenu($id, $validatedData);

        return response()->json(
            [
                'code' => 200,
                'status' => 'success',
                'data' => $menu
            ]
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        //
        $menu = $this->menuService->destroy($id);
    }
}
