<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $profiles = \App\Models\StaffProfile::query()->select(
            'id',
            'pillar',
            'organisation_unit',
            'grade',
            'appointment_type'
        )->get();

        $previous = [];

        if(!$profiles->isEmpty()){
            foreach ($profiles as $profile){
                $previous[] = (Object)[
                    'id' => $profile->id,
                    'pillar' => $profile->pillar,
                    'organisation_unit' => $profile->organisation_unit,
                    'grade' => $profile->grade,
                    'appointment_type' => $profile->appointment_type,
                ];

                $profile->pillar = null;
                $profile->organisation_unit = null;
                $profile->grade = null;
                $profile->appointment_type = null;
                $profile->save();
            }
        }

        Schema::table('staff_profiles', function (Blueprint $table) use ($previous) {
            $table->unsignedBigInteger('categories')->after('category')->nullable();
            $table->unsignedBigInteger('pillar')->nullable()->change();
            $table->unsignedBigInteger('organisation_unit')->nullable()->change();
            $table->unsignedBigInteger('grade')->nullable()->change();
            $table->unsignedBigInteger('appointment_type')->nullable()->change();

            if(count($previous) > 0){
                $registrar = new \App\Http\Controllers\StaffRegisterController();
                $parts = $registrar->insertParts();

                foreach ($previous as $prev){
                    $profile = \App\Models\StaffProfile::query()->find($prev->id);

                    if($prev->pillar)
                        $profile->pillar = $parts['pillar']($prev->pillar)?->id ?? null;

                    if($prev->organisation_unit)
                        $profile->organisation_unit = $parts['organizationUnit']($prev->organisation_unit)?->id ?? null;

                    if($prev->grade)
                        $profile->grade = $parts['grade']($prev->grade)?->id ?? null;

                    if($prev->appointment_type)
                        $profile->appointment_type = $parts['appointmentType']($prev->appointment_type)?->id ?? null;

                    $profile->save();
                }
            }

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff_profiles', function (Blueprint $table) {
            $table->dropColumn('categories');
            $table->string('pillar')->change();
            $table->string('organisation_unit')->change();
            $table->string('grade')->change();
            $table->string('appointment_type')->change();
        });
    }
};
