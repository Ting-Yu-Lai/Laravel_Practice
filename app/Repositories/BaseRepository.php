<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\LazyCollection;

abstract class BaseRepository
{
    public function __construct(protected Model $model) {}

    // -------------------------------------------------------------------------
    // 基本查詢
    // -------------------------------------------------------------------------

    public function find(int $id): ?Model
    {
        return $this->model->find($id);
    }

    public function findOrFail(int $id): Model
    {
        return $this->model->findOrFail($id);
    }

    public function findMany(array $ids): Collection
    {
        return $this->model->findMany($ids);
    }

    /** @param array<string, mixed> $conditions */
    public function findWhere(array $conditions): Collection
    {
        return $this->model->where($conditions)->get();
    }

    /** @param array<string, mixed> $conditions */
    public function findWhereFirst(array $conditions): ?Model
    {
        return $this->model->where($conditions)->first();
    }

    public function findWhereIn(string $column, array $values): Collection
    {
        return $this->model->whereIn($column, $values)->get();
    }

    public function findWhereNotIn(string $column, array $values): Collection
    {
        return $this->model->whereNotIn($column, $values)->get();
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->paginate($perPage);
    }

    public function pluck(string $column, ?string $key = null): \Illuminate\Support\Collection
    {
        return $this->model->pluck($column, $key);
    }

    // -------------------------------------------------------------------------
    // 存在性 / 計數
    // -------------------------------------------------------------------------

    /** @param array<string, mixed> $conditions */
    public function exists(array $conditions): bool
    {
        return $this->model->where($conditions)->exists();
    }

    /** @param array<string, mixed> $conditions */
    public function count(array $conditions = []): int
    {
        return $this->model->where($conditions)->count();
    }

    // -------------------------------------------------------------------------
    // 建立
    // -------------------------------------------------------------------------

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * 找到符合 $attributes 的第一筆，找不到就以 $attributes + $values 建立。
     *
     * @param array<string, mixed> $attributes 查詢條件（唯一鍵）
     * @param array<string, mixed> $values     建立時額外填入的欄位
     */
    public function firstOrCreate(array $attributes, array $values = []): Model
    {
        return $this->model->firstOrCreate($attributes, $values);
    }

    /**
     * 找到符合 $attributes 的第一筆，找不到就以 $attributes + $values new 一個（未存檔）。
     *
     * @param array<string, mixed> $attributes
     * @param array<string, mixed> $values
     */
    public function firstOrNew(array $attributes, array $values = []): Model
    {
        return $this->model->firstOrNew($attributes, $values);
    }

    // -------------------------------------------------------------------------
    // 更新
    // -------------------------------------------------------------------------

    public function update(Model $model, array $data): Model
    {
        $model->update($data);
        return $model->fresh();
    }

    /**
     * 找到符合 $attributes 的第一筆並以 $values 更新；找不到就建立。
     *
     * @param array<string, mixed> $attributes 查詢條件（唯一鍵）
     * @param array<string, mixed> $values     要寫入的欄位
     */
    public function updateOrCreate(array $attributes, array $values = []): Model
    {
        return $this->model->updateOrCreate($attributes, $values);
    }

    /**
     * 批次 upsert（高效率，不觸發 Model 事件）。
     *
     * @param array<int, array<string, mixed>> $values
     * @param array<int, string>               $uniqueBy
     * @param array<int, string>|null          $updateColumns null = 更新除 uniqueBy 外所有欄位
     */
    public function upsert(array $values, array $uniqueBy, ?array $updateColumns = null): int
    {
        return $this->model->upsert($values, $uniqueBy, $updateColumns);
    }

    // -------------------------------------------------------------------------
    // 刪除
    // -------------------------------------------------------------------------

    public function delete(Model $model): bool
    {
        return (bool) $model->delete();
    }

    /** @param array<string, mixed> $conditions */
    public function deleteWhere(array $conditions): int
    {
        return $this->model->where($conditions)->delete();
    }

    // -------------------------------------------------------------------------
    // Eager Loading 輔助
    // -------------------------------------------------------------------------

    /** @param array<int, string>|string $relations */
    public function with(array|string $relations): static
    {
        return $this->model->with($relations);
    }

    // -------------------------------------------------------------------------
    // Soft Delete 輔助（僅限 Model 有使用 SoftDeletes Trait 時有效）
    // -------------------------------------------------------------------------

    public function withTrashed(): static
    {
        $this->model = $this->model->withTrashed();
        return $this;
    }

    public function onlyTrashed(): static
    {
        $this->model = $this->model->onlyTrashed();
        return $this;
    }

    public function restore(Model $model): bool
    {
        return (bool) $model->restore();
    }

    public function forceDelete(Model $model): bool
    {
        return (bool) $model->forceDelete();
    }

    // -------------------------------------------------------------------------
    // 大量資料處理
    // -------------------------------------------------------------------------

    public function chunk(int $size, callable $callback): bool
    {
        return $this->model->chunk($size, $callback);
    }

    public function cursor(): LazyCollection
    {
        return $this->model->cursor();
    }
}
