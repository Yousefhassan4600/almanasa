<?php

namespace App\Actions\StudentPortal\Auth;

use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class CompleteStudentRegistration
{
    /**
     * @param  array{firstName: string, lastName: string|null, email: string|null, dateOfBirth: string, gender: string, countryId: int, cityId: int, educationStageId: int, gradeId: int, schoolName: string}  $data
     */
    public function handle(User $user, array $data, TemporaryUploadedFile|UploadedFile|null $avatar = null): void
    {
        DB::transaction(function () use ($user, $data, $avatar): void {
            $avatarPath = $avatar
                ? $avatar->store('students/avatars', 'public')
                : null;

            $user->forceFill([
                'first_name' => $data['firstName'],
                'last_name' => $data['lastName'],
                'date_of_birth' => $data['dateOfBirth'],
            ])->save();

            StudentProfile::query()->create([
                'user_id' => $user->id,
                'email' => $data['email'],
                'avatar' => $avatarPath,
                'gender' => $data['gender'],
                'country_id' => $data['countryId'],
                'city_id' => $data['cityId'],
                'education_stage_id' => $data['educationStageId'],
                'grade_id' => $data['gradeId'],
                'school_name' => $data['schoolName'],
            ]);
        });
    }
}
