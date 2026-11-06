<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Bed;
use App\Models\CheckIn;
use App\Models\PersonalInfoField;
use App\Models\User;
use App\Models\UserPersonalInfo;
use App\Repositories\ActivityLog\IndexActivityLogRepository;
use App\Repositories\CheckIn\IndexCheckInRepository;
use App\Repositories\Student\IndexStudentCheckInsRepository;
use App\Repositories\Student\IndexStudentRepository;
use Database\Seeders\BedSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RepositoryChangesTest extends TestCase
{
    use RefreshDatabase;

    public function test_check_ins_are_ordered_newest_first_with_id_as_tie_breaker(): void
    {
        $user = $this->createUser('student-1');
        $sameTime = Carbon::parse('2026-11-06 09:00:00');

        $older = CheckIn::create([
            'user_id' => $user->id,
            'reason_for_visit' => 'Older',
            'check_in_time' => $sameTime->copy()->subHour(),
        ]);
        $sameTimeLowerId = CheckIn::create([
            'user_id' => $user->id,
            'reason_for_visit' => 'Same time, lower id',
            'check_in_time' => $sameTime,
        ]);
        $sameTimeHigherId = CheckIn::create([
            'user_id' => $user->id,
            'reason_for_visit' => 'Same time, higher id',
            'check_in_time' => $sameTime,
        ]);

        $response = app(IndexCheckInRepository::class)->execute(Request::create('/check-ins'));

        $this->assertSame(200, $response->status());
        $this->assertSame(
            [$sameTimeHigherId->id, $sameTimeLowerId->id, $older->id],
            data_get($this->responseData($response), 'data.check_ins.*.id')
        );
    }

    public function test_check_ins_can_be_filtered_by_a_date_range(): void
    {
        $user = $this->createUser('student-1');

        $before = CheckIn::create([
            'user_id' => $user->id,
            'reason_for_visit' => 'Before range',
            'check_in_time' => '2026-11-05 23:59:59',
        ]);
        $inRange = CheckIn::create([
            'user_id' => $user->id,
            'reason_for_visit' => 'In range',
            'check_in_time' => '2026-11-06 12:00:00',
        ]);
        $after = CheckIn::create([
            'user_id' => $user->id,
            'reason_for_visit' => 'After range',
            'check_in_time' => '2026-11-07 00:00:00',
        ]);

        $response = app(IndexCheckInRepository::class)->execute(
            Request::create('/check-ins', 'GET', [
                'date_from' => '2026-11-06',
                'date_to' => '2026-11-06',
            ])
        );

        $this->assertSame(200, $response->status());
        $this->assertSame(
            [$inRange->id],
            data_get($this->responseData($response), 'data.check_ins.*.id')
        );
        $this->assertNotContains($before->id, data_get($this->responseData($response), 'data.check_ins.*.id'));
        $this->assertNotContains($after->id, data_get($this->responseData($response), 'data.check_ins.*.id'));
    }

    public function test_student_check_ins_are_ordered_newest_first_with_id_as_tie_breaker(): void
    {
        $user = $this->createUser('student-1');
        $sameTime = Carbon::parse('2026-11-06 09:00:00');

        $older = CheckIn::create([
            'user_id' => $user->id,
            'reason_for_visit' => 'Older',
            'check_in_time' => $sameTime->copy()->subHour(),
        ]);
        $sameTimeLowerId = CheckIn::create([
            'user_id' => $user->id,
            'reason_for_visit' => 'Same time, lower id',
            'check_in_time' => $sameTime,
        ]);
        $sameTimeHigherId = CheckIn::create([
            'user_id' => $user->id,
            'reason_for_visit' => 'Same time, higher id',
            'check_in_time' => $sameTime,
        ]);

        $response = app(IndexStudentCheckInsRepository::class)
            ->execute(Request::create('/students/1/check-ins'), $user);

        $this->assertSame(200, $response->status());
        $this->assertSame(
            [$sameTimeHigherId->id, $sameTimeLowerId->id, $older->id],
            data_get($this->responseData($response), 'data.check_ins.*.id')
        );
    }

    public function test_activity_log_date_filter_matches_only_the_requested_day(): void
    {
        $admin = $this->createUser('admin');
        Role::create(['name' => 'Super Admin', 'guard_name' => 'web']);
        $admin->assignRole('Super Admin');
        $this->actingAs($admin);

        $matching = ActivityLog::create([
            'group' => 'AUTH',
            'action' => 'Matching',
            'performed_by' => $admin->id,
        ]);
        $outsideRange = ActivityLog::create([
            'group' => 'AUTH',
            'action' => 'Outside range',
            'performed_by' => $admin->id,
        ]);

        $matching->created_at = Carbon::parse('2026-11-06 00:00:00');
        $matching->save();
        $outsideRange->created_at = Carbon::parse('2026-11-05 23:59:59');
        $outsideRange->save();

        $response = app(IndexActivityLogRepository::class)->execute(
            Request::create('/activity-logs', 'GET', ['date' => '2026-11-06'])
        );

        $this->assertSame(200, $response->status());
        $this->assertSame([$matching->id], data_get($this->responseData($response), 'data.data.*.id'));
    }

    public function test_invalid_activity_log_date_returns_no_logs(): void
    {
        $admin = $this->createUser('admin');
        Role::create(['name' => 'Super Admin', 'guard_name' => 'web']);
        $admin->assignRole('Super Admin');
        $this->actingAs($admin);

        ActivityLog::create([
            'group' => 'AUTH',
            'action' => 'Existing log',
            'performed_by' => $admin->id,
        ]);

        $response = app(IndexActivityLogRepository::class)->execute(
            Request::create('/activity-logs', 'GET', ['date' => 'not-a-date'])
        );

        $this->assertSame(200, $response->status());
        $this->assertSame([], data_get($this->responseData($response), 'data.data'));
    }

    public function test_student_sorting_is_done_in_the_database(): void
    {
        $role = Role::create(['name' => 'Student', 'guard_name' => 'web']);
        $second = $this->createUser('student-2');
        $first = $this->createUser('student-1');
        $second->assignRole($role);
        $first->assignRole($role);

        $response = app(IndexStudentRepository::class)->execute(
            Request::create('/students', 'GET', ['sort_by' => 'student_id'])
        );

        $this->assertSame(200, $response->status());
        $this->assertSame(
            [$first->id, $second->id],
            data_get($this->responseData($response), 'data.students.*.id')
        );
    }

    public function test_student_lookup_matches_full_name_and_partial_username(): void
    {
        $role = Role::create(['name' => 'Student', 'guard_name' => 'web']);
        $nameMatch = $this->createUser('student-100');
        $usernameMatch = $this->createUser('student-200');
        $nameMatch->assignRole($role);
        $usernameMatch->assignRole($role);

        PersonalInfoField::create(['id' => 1]);
        PersonalInfoField::create(['id' => 2]);
        UserPersonalInfo::create([
            'user_id' => $nameMatch->id,
            'personal_info_field_id' => 1,
            'value' => 'Maria',
        ]);
        UserPersonalInfo::create([
            'user_id' => $nameMatch->id,
            'personal_info_field_id' => 2,
            'value' => 'Santos',
        ]);

        $nameResponse = app(IndexStudentRepository::class)->execute(
            Request::create('/students', 'GET', ['student_id' => 'Maria Santos'])
        );
        $usernameResponse = app(IndexStudentRepository::class)->execute(
            Request::create('/students', 'GET', ['student_id' => '200'])
        );

        $this->assertSame(
            [$nameMatch->id],
            data_get($this->responseData($nameResponse), 'data.students.*.id')
        );
        $this->assertSame(
            [$usernameMatch->id],
            data_get($this->responseData($usernameResponse), 'data.students.*.id')
        );
    }

    public function test_bed_seeder_pads_bed_numbers(): void
    {
        $this->seed(BedSeeder::class);

        $this->assertSame('Bed 01', Bed::query()->oldest('id')->value('bed_number'));
        $this->assertSame('Bed 20', Bed::query()->latest('id')->value('bed_number'));
    }

    public function test_new_high_volume_indexes_are_registered(): void
    {
        $this->assertContains(
            'check_ins_status_current_bed_index',
            array_column(Schema::getIndexes('check_ins'), 'name')
        );
        $this->assertContains(
            'check_ins_check_in_time_index',
            array_column(Schema::getIndexes('check_ins'), 'name')
        );
        $this->assertContains(
            'dispensed_items_created_at_index',
            array_column(Schema::getIndexes('dispensed_items'), 'name')
        );
        $this->assertContains(
            'activity_logs_created_at_index',
            array_column(Schema::getIndexes('activity_logs'), 'name')
        );
        $this->assertContains(
            'user_personal_infos_user_field_index',
            array_column(Schema::getIndexes('user_personal_infos'), 'name')
        );
    }

    private function createUser(string $username): User
    {
        return User::create([
            'username' => $username,
            'password' => 'password',
        ]);
    }

    private function responseData(JsonResponse $response): array
    {
        return $response->getData(true);
    }
}
