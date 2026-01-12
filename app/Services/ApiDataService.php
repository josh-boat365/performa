<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

/**
 * ApiDataService
 *
 * Helper service for common data transformation and processing operations.
 * Provides reusable methods for filtering, pagination, and data manipulation
 * across different API responses.
 */
class ApiDataService
{
    /**
     * Filter KPIs by type and active status
     *
     * @param array|Collection $kpis The KPI collection
     * @param string $type The KPI type to filter by (e.g., 'REGULAR', 'PROBATION')
     * @param bool|null $active Filter by active status (null = all)
     * @return Collection Filtered KPIs
     */
    public static function filterKpisByTypeAndStatus($kpis, string $type, ?bool $active = null): Collection
    {
        return collect($kpis)
            ->filter(function ($kpi) use ($type, $active) {
                $typeMatches = ($kpi['type'] ?? $kpi->type ?? null) === $type;
                $statusMatches = $active === null || ($kpi['active'] ?? $kpi->active ?? null) == $active;
                return $typeMatches && $statusMatches;
            });
    }

    /**
     * Filter items managed by a specific user
     *
     * @param array|Collection $items The items to filter
     * @param int|string $userId The user ID
     * @param string $managerField The field name containing the manager ID
     * @return Collection Filtered items
     */
    public static function filterByManager($items, $userId, string $managerField = 'manager'): Collection
    {
        return collect($items)
            ->filter(function ($item) use ($userId, $managerField) {
                $managerValue = $item[$managerField] ?? $item->$managerField ?? null;
                return isset($managerValue) && $managerValue === $userId;
            });
    }

    /**
     * Filter by status value
     *
     * @param array|Collection $items The items to filter
     * @param string $status The status to filter by
     * @param string $statusField The field name containing the status
     * @return Collection Filtered items
     */
    public static function filterByStatus($items, string $status, string $statusField = 'status'): Collection
    {
        return collect($items)
            ->filter(function ($item) use ($status, $statusField) {
                $statusValue = $item[$statusField] ?? $item->$statusField ?? null;
                return isset($statusValue) && $statusValue === $status;
            });
    }

    /**
     * Extract unique values from a collection
     *
     * @param array|Collection $items The collection
     * @param string $field The field to extract unique values from
     * @return array Unique values
     */
    public static function extractUnique($items, string $field): array
    {
        return collect($items)
            ->pluck($field)
            ->unique()
            ->values()
            ->toArray();
    }

    /**
     * Map items to a simplified structure
     *
     * @param array|Collection $items The items to map
     * @param array $map Mapping of field names ['from' => 'to']
     * @return Collection Mapped items
     */
    public static function mapItems($items, array $map): Collection
    {
        return collect($items)
            ->map(function ($item) use ($map) {
                $mapped = [];
                foreach ($map as $fromField => $toField) {
                    $mapped[$toField] = $item[$fromField] ?? $item->$fromField ?? null;
                }
                return (object)$mapped;
            });
    }

    /**
     * Sort collection by field
     *
     * @param array|Collection $items The items to sort
     * @param string $field The field to sort by
     * @param string $direction 'asc' or 'desc'
     * @return Collection Sorted items
     */
    public static function sortByField($items, string $field, string $direction = 'asc'): Collection
    {
        $collection = collect($items);

        if ($direction === 'desc') {
            return $collection->sortByDesc($field)->values();
        }

        return $collection->sortBy($field)->values();
    }

    /**
     * Group items by a field
     *
     * @param array|Collection $items The items to group
     * @param string $field The field to group by
     * @return Collection Grouped items
     */
    public static function groupByField($items, string $field): Collection
    {
        return collect($items)->groupBy($field);
    }

