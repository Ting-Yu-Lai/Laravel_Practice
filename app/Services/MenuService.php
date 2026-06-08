<?php

namespace App\Services;
use App\Repositories\MenuRepository;

class MenuService
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        protected MenuRepository $menuRepository
    ) {}

    /**
     * 取得所有可供應的菜單列表
     * 
     * @return \Illuminate\Database\Eloquent\Collection<\App\Models\Menu>
     */
    public function getAllAvailableMenus() {
        return $this->menuRepository->getAllAvailable();
    }

    /**
     * 儲存新菜單
     * 
     * @param array $data 包含菜單創建所需資料的陣列，通常包含名稱、描述、價格、庫存和可用性等資訊。
     * @return \App\Models\Menu 新創建的菜單模型實例，包含創建後的菜單資料。
     */
    public function storeMenu(array $data) {
        return $this->menuRepository->create($data);
    }

    /**
     * 根據 ID 取得可供應的菜單
     * 
     * @param int $id 菜單的唯一標識符，通常是資料庫中的主鍵。
     * @return \App\Models\Menu|null 如果找到對應的菜單，返回菜單模型實例；如果未找到，返回 null。
     */
    public function getAvailableMenusById(int $id) {
        $menu = $this->menuRepository->getAvailableMenusById($id);
        return $menu;
    }

    public function updateMenu(int $id, array $data) {
        $menu = $this->menuRepository->findOrFail($id);
        return $this->menuRepository->update($menu, $data);
    }

    /**
     * 刪除指定的菜單
     * 
     * @param int $id 菜單的唯一標識符，通常是資料庫中的主鍵。
     * @return bool 刪除成功返回 true，失敗返回 false。
     */
    public function destroy(int $id) {
        $menu = $this->menuRepository->findOrFail($id);
        return $this->menuRepository->delete($menu);
    }
}
