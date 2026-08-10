<?php

/**
 * ContactCall
 * Logs every time a logged-in user taps "Call Me" on a teacher's
 * portfolio, so the teacher can see who tried to reach them.
 */
class ContactCall extends Model
{
    protected static string $table = 'contact_calls';

    public static function log(int $teacherId, int $callerId): int
    {
        return self::insert([
            'teacher_id' => $teacherId,
            'caller_id'  => $callerId,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * History of who called a given teacher, most recent first.
     */
    public static function historyFor(int $teacherId, int $limit = 50): array
    {
        $stmt = self::conn()->prepare(
            'SELECT cc.id, cc.created_at, t.id AS caller_id, t.full_name AS caller_name,
                    t.email AS caller_email, t.profile_photo AS caller_photo
             FROM contact_calls cc
             INNER JOIN teachers t ON t.id = cc.caller_id
             WHERE cc.teacher_id = :teacher_id
             ORDER BY cc.created_at DESC
             LIMIT :limit'
        );
        $stmt->bindValue(':teacher_id', $teacherId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function countFor(int $teacherId): int
    {
        return self::count('teacher_id = :teacher_id', ['teacher_id' => $teacherId]);
    }
}