    /**
     * Paginate a collection
     *
     * @param array|Collection $items The items to paginate
     * @param int $perPage Items per page
     * @param int $page Current page number
     * @return LengthAwarePaginator Paginated items
     */
    public static function paginateCollection($items, int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        $items = collect($items)->values();
        $total = $items->count();
        $items = $items->slice(($page - 1) * $perPage, $perPage);

        return new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            [
                'path' => Paginator::resolveCurrentPath(),
                'query' => Paginator::resolveQueryString(),
            ]
        );
    }

    /**
     * Transform nested sections and metrics
     *
     * @param array|object $kpi The KPI with sections
     * @param bool $includeInactive Include inactive items
     * @return object Transformed KPI
     */
    public static function transformKpiSections($kpi, bool $includeInactive = false): object
    {
        // Handle both array and object access
        $sectionsData = is_array($kpi) ? ($kpi['sections'] ?? []) : ($kpi->sections ?? []);

        $sections = collect($sectionsData)
            ->when(!$includeInactive, function ($col) {
                return $col->filter(function ($s) {
                    $active = is_array($s) ? ($s['active'] ?? true) : ($s->active ?? true);
                    return $active;
                });
            })
            ->map(function ($section) use ($includeInactive) {
                $metricsData = is_array($section) ? ($section['metrics'] ?? []) : ($section->metrics ?? []);
                $metrics = collect($metricsData)
                    ->when(!$includeInactive, function ($col) {
                        return $col->filter(function ($m) {
                            $active = is_array($m) ? ($m['active'] ?? true) : ($m->active ?? true);
                            return $active;
                        });
                    })
                    ->values();

                if (is_array($section)) {
                    $section['metrics'] = $metrics;
                } else {
                    $section->metrics = $metrics;
                }
                return $section;
            })
            ->values();

        if (is_array($kpi)) {
            $kpi['sections'] = $sections;
            return (object)$kpi;
        } else {
            /** @var object $kpi */
            $kpi->sections = $sections;
            return $kpi;
        }
    }

    /**
     * Check if KPI belongs to user's managed roles
     *
     * @param object $kpi The KPI object
     * @param int|string $userId The user ID
     * @param string $managerField The manager field to check
     * @return bool True if KPI is manageable by user
     */
    public static function isKpiManagedByUser($kpi, $userId, string $managerField = 'manager'): bool
    {
        $empRoleManager = null;

        // Handle both array and object access for empRole
        if (is_array($kpi)) {
            $empRole = $kpi['empRole'] ?? null;
            if (is_array($empRole)) {
                $empRoleManager = $empRole[$managerField] ?? null;
            } elseif (is_object($empRole)) {
                $empRoleManager = $empRole->$managerField ?? null;
            }
        } elseif (is_object($kpi)) {
            $empRole = $kpi->empRole ?? null;
            if (is_array($empRole)) {
                $empRoleManager = $empRole[$managerField] ?? null;
            } elseif (is_object($empRole)) {
                $empRoleManager = $empRole->$managerField ?? null;
            }
        }

        return isset($empRoleManager) && $empRoleManager === $userId;
    }

    /**
     * Extract role information
     *
     * @param array|Collection $roles The roles collection
     * @param int|string $userId The user ID
     * @return array Extracted and deduplicated roles
     */
    public static function extractUserRoles($roles, $userId): array
    {
        return collect($roles)
            ->filter(function ($role) use ($userId) {
                $managerValue = $role['manager'] ?? $role->manager ?? null;
                return $managerValue === $userId;
            })
            ->map(function ($role) {
                return [
                    'id' => $role['id'] ?? $role->id ?? null,
                    'name' => $role['name'] ?? $role->name ?? null
                ];
            })
            ->unique('id')
            ->values()
            ->toArray();
    }

    /**
     * Flatten nested collection to single level
     *
     * @param array|Collection $items The nested items
     * @param string $childField The field containing children
     * @return Collection Flattened collection
     */
    public static function flattenNested($items, string $childField = 'children'): Collection
    {
        $flattened = collect();

        collect($items)->each(function ($item) use ($flattened, $childField) {
            $flattened->push($item);
            $childData = is_array($item) ? ($item[$childField] ?? null) : ($item->$childField ?? null);
            if (isset($childData)) {
                $flattened->push(...self::flattenNested($childData, $childField));
            }
        });

        return $flattened;
    }

    /**
     * Get first item or return default
     *
     * @param array|Collection $items The items
     * @param mixed $default Default value if empty
     * @return mixed The first item or default
     */
    public static function getFirstOrDefault($items, $default = null)
    {
        $collection = collect($items);
        return $collection->isNotEmpty() ? $collection->first() : $default;
    }

    /**
     * Check if collection has items
     *
     * @param array|Collection $items The items
     * @return bool True if collection is not empty
     */
    public static function hasItems($items): bool
    {
        return collect($items)->isNotEmpty();
    }

    /**
     * Format score data for display
     *
     * @param float $score The score value
     * @param int $decimals Number of decimal places
     * @return string Formatted score
     */
    public static function formatScore(float $score, int $decimals = 2): string
    {
        return number_format($score, $decimals);
    }

    /**
     * Calculate percentage
     *
     * @param float $value The value
     * @param float $total The total
     * @param int $decimals Number of decimal places
     * @return float The percentage
     */
    public static function calculatePercentage(float $value, float $total, int $decimals = 2): float
    {
        if ($total === 0) {
            return 0;
        }
        return round(($value / $total) * 100, $decimals);
    }

    /**
     * Merge multiple collections
     *
     * @param array $collections Array of collections to merge
     * @return Collection Merged collection
     */
    public static function mergeCollections(array $collections): Collection
    {
        return collect($collections)
            ->reduce(fn($carry, $collection) => $carry->merge($collection), collect());
    }
}
