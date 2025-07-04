<?php

require_once 'models/Attendance.php';
require_once 'models/Employee.php';

class AttendanceController extends BaseController {
    
    public function index() {
        $attendance = new Attendance($this->db);
        $employee = new Employee($this->db);
        
        $date = $_GET['date'] ?? date('Y-m-d');
        $attendances = $attendance->getAttendanceWithEmployee($date);
        $employees = $employee->getEmployeesWithDetails();
        
        $data = [
            'page_title' => 'Presensi Pegawai',
            'attendances' => $attendances,
            'employees' => $employees,
            'selected_date' => $date,
            'additional_css' => 1,
            'additional_js' => 1
        ];
        
        $this->view('attendance/index', $data);
    }
    
    public function clockIn() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $attendance = new Attendance($this->db);
            $pegawai_id = $_POST['pegawai_id'];
            
            try {
                $result = $attendance->clockIn($pegawai_id);
                if ($result) {
                    $this->redirect('attendance', 'Berhasil clock in!', 'success');
                } else {
                    $this->redirect('attendance', 'Sudah melakukan clock in hari ini!', 'warning');
                }
            } catch (Exception $e) {
                $this->redirect('attendance', 'Error: ' . $e->getMessage(), 'error');
            }
        }
    }
    
    public function clockOut() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $attendance = new Attendance($this->db);
            $pegawai_id = $_POST['pegawai_id'];
            
            try {
                $result = $attendance->clockOut($pegawai_id);
                if ($result) {
                    $this->redirect('attendance', 'Berhasil clock out!', 'success');
                } else {
                    $this->redirect('attendance', 'Belum clock in atau sudah clock out!', 'warning');
                }
            } catch (Exception $e) {
                $this->redirect('attendance', 'Error: ' . $e->getMessage(), 'error');
            }
        }
    }
}
