<?php
require_once __DIR__ .  '/../models/Job.php';

class DashboardController extends BaseController
{
    public function showDashboard()
    {
        $job = new Job();
        $jobs = $job->getAllJobs();
        // print '<pre>';
        // var_dump($jobs);
        // die();
        $this->render('dashboard', ['jobs' => $jobs]);
    }
}
