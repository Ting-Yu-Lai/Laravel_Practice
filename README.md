# Laravel 工程師訓練計畫

> **專案說明**：此專案是一個以「咖啡廳系統」為作品集主題的 Laravel 12 學習專案，目標是從 Junior 成長為能應付中小型公司後端面試的 Mid-level Laravel 全端工程師。

---

## 🎯 訓練目標

1. 能通過 Laravel / Backend 工程師技術面試
2. 強化「系統架構 + 資料流 + API 設計」理解
3. 補強偏弱的：程式碼手寫能力、架構拆分（Service / Repository）、RESTful API 設計、Debug 與問題拆解
4. 能用「作品集專案」講出工程深度，不只是 CRUD

---

## Phase 1：基礎手寫能力（必須會）

### 概念目標

理解並能徒手寫出 Laravel 的請求生命週期與 CRUD 流程：

```
Request → Middleware → Route → Controller → Request(Validation) → Service → Repository → Model → Response
```

---

### 練習 1-1：Route / Controller / Request 生命週期

**題目**：不看文件，說明並手寫一個 `GET /products` 路由，回傳所有商品列表（JSON）。

**你應該手寫的範圍**：
- `routes/api.php` 的路由定義
- `ProductController@index` 方法
- Eloquent 查詢 `Product::all()`
- 正確的 `response()->json()` 回傳

**常見錯誤**：
- 忘記在 `api.php` 加路由，卻在 `web.php` 加（Session vs Stateless 差異）
- Controller 直接 `echo` 而非 `return response()->json()`
- 忘記 `use App\Models\Product;`

**面試會怎麼問**：
- 「一個 Request 進來之後，Laravel 做了什麼？」（要講到 Kernel、Middleware Pipeline）
- 「`web.php` 和 `api.php` 的差別是什麼？」

---

### 練習 1-2：基本 CRUD 手寫（以 Menu 商品為例）

**題目**：為咖啡廳「菜單商品」實作完整 CRUD API。

| 操作 | Method | URI |
|------|--------|-----|
| 列表 | GET | `/api/menus` |
| 詳情 | GET | `/api/menus/{id}` |
| 新增 | POST | `/api/menus` |
| 更新 | PUT | `/api/menus/{id}` |
| 刪除 | DELETE | `/api/menus/{id}` |

**你應該手寫的範圍**：
```php
// Migration: database/migrations/xxxx_create_menus_table.php
Schema::create('menus', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->text('description')->nullable();
    $table->decimal('price', 8, 2);
    $table->integer('stock')->default(0);
    $table->boolean('is_available')->default(true);
    $table->timestamps();
});

// Model: app/Models/Menu.php
protected $fillable = ['name', 'description', 'price', 'stock', 'is_available'];

// Controller: app/Http/Controllers/MenuController.php (完整 5 個方法)
```

**常見錯誤**：
- `store()` 忘記 `$request->validate()`
- `update()` 用 `Menu::create()` 而非 `$menu->update()`
- 刪除後忘記確認回傳 `204 No Content`
- `$fillable` 沒設定，導致 mass assignment 錯誤

**面試會怎麼問**：
- 「`findOrFail` 和 `find` 的差別？」
- 「`firstOrCreate` 和 `updateOrCreate` 什麼時候用？」
- 「如果 `id` 不存在，你的 API 回什麼 HTTP status？」

---

> 📋 **[練習 1-2 實作回顧 →](docs/review-phase1-2.md)**

---

### 練習 1-3：SQL 與 Eloquent 對照

**題目**：用 Eloquent 重現以下 SQL，並說明效能差異。

```sql
-- 查詢所有可用商品，依價格排序
SELECT * FROM menus WHERE is_available = 1 ORDER BY price ASC;

-- 查詢某訂單的所有商品名稱與數量
SELECT menus.name, order_items.quantity
FROM order_items
JOIN menus ON order_items.menu_id = menus.id
WHERE order_items.order_id = 5;

-- 統計每個商品的訂購次數
SELECT menu_id, COUNT(*) as total_orders
FROM order_items
GROUP BY menu_id
HAVING total_orders > 10;
```

