<?php

class HomeController extends Controller
{
    public function index(): void
    {
        $filters = [
            'q'             => $this->input('q', ''),
            'subject'       => $this->input('subject', ''),
            'city'          => $this->input('city', ''),
            'qualification' => $this->input('qualification', ''),
            'teacher_type'  => $this->input('type', ''),
        ];
        $page = max(1, (int) $this->input('page', 1));

        $result = Teacher::filter($filters, $page, 9);

        View::render('home/index', [
            'title'         => 'Skoolyst Teachers — Build & Share Your Professional Portfolio',
            'teachers'      => $result['data'],
            'pagination'    => $result,
            'filters'       => $filters,
            'subjects'      => Teacher::distinctValues('subject'),
            'cities'        => Teacher::distinctValues('city'),
            'qualifications'=> Teacher::distinctValues('qualification'),
            'teacherTypes'  => [
                'school' => 'School Teacher', 'college' => 'College Teacher',
                'university' => 'University Professor', 'technical' => 'Technical Instructor',
                'medical' => 'Medical Faculty', 'science' => 'Science Teacher',
                'mathematics' => 'Mathematics Teacher', 'arts' => 'Arts Teacher',
                'computer_science' => 'Computer Science Teacher', 'general' => 'General Subject Teacher',
                'other' => 'Other',
            ],
        ]);
    }
}
