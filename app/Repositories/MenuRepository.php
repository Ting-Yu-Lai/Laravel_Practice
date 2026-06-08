<?php

namespace App\Repositories;
use App\Models\Menu;
use App\Repositories\BaseRepository;
class MenuRepository extends BaseRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        protected Menu $menu
    ) {
        parent::__construct($menu);
    }

    /**
     * 取得所有可供應的菜單列表
     * 
     * 篩選資料庫中所有啟用的餐點項目
     * 
     * @return \Illuminate\Database\Eloquent\Collection<\App\Models\Menu> 可供應的菜單模型集合。
     */
    public function getAllAvailable() {
        return $this->menu->where('is_available', true)->get();
    }

    /**
     * 根據 ID 取得菜單
     * 
     * @param int $id 菜單的唯一標識符，通常是資料庫中的主鍵。
     * @return \App\Models\Menu|null 如果找到對應的菜單，返回菜單模型實例；如果未找到，返回 null。
     */
    public function getAvailableMenusById(int $id) {
        return $this->menu->where('is_available', true)->where('id', $id)->firstOrFail();
    }
}