**Eloquent 對照**：
```php
// 查詢 1
Menu::where('is_available', true)->orderBy('price')->get();

// 查詢 2
OrderItem::with('menu')->where('order_id', 5)->get();

// 查詢 3
OrderItem::select('menu_id', DB::raw('COUNT(*) as total_orders'))
    ->groupBy('menu_id')
    ->having('total_orders', '>', 10)
    ->get();
```

**🔥 實戰 Coding 練習**：新增 `Menu` model 加上 `scopeAvailable()` scope，讓 `Menu::available()->get()` 可用。

**🔥 面試官陷阱題**：「N+1 問題是什麼？你怎麼偵測它？怎麼修？」（答：`with()` eager loading，用 Laravel Debugbar 或 `DB::listen` 偵測）

**🔥 答不出來代表哪裡不懂**：不懂 ORM 和 SQL 的對應關係，沒有真正理解 Eloquent 底層。

**🔥 如何補強**：打開 `DB::enableQueryLog()` 看 Eloquent 產生的 SQL，逐一比對。

---

## Phase 2：API 與前後端資料流（重點）

### 概念目標

理解 Axios 如何與 Laravel API 溝通，Token vs Session 差異，JSON vs FormData 差異，RESTful 設計規範。

---

### Token vs Session 差異

| 比較點 | Session（Cookie） | Token（Sanctum/JWT） |
|--------|-----------------|---------------------|
| 儲存位置 | Server 端 Session | Client 端（localStorage/Header）|
| 適用場景 | 傳統 Web（同網域） | SPA / Mobile API |
| CSRF 保護 | 需要 | 不需要（Header 驗證）|
| Laravel 實作 | `Auth::login()` | `createToken()` |

### FormData vs JSON 差異

```javascript
// JSON（最常用，後端 $request->input()）
axios.post('/api/menus', { name: '拿鐵', price: 150 })

// FormData（有檔案上傳時用）
const form = new FormData()
form.append('name', '拿鐵')
form.append('image', file)
axios.post('/api/menus', form, { headers: { 'Content-Type': 'multipart/form-data' } })
```

---

### 咖啡廳系統 API 模組設計

#### 1. 使用者登入 `POST /api/login`

**Request**：
```json
{ "email": "user@example.com", "password": "password123" }
```

**Response（成功 200）**：
```json
{
  "token": "1|abc123...",
  "user": { "id": 1, "name": "Kayn", "email": "user@example.com" }
}
```

**Controller 寫法**：
```php
public function login(Request $request)
{
    $credentials = $request->validate([
        'email'    => 'required|email',
        'password' => 'required',
    ]);

    if (!Auth::attempt($credentials)) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    $token = $request->user()->createToken('api-token')->plainTextToken;
    return response()->json(['token' => $token, 'user' => $request->user()]);
}
```

**面試追問**：「`createToken` 存在哪裡？Token 怎麼失效？」（答：`personal_access_tokens` 表，呼叫 `$token->delete()` 或 `tokens()->delete()`）

---

#### 2. 菜單 CRUD `GET|POST|PUT|DELETE /api/menus`

**POST /api/menus Request**：
```json
{ "name": "卡布奇諾", "description": "經典義式咖啡", "price": 130, "stock": 50 }
```

**Response（201 Created）**：
```json
{
  "id": 3, "name": "卡布奇諾", "price": "130.00", "stock": 50, "is_available": true
}
```

---

#### 3. 建立訂單 `POST /api/orders`

**Request**：
```json
{
  "items": [
    { "menu_id": 1, "quantity": 2 },
    { "menu_id": 3, "quantity": 1 }
  ],
  "note": "少冰"
}
```

**Response（201 Created）**：
```json
{
  "order_id": 10,
  "status": "pending",
  "total_price": "410.00",
  "items": [
    { "name": "拿鐵", "quantity": 2, "subtotal": "260.00" },
    { "name": "卡布奇諾", "quantity": 1, "subtotal": "130.00" }
  ]
}
```

**🔥 實戰 Coding 練習**：實作 `OrderController@store`，要在同一個 DB transaction 中建立 `Order` 和多筆 `OrderItem`，任一失敗則 rollback。

**🔥 面試官陷阱題**：「如果同時有 100 人搶購庫存只剩 1 的商品，你怎麼處理？」（答：`lockForUpdate()` 悲觀鎖，或 `decrement` + check）

