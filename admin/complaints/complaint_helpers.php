<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once(__DIR__ . "/../../config/db.php");

$hostel_name = $_SESSION['hostel_name'] ?? '';

function e($value)
{
    return htmlspecialchars(
        $value ?? '',
        ENT_QUOTES,
        'UTF-8'
    );
}

function complaintPriorityClass($priority)
{
    return match ($priority) {
        'Low'    => 'priority-low',
        'Medium' => 'priority-medium',
        'High'   => 'priority-high',
        'Urgent' => 'priority-urgent',
        default  => 'priority-default'
    };
}

function complaintStatusClass($status)
{
    return match ($status) {
        'Open'        => 'status-open',
        'In-Progress' => 'status-progress',
        'Resolved'    => 'status-resolved',
        'Rejected'    => 'status-rejected',
        default       => 'status-default'
    };
}

function allowedComplaintStatuses()
{
    return [
        'Open',
        'In-Progress',
        'Resolved',
        'Rejected'
    ];
}