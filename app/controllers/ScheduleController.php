<?php
namespace App\Controllers;

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Models\Schedule;
use App\Models\UserSubject;
use App\Models\Subject;
use App\Models\Template;
use App\Models\User;
use App\Helpers\GoogleCalendar;

class ScheduleController
{

    private $scheduleModel;
    private $userSubjectModel;
    private $subjectModel;
    private $templateModel;
    private $userModel;

    public function __construct()
    {
        $this->scheduleModel = new Schedule();
        $this->userSubjectModel = new UserSubject();
        $this->subjectModel = new Subject();
        $this->templateModel = new Template();
        $this->userModel = new User();
    }

    public function addSchedule(
        $userId,
        $subjectChoice,
        $templateId,
        $title,
        $desc,
        $start_dt,
        $end_dt
    ) {
        $user_subject_id = null;

        // ============================================================
        // SUBJECT HANDLING
        // ============================================================

        // 1️⃣ Pakai user_subject yang sudah ada
        if (!empty($subjectChoice['existing_user_subject_id'])) {
            $user_subject_id = (int) $subjectChoice['existing_user_subject_id'];

            // 2️⃣ Global subject
        } elseif (!empty($subjectChoice['global_subject_id'])) {

            // ✅ Cek dulu apakah user sudah punya
            $existing = $this->userSubjectModel
                ->findByUserAndSubject($userId, $subjectChoice['global_subject_id']);

            if ($existing) {
                $user_subject_id = $existing['id'];
            } else {
                $global = $this->subjectModel->find($subjectChoice['global_subject_id']);
                $name = $global ? $global['name'] : 'Unnamed Subject';

                $user_subject_id = $this->userSubjectModel->create(
                    $userId,
                    $subjectChoice['global_subject_id'],
                    $name
                );
            }

            // 3️⃣ Custom subject
        } else {
            $name = trim($subjectChoice['custom_name'] ?? 'Unnamed Subject');
            $user_subject_id = $this->userSubjectModel->create(
                $userId,
                null,
                $name
            );
        }

        // ============================================================
        // GOOGLE CALENDAR
        // ============================================================

        $user = $this->userModel->findById($userId);
        $google_event_id = null;

        if (!empty($user['provider_refresh_token'])) {
            $start_google = date('c', strtotime($start_dt));
            $end_google = date('c', strtotime($end_dt));
            $tz = $_ENV['TIMEZONE'] ?? 'Asia/Jakarta';

            $res = GoogleCalendar::createEventFromRefreshToken(
                $user['provider_refresh_token'],
                $title,
                $desc,
                $start_google,
                $end_google,
                $tz
            );

            if (!empty($res['success'])) {
                $google_event_id = $res['eventId'];
            }
        }

        // ============================================================
        // SAVE SCHEDULE
        // ============================================================

        $scheduleId = $this->scheduleModel->create(
            $userId,
            $user_subject_id,
            $templateId,
            $title,
            $desc,
            $start_dt,
            $end_dt,
            $google_event_id
        );

        return [
            'success' => true,
            'schedule_id' => $scheduleId,
            'google_event_id' => $google_event_id
        ];
    }
}