**🔥 答不出來代表哪裡不懂**：沒有真正理解 DB Transaction 和 Race Condition。

**🔥 如何補強**：手寫 `DB::transaction()` 包裹多步驟寫入，用 Tinker 模擬並發情境。

---

## Phase 3：架構能力（Service / Repository）

### 為什麼需要分層

**肥 Controller 的問題**：邏輯混在一起、難測試、難維護、無法複用。

```
Controller → (直接塞一堆業務邏輯) → Model
```

**目標分層**：
```
Controller（薄，只做 HTTP 進出）
    → Service（業務邏輯）
        → Repository（資料存取）
            → Model（Eloquent）
```

---

### Before（肥 Controller）

```php
// ❌ 壞的寫法：OrderController@store 塞了所有邏輯
public function store(Request $request)
{
    $request->validate(['items' => 'required|array']);

    $total = 0;
    foreach ($request->items as $item) {
        $menu = Menu::find($item['menu_id']);
        if ($menu->stock < $item['quantity']) {
            return response()->json(['message' => '庫存不足'], 422);
        }
        $total += $menu->price * $item['quantity'];
    }

    $order = Order::create(['user_id' => auth()->id(), 'total_price' => $total, 'status' => 'pending']);

    foreach ($request->items as $item) {
        $menu = Menu::find($item['menu_id']);
        OrderItem::create(['order_id' => $order->id, 'menu_id' => $item['menu_id'], 'quantity' => $item['quantity'], 'price' => $menu->price]);
        $menu->decrement('stock', $item['quantity']);
    }

    return response()->json($order, 201);
}
```

---

### After（乾淨架構）

```php
// ✅ Controller 只做 HTTP 進出
public function store(StoreOrderRequest $request)
{
    $order = $this->orderService->createOrder(auth()->user(), $request->validated());
    return response()->json($order, 201);
}

// app/Services/OrderService.php — 業務邏輯
class OrderService
{
    public function __construct(private OrderRepository $orderRepo, private MenuRepository $menuRepo) {}

    public function createOrder(User $user, array $data): Order
    {
        return DB::transaction(function () use ($user, $data) {
            $total = 0;
            $items = [];

            foreach ($data['items'] as $item) {
                $menu = $this->menuRepo->findOrFail($item['menu_id']);
                throw_if($menu->stock < $item['quantity'], new InsufficientStockException());
                $total += $menu->price * $item['quantity'];
                $items[] = ['menu' => $menu, 'quantity' => $item['quantity']];
            }

            $order = $this->orderRepo->create($user->id, $total);

            foreach ($items as $item) {
                $this->orderRepo->addItem($order, $item['menu'], $item['quantity']);
                $this->menuRepo->decrementStock($item['menu'], $item['quantity']);
            }

            return $order->load('items.menu');
        });
    }
}

// app/Repositories/OrderRepository.php — 資料存取
class OrderRepository
{
    public function create(int $userId, float $total): Order
    {
        return Order::create(['user_id' => $userId, 'total_price' => $total, 'status' => 'pending']);
    }

    public function addItem(Order $order, Menu $menu, int $quantity): OrderItem
    {
        return $order->items()->create(['menu_id' => $menu->id, 'quantity' => $quantity, 'price' => $menu->price]);
    }
}
```

**拆分理由**：Controller 無業務邏輯（可換成 CLI 呼叫）；Service 可單獨測試；Repository 可換實作（換 DB、加 Cache）。

**🔥 實戰 Coding 練習**：把你寫好的 `MenuController` 的 `store` / `update` 邏輯搬到 `MenuService`，Controller 只留 validate + call + return。

**🔥 面試官陷阱題**：「Repository Pattern 在 Laravel 有必要嗎？Eloquent 本身已經是 Repository 了吧？」（答：看團隊規模和測試需求，小專案可以略過，但 Service Layer 幾乎總是值得的）

**🔥 答不出來代表哪裡不懂**：沒有思考過「可測試性」和「依賴倒置」的概念。

**🔥 如何補強**：把現有的一個 controller 方法完整重構，寫 unit test 只測 Service，mock Repository。

---

## Phase 4：系統設計 + 面試實戰

### 咖啡廳訂單系統設計

#### DB Schema

