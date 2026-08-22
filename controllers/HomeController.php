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
            'title'         => 'Find Qualified Teachers in Pakistan | Skoolyst Teachers',
            'description'   => 'Discover qualified school, college, university, science, computer, mathematics and private teachers in Pakistan. Browse teacher portfolios and connect with the right educator.',
            'canonical'     => Helpers::url('/'),
            'teachers'      => $result['data'],
            'pagination'    => $result,
            'filters'       => $filters,
            'subjects'      => Teacher::distinctValues('subject'),
            'cities'        => Teacher::distinctValues('city'),
            'qualifications'=> Teacher::distinctValues('qualification'),
            'teacherTypes'  => Teacher::TEACHER_TYPE_LABELS,
        ]);
    }
}
