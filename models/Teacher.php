<?php

/**
 * Teacher
 * One table serves both roles ('teacher', 'super-admin') as requested.
 * Flexible portfolio sections (education, experience, skills, certifications,
 * projects, languages, awards, social_links) are stored as JSON columns —
 * this keeps the schema simple while remaining fully extensible per teacher,
 * and indexed scalar columns (subject, city, qualification, teacher_type,
 * status) keep the public directory filters fast at scale.
 */
class Teacher extends Model
{
    protected static string $table = 'teachers';

    public const JSON_FIELDS = [
        'educations', 'experiences', 'skills', 'certifications',
        'projects', 'languages', 'awards', 'social_links', 'services',
    ];

    public static function findByEmail(string $email): ?array
    {
        return self::findBy('email', $email);
    }

    public static function findBySlug(string $slug): ?array
    {
        return self::findBy('slug', $slug);
    }

    public static function findByUuid(string $uuid): ?array
    {
        return self::findBy('uuid', $uuid);
    }

    public static function create(array $data): int
    {
        $data['uuid'] = Helpers::uuid4();
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        foreach (self::JSON_FIELDS as $field) {
            if (!isset($data[$field])) {
                $data[$field] = '[]';
            }
        }

        return self::insert($data);
    }

    public static function updateProfile(int $id, array $data): bool
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return self::update($id, $data);
    }

    public static function updateJsonField(int $id, string $field, array $value): bool
    {
        if (!in_array($field, self::JSON_FIELDS, true)) {
            return false;
        }
        return self::update($id, [
            $field      => Helpers::jsonEncode($value),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public static function incrementViews(int $id): void
    {
        $stmt = self::conn()->prepare('UPDATE teachers SET views_count = views_count + 1 WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    /**
     * Public directory listing with optional filters + pagination.
     */
    public static function filter(array $filters, int $page = 1, int $perPage = 12): array
    {
        $where = ["role = 'teacher'", "status = 'active'", "is_public = 1"];
        $params = [];

        // if (!empty($filters['q'])) {
        //     $where[] = '(full_name LIKE :q OR profession_title LIKE :q OR bio LIKE :q)';
        //     $params['q'] = '%' . $filters['q'] . '%';
        // }

        if (!empty($filters['q'])) {
            $where[] = '(full_name LIKE :q1 OR profession_title LIKE :q2 OR bio LIKE :q3)';
            $params['q1'] = '%' . $filters['q'] . '%';
            $params['q2'] = '%' . $filters['q'] . '%';
            $params['q3'] = '%' . $filters['q'] . '%';
        }

        if (!empty($filters['subject'])) {
            $where[] = 'subject = :subject';
            $params['subject'] = $filters['subject'];
        }
        if (!empty($filters['city'])) {
            $where[] = 'city = :city';
            $params['city'] = $filters['city'];
        }
        if (!empty($filters['qualification'])) {
            $where[] = 'qualification = :qualification';
            $params['qualification'] = $filters['qualification'];
        }
        if (!empty($filters['teacher_type'])) {
            $where[] = 'teacher_type = :teacher_type';
            $params['teacher_type'] = $filters['teacher_type'];
        }

        $whereSql = implode(' AND ', $where);
        $offset = max(0, ($page - 1) * $perPage);

        $countStmt = self::conn()->prepare("SELECT COUNT(*) FROM teachers WHERE {$whereSql}");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $sql = "SELECT id, uuid, slug, full_name, profession_title, subject, city,
                       qualification, teacher_type, profile_photo, views_count
                FROM teachers
                WHERE {$whereSql}
                ORDER BY created_at DESC
                LIMIT :limit OFFSET :offset";
        $stmt = self::conn()->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue(':' . $k, $v);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'data' => $stmt->fetchAll(),
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'last_page' => (int) max(1, ceil($total / $perPage)),
        ];
    }

    public static function distinctValues(string $column): array
    {
        $allowed = ['subject', 'city', 'qualification', 'teacher_type'];
        if (!in_array($column, $allowed, true)) {
            return [];
        }
        $stmt = self::conn()->query(
            "SELECT DISTINCT {$column} FROM teachers
             WHERE role = 'teacher' AND status = 'active' AND is_public = 1
                   AND {$column} IS NOT NULL AND {$column} <> ''
             ORDER BY {$column} ASC"
        );
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public static function adminList(int $page = 1, int $perPage = 20, string $search = ''): array
    {
        $where = "role = 'teacher'";
        $params = [];
        if ($search !== '') {
            $where .= ' AND (full_name LIKE :s OR email LIKE :s)';
            $params['s'] = '%' . $search . '%';
        }
        $offset = ($page - 1) * $perPage;

        $countStmt = self::conn()->prepare("SELECT COUNT(*) FROM teachers WHERE {$where}");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $stmt = self::conn()->prepare(
            "SELECT id, uuid, slug, full_name, email, city, subject, status, created_at
             FROM teachers WHERE {$where} ORDER BY created_at DESC LIMIT :limit OFFSET :offset"
        );
        foreach ($params as $k => $v) $stmt->bindValue(':' . $k, $v);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'data' => $stmt->fetchAll(),
            'total' => $total,
            'page' => $page,
            'last_page' => (int) max(1, ceil($total / $perPage)),
        ];
    }

    /**
     * Human-readable labels for the profile sections most worth having filled
     * in before a profile looks complete. Used to build admin "reminder"
     * emails and any future "profile completeness" UI.
     */
    public const COMPLETENESS_FIELDS = [
        'profile_photo'   => 'Profile photo',
        'bio'             => 'Bio / About',
        'profession_title'=> 'Profession title',
        'educations'      => 'Education',
        'experiences'     => 'Experience',
        'skills'          => 'Skills',
        'certifications'  => 'Certificates',
        'awards'          => 'Awards',
    ];

    /**
     * Returns the labels of sections that are empty/missing for a teacher.
     * Expects a full row (e.g. from Teacher::find()), not the trimmed
     * columns returned by adminList().
     */
    public static function missingFields(array $teacher): array
    {
        $missing = [];
        foreach (self::COMPLETENESS_FIELDS as $field => $label) {
            if (in_array($field, self::JSON_FIELDS, true)) {
                $decoded = Helpers::jsonDecode($teacher[$field] ?? null);
                if (empty($decoded)) {
                    $missing[] = $label;
                }
            } else {
                if (trim((string) ($teacher[$field] ?? '')) === '') {
                    $missing[] = $label;
                }
            }
        }
        return $missing;
    }
}
