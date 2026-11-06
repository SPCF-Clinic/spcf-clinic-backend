<?php

namespace App\Repositories\Student;

use App\Repositories\BaseRepository;
use App\Models\User;
use App\Http\Resources\StudentResource;
use App\Support\NameSearch;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class IndexStudentRepository extends BaseRepository
{
    // EAV personal-info field ids (see StudentResource).
    private const BIRTHDATE_FIELD_ID = 4;
    private const SEX_FIELD_ID = 5;
    private const GRADE_LEVEL_FIELD_ID = 12;
    private const YEAR_LEVEL_FIELD_ID = 13;

    public function execute($request)
    {
        $perPage = (int) $request->input('per_page', 20);
        $sortBy = $request->input('sort_by') ?: 'id';
        $sortOrder = $request->input('sort_order') === 'desc' ? 'desc' : 'asc';

        $query = User::role('Student')
            ->withTrashed()
            ->when($request->student_id, function ($query, $student_id) {
                return $query->where('users.username', $student_id);
            });

        // Sort and paginate in the database, then hydrate only the current
        // page. Eager loading what StudentResource reads avoids per-student
        // queries.
        $this->applySort($query, $sortBy, $sortOrder);

        $students = $query
            ->with(['personalInfos', 'latestCheckIn.currentBed'])
            ->paginate($perPage);

        return $this->success('Students retrieved successfully.', [
            'students' => StudentResource::collection($students),
            'pagination' => $this->pagePaginationData($students),
        ], 200);
    }

    /**
     * Mirrors the values StudentResource derives for each sortable column.
     * $direction is validated to 'asc'|'desc' before it gets here, and every
     * interpolated field id is an int constant, so the raw SQL is safe.
     */
    private function applySort(Builder $query, string $sortBy, string $direction): void
    {
        $inverse = $direction === 'asc' ? 'desc' : 'asc';

        switch ($sortBy) {
            case 'student_id':
                $query->orderBy('users.username', $direction);
                break;

            case 'name':
                $first = $this->personalInfo(NameSearch::FIRST_NAME_FIELD_ID);
                $middle = $this->personalInfo(NameSearch::MIDDLE_NAME_FIELD_ID);
                $last = $this->personalInfo(NameSearch::LAST_NAME_FIELD_ID);

                $query->orderByRaw("CONCAT_WS(' ', NULLIF({$first}, ''), NULLIF({$middle}, ''), NULLIF({$last}, '')) {$direction}");
                break;

            case 'year_level':
                $year = $this->personalInfo(self::YEAR_LEVEL_FIELD_ID);
                $grade = $this->personalInfo(self::GRADE_LEVEL_FIELD_ID);

                // College ("1st Year") before basic ed ("Grade 7"); length first
                // so "Grade 9" sorts before "Grade 10".
                $query
                    ->orderByRaw("({$year} IS NULL) {$direction}")
                    ->orderByRaw("CHAR_LENGTH(COALESCE({$year}, {$grade})) {$direction}")
                    ->orderByRaw("COALESCE({$year}, {$grade}) {$direction}");
                break;

            case 'age':
                // Older = earlier birthdate, so the date direction is inverted.
                // Students without a birthdate come first ascending, last descending.
                $birthdate = $this->personalInfo(self::BIRTHDATE_FIELD_ID);

                $query
                    ->orderByRaw("({$birthdate} IS NOT NULL) {$direction}")
                    ->orderByRaw("{$birthdate} {$inverse}");
                break;

            case 'sex':
                $query->orderByRaw($this->personalInfo(self::SEX_FIELD_ID) . " {$direction}");
                break;

            case 'status':
                // Same rules as StudentResource: archived, else bed number /
                // "In Clinic" when the latest check-in is open, else "Not In Clinic".
                $query
                    ->select('users.*')
                    ->leftJoin('check_ins as latest_ci', 'latest_ci.id', '=', DB::raw('(select max(ci.id) from check_ins as ci where ci.user_id = users.id)'))
                    ->leftJoin('beds as latest_bed', 'latest_bed.id', '=', 'latest_ci.current_bed_id')
                    ->orderByRaw(
                        "CASE
                            WHEN users.deleted_at IS NOT NULL THEN 'Archived'
                            WHEN latest_ci.status = 'Checked In' THEN COALESCE(latest_bed.bed_number, 'In Clinic')
                            ELSE 'Not In Clinic'
                        END {$direction}"
                    );
                break;

            default:
                $query->orderBy('users.id', $direction);
        }

        // Stable order for ties so pages never overlap or skip rows.
        $query->orderBy('users.id');
    }

    /** Scalar subquery for one student's answer to a personal-info field. */
    private function personalInfo(int $fieldId): string
    {
        return "(select upi.value from user_personal_infos as upi where upi.user_id = users.id and upi.personal_info_field_id = {$fieldId} order by upi.id limit 1)";
    }
}
