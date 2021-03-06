<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Jenssegers\Date\Date;
use Monolog\DateTimeImmutable;

class DummySeeder extends Seeder
{
    /**
     * Run the dummy seed.
     *
     * @return void
     */
    public function run()
    {
        $now = Date::now();
        $tomorrow = Date::now()->addDays(1);
        $questionnaireUuid = 'facade01-feed-dead-c0de-defacedc0c0a';
        $dummyUserUuid = '00000000-0000-0000-0000-000000000001';
        $dummyPlannerUuid = '00000000-0000-0000-0000-000000000002';
        $dummyAdminUuid = '00000000-0000-0000-0000-000000000003';
        $dummyOrgUuid = '00000000-0000-0000-0000-000000000000';

        DB::table('organisation')->insert([
            'name' => 'Demo organisatie',
            'uuid' => $dummyOrgUuid,
            'external_id' => '999999', // 6 digit ids don't exist in real ggd ecosystem
            'created_at' => $now,
            'updated_at' => $now
        ]);

        DB::table('bcouser')->insert([
            'name' => 'Demo Gebruiker',
            'uuid' => $dummyUserUuid,
            'external_id' => $dummyUserUuid,
            'roles' => 'user',
            'created_at' => $now,
            'updated_at' => $now
        ]);

        DB::table('bcouser')->insert([
            'name' => 'Demo Planner',
            'uuid' => $dummyPlannerUuid,
            'external_id' => $dummyPlannerUuid,
            'roles' => 'user,planner',
            'created_at' => $now,
            'updated_at' => $now
        ]);

        DB::table('bcouser')->insert([
            'name' => 'Demo Beheerder',
            'uuid' => $dummyAdminUuid,
            'external_id' => $dummyAdminUuid,
            'roles' => 'user,admin',
            'created_at' => $now,
            'updated_at' => $now
        ]);

        for ($i=0; $i<30; $i++) {

            $caseUuid = (string)Str::uuid();
            // Create a case for the dummy user (id 0), with tasks. Case is open, not yet submitted.
            DB::table('covidcase')->insert([
                'name' => 'Bruce Wayne',
                'uuid' => $caseUuid,
                'organisation_uuid' => $dummyOrgUuid,
                'owner' => $dummyUserUuid,
                'assigned_uuid' => $dummyUserUuid,
                'index_submitted_at' => null,
                'date_of_symptom_onset' => date('Y-m-d'),
                'status' => 'paired',
                'case_id' => 'GOTHAM'.sprintf("%02d", $i),
                'pairing_expires_at' => null,
                'window_expires_at' => $tomorrow,
                'created_at' => $now,
                'updated_at' => $now
            ]);

            DB::table('task')->insert([[
                'uuid' => (string)Str::uuid(),
                'case_uuid' => $caseUuid,
                'task_type' => 'contact',
                'source' => 'portal',
                'label' => 'Robin',
                'task_context' => 'Business partner',
                'category' => '2a',
                'date_of_last_exposure' => date('Y-m-d'),
                'communication' => 'staff',
                'informed_by_index' => 0,
                'created_at' => $now,
                'updated_at' => $now
            ], [
                'uuid' => (string)Str::uuid(),
                'case_uuid' => $caseUuid,
                'task_type' => 'contact',
                'source' => 'portal',
                'label' => 'Alfred',
                'task_context' => 'Butler',
                'category' => '1',
                'date_of_last_exposure' => date('Y-m-d'),
                'communication' => 'staff',
                'informed_by_index' => 0,
                'created_at' => $now,
                'updated_at' => $now
            ], [
                'uuid' => (string)Str::uuid(),
                'case_uuid' => $caseUuid,
                'task_type' => 'contact',
                'source' => 'portal',
                'label' => 'Batman',
                'task_context' => 'Unclear relationship (never in same room)',
                'category' => '3',
                'date_of_last_exposure' => date('Y-m-d'),
                'communication' => 'index',
                'informed_by_index' => 0,
                'created_at' => $now,
                'updated_at' => $now
            ], [
                'uuid' => (string)Str::uuid(),
                'case_uuid' => $caseUuid,
                'task_type' => 'contact',
                'source' => 'portal',
                'label' => 'Catwoman',
                'task_context' => 'Friend',
                'category' => '2b',
                'date_of_last_exposure' => date('Y-m-d'),
                'communication' => 'staff',
                'informed_by_index' => 0,
                'created_at' => $now,
                'updated_at' => $now
            ]]);

            $caseUuid = (string)Str::uuid();
            // Create another case for the dummy user (id 0), with tasks. Case is open, user has delivered data
            DB::table('covidcase')->insert([
                'name' => 'Clark Kent',
                'uuid' => $caseUuid,
                'organisation_uuid' => $dummyOrgUuid,
                'owner' => $dummyUserUuid,
                'assigned_uuid' => $dummyUserUuid,
                'date_of_symptom_onset' => date('Y-m-d'),
                'index_submitted_at' => Date::now()->addHours(1),
                'status' => 'paired',
                'pairing_expires_at' => null,
                'window_expires_at' => $tomorrow,
                'case_id' => 'METROPOLIS'.sprintf("%02d", $i),
                'created_at' => Date::now()->addHours(-3),
                'updated_at' => $now
            ]);

            $taskUuidLex = (string)Str::uuid();
            $taskUuidLois = (string)Str::uuid();
            $taskUuidMartha = (string)Str::uuid();
            $taskUuidZod = (string)Str::uuid();
            $taskUuidAquaman = (string)Str::uuid();

            DB::table('task')->insert([[
                'uuid' => $taskUuidLex,
                'case_uuid' => $caseUuid,
                'task_type' => 'contact',
                'source' => 'portal',
                'label' => 'Lex L.',
                'derived_label' => null,
                'task_context' => 'Arch enemy',
                'category' => '2b',
                'date_of_last_exposure' => date('Y-m-d'),
                'communication' => 'staff',
                'informed_by_index' => 0,
                'questionnaire_uuid' => $questionnaireUuid,
                'created_at' => $now,
                'updated_at' => $now,
            ], [
                'uuid' => $taskUuidLois,
                'case_uuid' => $caseUuid,
                'task_type' => 'contact',
                'source' => 'portal',
                'label' => 'Lois L.',
                'derived_label' => null,
                'task_context' => "It's complicated",
                'category' => '1',
                'date_of_last_exposure' => null,
                'communication' => 'index',
                'informed_by_index' => 1,
                'questionnaire_uuid' => $questionnaireUuid,
                'created_at' => $now,
                'updated_at' => $now
            ], [
                'uuid' => $taskUuidMartha,
                'case_uuid' => $caseUuid,
                'task_type' => 'contact',
                'source' => 'portal',
                'label' => 'Martha K.',
                'derived_label' => null,
                'task_context' => "Mother",
                'category' => '2a',
                'date_of_last_exposure' => date('Y-m-d'),
                'communication' => 'index',
                'informed_by_index' => 0,
                'questionnaire_uuid' => $questionnaireUuid,
                'created_at' => $now,
                'updated_at' => $now
            ], [
                'uuid' => $taskUuidZod,
                'case_uuid' => $caseUuid,
                'task_type' => 'contact',
                'source' => 'portal',
                'label' => 'General Zod',
                'derived_label' => null,
                'task_context' => "Nemesis. Has no contact details answer",
                'category' => '3',
                'date_of_last_exposure' => date('Y-m-d'),
                'communication' => 'index',
                'informed_by_index' => 0,
                'questionnaire_uuid' => $questionnaireUuid,
                'created_at' => $now,
                'updated_at' => $now
            ],[
                'uuid' => $taskUuidAquaman,
                'case_uuid' => $caseUuid,
                'task_type' => 'contact',
                'source' => 'app',
                'label' => 'Aquaman',
                'derived_label' => 'Arthur Joseph Curry',
                'task_context' => "Swam in the same water",
                'category' => '2a',
                'date_of_last_exposure' => date('Y-m-d'),
                'communication' => 'index',
                'informed_by_index' => 1,
                'questionnaire_uuid' => $questionnaireUuid,
                'created_at' => $now,
                'updated_at' => $now
            ]
            ]);

            $questions = DB::table('question')
                ->where('questionnaire_uuid', '=', $questionnaireUuid)->get();

            foreach ($questions as $question) {
                if ($question->question_type == 'contactdetails') {
                    $contactQuestionUuid = (string)$question->uuid;
                } else if ($question->question_type == 'classificationdetails') {
                    $classificationQuestionUuid = (string)$question->uuid;
                } else if ($question->label == 'Geboortedatum') {
                    $birthdateQuestionUuid = (string)$question->uuid;
                } else if ($question->label == 'Waar ken je deze persoon van?') {
                    $relationshipQuestionUuid = (string)$question->uuid;
                } else if ($question->header == 'Prioriteit') {
                    $priorityQuestionUuid = (string)$question->uuid;
                }
            }

            DB::table('answer')->insert([[
                'uuid' => (string)Str::uuid(),
                'task_uuid' => $taskUuidLex,
                'question_uuid' => $classificationQuestionUuid,
                'cfd_cat_1_risk' => '0',
                'cfd_cat_2a_risk' => '1',
                'cfd_cat_2b_risk' => '1',
                'cfd_cat_3_risk' => '1',
                'created_at' => $now,
                'updated_at' => $now
            ], [
                'uuid' => (string)Str::uuid(),
                'task_uuid' => $taskUuidLois,
                'question_uuid' => $classificationQuestionUuid,
                'cfd_cat_1_risk' => '1',
                'cfd_cat_2a_risk' => '1',
                'cfd_cat_2b_risk' => '1',
                'cfd_cat_3_risk' => '0',
                'created_at' => $now,
                'updated_at' => $now
            ], [
                'uuid' => (string)Str::uuid(),
                'task_uuid' => $taskUuidMartha,
                'question_uuid' => $classificationQuestionUuid,
                'cfd_cat_1_risk' => '0',
                'cfd_cat_2a_risk' => '1',
                'cfd_cat_2b_risk' => '0',
                'cfd_cat_3_risk' => '0',
                'created_at' => $now,
                'updated_at' => $now
            ]]);

            DB::table('answer')->insert([[
                'uuid' => (string)Str::uuid(),
                'task_uuid' => $taskUuidLex,
                'question_uuid' => $contactQuestionUuid,
                'ctd_firstname' => 'Lex',
                'ctd_lastname' => 'Luthor',
                'ctd_email' => 'lex@luthor.dc',
                'ctd_phonenumber' => '0612345678',
                'created_at' => $now,
                'updated_at' => $now
            ], [
                'uuid' => (string)Str::uuid(),
                'task_uuid' => $taskUuidLois,
                'question_uuid' => $contactQuestionUuid,
                'ctd_firstname' => 'Lois',
                'ctd_lastname' => 'Lane',
                'ctd_email' => 'lane.lois@dailyplanet.dc',
                'ctd_phonenumber' => '+31612345678',
                'created_at' => $now,
                'updated_at' => $now
            ], [
                'uuid' => (string)Str::uuid(),
                'task_uuid' => $taskUuidMartha,
                'question_uuid' => $contactQuestionUuid,
                'ctd_firstname' => 'Martha',
                'ctd_lastname' => 'Kent',
                'ctd_email' => 'martha.kent@kansas.dc',
                'ctd_phonenumber' => '555-123145667',
                'created_at' => $now,
                'updated_at' => $now
            ],[
                'uuid' => (string)Str::uuid(),
                'task_uuid' => $taskUuidAquaman,
                'question_uuid' => $contactQuestionUuid,
                'ctd_firstname' => 'Arthur',
                'ctd_lastname' => 'Joseph Curry',
                'ctd_email' => 'water@everywhere.tst',
                'ctd_phonenumber' => '555-123142667',
                'created_at' => $now,
                'updated_at' => $now
            ]]);

            DB::table('answer')->insert([[
                'uuid' => (string)Str::uuid(),
                'task_uuid' => $taskUuidLois,
                'question_uuid' => $birthdateQuestionUuid,
                'spv_value' => Date('1976-10-12'),
                'created_at' => $now,
                'updated_at' => $now
            ], [
                'uuid' => (string)Str::uuid(),
                'task_uuid' => $taskUuidLex,
                'question_uuid' => $birthdateQuestionUuid,
                'spv_value' => Date('1970-10-11'),
                'created_at' => $now,
                'updated_at' => $now
            ], [
                'uuid' => (string)Str::uuid(),
                'task_uuid' => $taskUuidMartha,
                'question_uuid' => $birthdateQuestionUuid,
                'spv_value' => Date('1930-10-11'),
                'created_at' => $now,
                'updated_at' => $now
            ], [
                'uuid' => (string)Str::uuid(),
                'task_uuid' => $taskUuidLex,
                'question_uuid' => $relationshipQuestionUuid,
                'spv_value' => 'Overig',
                'created_at' => $now,
                'updated_at' => $now
            ], [
                'uuid' => (string)Str::uuid(),
                'task_uuid' => $taskUuidMartha,
                'question_uuid' => $relationshipQuestionUuid,
                'spv_value' => null,
                'created_at' => $now,
                'updated_at' => $now
            ], [
                'uuid' => (string)Str::uuid(),
                'task_uuid' => $taskUuidLex,
                'question_uuid' => $priorityQuestionUuid,
                'spv_value' => 'Nee',
                'created_at' => $now,
                'updated_at' => $now
            ], [
                'uuid' => (string)Str::uuid(),
                'task_uuid' => $taskUuidMartha,
                'question_uuid' => $priorityQuestionUuid,
                'spv_value' => 'Ja',
                'created_at' => $now,
                'updated_at' => $now
            ], [
                'uuid' => (string)Str::uuid(),
                'task_uuid' => $taskUuidZod,
                'question_uuid' => $priorityQuestionUuid,
                'spv_value' => 'Nee',
                'created_at' => $now,
                'updated_at' => $now
            ],[
                'uuid' => (string)Str::uuid(),
                'task_uuid' => $taskUuidAquaman,
                'question_uuid' => $priorityQuestionUuid,
                'spv_value' => 'Nee',
                'created_at' => $now,
                'updated_at' => $now
            ]]);

            $caseUuid = (string)Str::uuid();
            // Create a case for the dummy user (id 0), some tasks. Case is opened and not yet paired
            DB::table('covidcase')->insert([
                'name' => 'Carol Danvers',
                'uuid' => $caseUuid,
                'organisation_uuid' => $dummyOrgUuid,
                'owner' => $dummyUserUuid,
                'assigned_uuid' => $dummyUserUuid,
                'date_of_symptom_onset' => date('Y-m-d'),
                'index_submitted_at' => null,
                'status' => 'open',
                'pairing_expires_at' => Date::now()->addMinutes(60),
                'window_expires_at' => $tomorrow,
                'case_id' => 'ASGARD'.sprintf("%02d", $i),
                'created_at' => $now,
                'updated_at' => $now
            ]);

            DB::table('task')->insert([[
                'uuid' => (string)Str::uuid(),
                'case_uuid' => $caseUuid,
                'task_type' => 'contact',
                'source' => 'portal',
                'label' => 'Steve R.',
                'task_context' => 'Ally',
                'category' => '2a',
                'date_of_last_exposure' => date('Y-m-d'),
                'communication' => 'staff',
                'informed_by_index' => 0,
                'created_at' => $now,
                'updated_at' => $now,
                'export_id' => 'abcd1234',
                'exported_at' => $now,
                'copied_at' => $now,
            ], [
                'uuid' => (string)Str::uuid(),
                'case_uuid' => $caseUuid,
                'task_type' => 'contact',
                'source' => 'portal',
                'label' => 'Nick F.',
                'task_context' => "Discovered by",
                'category' => '3',
                'date_of_last_exposure' => date('Y-m-d'),
                'communication' => 'index',
                'informed_by_index' => 0,
                'created_at' => $now,
                'updated_at' => $now,
                'export_id' => null,
                'exported_at' => null,
                'copied_at' => null,
            ]]);

            $caseUuid = (string)Str::uuid();
            // Create an unassigned case.
            DB::table('covidcase')->insert([
                'name' => 'Diana Prince',
                'uuid' => $caseUuid,
                'organisation_uuid' => $dummyOrgUuid,
                'owner' => $dummyPlannerUuid,
                'assigned_uuid' => null,
                'date_of_symptom_onset' => null,
                'status' => 'draft',
                'case_id' => 'THEMYSCIRA'.sprintf("%02d", $i),
                'created_at' => $now,
                'updated_at' => $now
            ]);
        }
    }
}
