# 練習 1-2 實作回顧

> 對應練習：基本 CRUD 手寫（Menu 商品）
> Commit 範圍：Menu CRUD 初版實作（MenuController / MenuService / MenuRepository / BaseRepository）

---

## ✅ 做得好的地方

- Migration 欄位定義正確，`description` nullable、`stock` 有 default
- Model `$fillable` 設定完整，沒有 mass assignment 漏洞
- 主動加上 Form Request（`MenuStoreRequest`、`UpdateRequest`）做驗證分離，超出基本要求
- 提前導入 Service / Repository 分層（Phase 3 的概念），架構思維正確
- Route 群組使用 `prefix` + `name`，結構清晰

---

## ❌ 需要修正的 Bug

### 1. `store()` 沒有設定 HTTP status 201

```php
// ❌ HTTP 實際回傳 200，body 裡的 code 只是自定義欄位，不影響協定
return response()->json(['code' => 201, ...]);

// ✅ 第二個參數才是真正的 HTTP status code
return response()->json(['code' => 201, ...], 201);
```

### 2. `destroy()` 是空方法

```php
// ❌ 什麼都沒做，回傳 HTTP 200 + null body
public function destroy(Menu $menu) { // }

// ✅
public function destroy(int $id): JsonResponse
{
    $this->menuService->deleteMenu($id);
    return response()->json(null, 204);
}
```

### 3. `MenuRepository::getAvailableMenusById` 的 `findOrFail` 用法錯誤

```php
// ❌ findOrFail('id', $id) → 第一個參數是主鍵值，不是欄位名稱
//    等於找 WHERE primary_key = 'id'（字串），永遠找不到
return $this->menu->where('is_available', true)->findOrFail('id', $id);

// ✅ Builder 上應該用 firstOrFail()
return $this->menu->where('is_available', true)->where('id', $id)->firstOrFail();
```

---

## ⚠️ 需要注意的設計問題

### 4. Controller constructor 不應注入 Form Request

```php
// ❌ $this->menuStoreRequest 和 $this->updateRequest 從來沒被使用
public function __construct(
    protected MenuService $menuService,
    protected MenuStoreRequest $menuStoreRequest,  // 多餘
    protected UpdateRequest $updateRequest          // 多餘
) {}

// ✅ Form Request 只放在需要它的 method 參數上，不注入 constructor
public function __construct(
    protected MenuService $menuService
) {}
```

**為什麼錯**：Form Request 是 per-request 物件，它的 `authorize()` 和 `rules()` 設計上是在 method 被呼叫時才執行。注入到 constructor 的屬性在整個 request 生命週期內都是同一個實例，且從未透過 `$this->` 存取，純屬死碼。

### 5. `BaseRepository::with()` 永久汙染實例狀態

```php
// ❌ 直接改掉 $this->model，下一次查詢也會帶這個 with
public function with(array|string $relations): static
{
    $this->model = $this->model->with($relations);
    return $this;
}

// ✅ 不修改 $this->model，回傳新的 Builder
public function with(array|string $relations)
{
    return $this->model->with($relations);
}
```

**為什麼危險**：Repository 在 Laravel container 裡是 singleton，`$this->model` 一旦被 `with()` 改掉，之後所有查詢都會帶這個 eager load，直到 process 重啟。這種 bug 在開發環境幾乎看不出來，在高並發或 Laravel Octane 下才會爆炸。`withTrashed()`、`onlyTrashed()` 有同樣問題，一起修。

### 6. `MenuRepository::store()` 重複了 `BaseRepository::create()`

```php
// ❌ store() 和 BaseRepository::create() 做的是完全一樣的事
public function store(array $data) {
    return $this->menu->create($data);
}

// ✅ 直接刪除 store()，改用繼承來的 create()
// MenuService 改呼叫：$this->menuRepository->create($data)
```

---

## 🟢 小細節

- `$validatedDate` 是 typo，應為 `$validatedData`（Date 是日期，Data 是資料）
- `MenuStoreRequest` 的 `description` 缺少 `max:255`，與 `UpdateRequest` 規則不一致；建立時也應限制長度
