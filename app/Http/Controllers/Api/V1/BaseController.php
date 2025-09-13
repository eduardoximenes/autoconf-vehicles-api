<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\Request;

abstract class BaseController extends Controller
{
    use ApiResponse;

    /**
     * Get pagination parameters from request
     *
     * @param Request $request
     * @param int $defaultPerPage
     * @param int $maxPerPage
     * @return array
     */
    protected function getPaginationParams(Request $request, int $defaultPerPage = 15, int $maxPerPage = 100): array
    {
        $perPage = min((int) $request->get('per_page', $defaultPerPage), $maxPerPage);
        $page = (int) $request->get('page', 1);

        return [
            'per_page' => $perPage,
            'page' => max($page, 1)
        ];
    }

    /**
     * Get sorting parameters from request
     *
     * @param Request $request
     * @param array $allowedSorts
     * @param string $defaultSort
     * @return array
     */
    protected function getSortParams(Request $request, array $allowedSorts = [], string $defaultSort = 'id'): array
    {
        $sort = $request->get('sort', $defaultSort);
        $sorts = [];

        if ($sort) {
            $sortFields = explode(',', $sort);

            foreach ($sortFields as $field) {
                $direction = 'asc';

                if (str_starts_with($field, '-')) {
                    $direction = 'desc';
                    $field = substr($field, 1);
                }

                // Only allow whitelisted sort fields
                if (empty($allowedSorts) || in_array($field, $allowedSorts)) {
                    $sorts[] = [
                        'field' => $field,
                        'direction' => $direction
                    ];
                }
            }
        }

        return $sorts ?: [['field' => $defaultSort, 'direction' => 'asc']];
    }

    /**
     * Apply sorting to query builder
     *
     * @param mixed $query
     * @param array $sorts
     * @return mixed
     */
    protected function applySorting($query, array $sorts)
    {
        foreach ($sorts as $sort) {
            $query->orderBy($sort['field'], $sort['direction']);
        }

        return $query;
    }

    /**
     * Get search parameters from request
     *
     * @param Request $request
     * @param string $searchParam
     * @return string|null
     */
    protected function getSearchParam(Request $request, string $searchParam = 'q'): ?string
    {
        $search = $request->get($searchParam);
        return $search ? trim($search) : null;
    }
}