```sql
-- 使用者
users: id, name, email, password, role(enum: customer/staff/admin), timestamps

-- 菜單商品
menus: id, name, description, price(decimal), stock(int), category(string), is_available(bool), timestamps

-- 訂單主表
orders: id, user_id(FK), status(enum: pending/confirmed/preparing/completed/cancelled),
        total_price(decimal), note(text nullable), paid_at(timestamp nullable), timestamps

-- 訂單明細
order_items: id, order_id(FK), menu_id(FK), quantity(int), price(decimal at time of order), timestamps
```

**設計重點**：`order_items.price` 記錄下單當下的價格（商品漲價不影響歷史訂單）。

---

#### API 設計（RESTful）

```
POST   /api/auth/login
POST   /api/auth/logout

GET    /api/menus              # 公開，可 cache
GET    /api/menus/{id}
POST   /api/menus              # 需 staff 權限
PUT    /api/menus/{id}
DELETE /api/menus/{id}

POST   /api/orders             # 建立訂單（需登入）
GET    /api/orders             # 查自己的訂單
GET    /api/orders/{id}
PATCH  /api/orders/{id}/status # staff 更新訂單狀態
```

---

#### 系統架構描述

```
[Client: Vue/React/Axios]
        ↓ HTTPS
[Nginx / Laravel API]
    ├── Sanctum Token Auth
    ├── Route → Controller → Service → Repository → MySQL
    ├── Queue Worker（訂單通知、Email）
    └── Cache（Redis）
            ├── 菜單列表快取（TTL 5min）
            └── 庫存 atomic decrement
```

---

#### 延展性問題

| 問題 | 解法 |
|------|------|
| 菜單查詢頻繁（高 QPS） | Redis cache `menus:all`，有異動時 cache invalidate |
| 訂單庫存 Race Condition | `Redis::decr()` atomic，或 MySQL `SELECT ... FOR UPDATE` |
| 訂單完成發 Email 太慢 | 丟 Queue Job（`OrderConfirmedJob`），非同步處理 |
| 系統擴展到多台 Server | Session 改 Redis，Queue 改 Redis，Stateless API |

---

#### 面試官追問 10 題

1. 訂單狀態流轉你怎麼設計？什麼狀態可以轉什麼狀態？
2. 如果要加入「優惠碼折扣」，你的 schema 和 service 怎麼改？
3. `pending` 訂單 30 分鐘未付款自動取消，你怎麼實作？
4. 多人同時下單同一個庫存剩 1 的商品，你怎麼保證不超賣？
5. API 如何防止未登入用戶建立訂單？Token 過期怎麼處理？
6. 菜單有分類，如何設計讓分類可以多層級（大類/小類）？
7. 你的 Order API 如何做分頁？`cursor` 分頁和 `offset` 分頁差在哪？
8. 如何知道某個 API 很慢？你用什麼工具監控？
9. Repository Pattern 的 Interface 你會怎麼設計？為什麼要有 Interface？
10. 如果老闆說「訂單量暴增 10 倍」，你第一步做什麼？

---

**🔥 實戰 Coding 練習**：從零建立 `Order` model + migration + factory + seeder，實作 `OrderService@createOrder` 含 transaction，並寫 Feature Test 驗證建立成功與庫存不足兩個情境。

**🔥 面試官陷阱題**：「你的 API 是 RESTful，那 `PATCH /orders/{id}/status` 算不算 RESTful？還是應該用 `PUT`？」（答：PATCH 對部分更新語意正確；也有人用 `/orders/{id}/cancel` 這類 action-based，各有優缺，能說出取捨才是重點）

**🔥 答不出來代表哪裡不懂**：沒有思考過系統整體資料流，只會寫單一功能。

**🔥 如何補強**：拿一張白紙，在 15 分鐘內畫出整個系統的 DB schema + API list + 架構圖，然後對照實作檢查是否一致。

---

## 進度追蹤

| Phase | 狀態 | 目標完成 |
|-------|------|----------|
| Phase 1：基礎手寫 | ⬜ 未開始 | — |
| Phase 2：API 資料流 | ⬜ 未開始 | — |
| Phase 3：Service / Repository | ⬜ 未開始 | — |
| Phase 4：系統設計 | ⬜ 未開始 | — |
