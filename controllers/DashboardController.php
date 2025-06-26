<?php

require_once 'models/BaseModel.php';
require_once 'models/Student.php';
require_once 'models/Employee.php';

class DashboardController extends BaseController {
    
    public function index() {
        $student = new Student($this->db);
        $employee = new Employee($this->db);
        
        // Get statistics
        $total_students = count($student->read(['status' => 'aktif']));
        $total_employees = count($employee->read(['status' => 'aktif']));
        
        // Get financial data (placeholder for now)
        $monthly_income = 0;
        $monthly_expense = 0;
        
        $data = [
            'page_title' => 'Dashboard',
            'total_students' => $total_students,
            'total_employees' => $total_employees,
            'monthly_income' => $monthly_income,
            'monthly_expense' => $monthly_expense
        ];
        
        $this->view('dashboard/index', $data);
    }
}
