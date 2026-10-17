<?php

namespace Core\database;

/** Stable event metadata; independent of table-name parsing in the presentation layer. */
final class NotificationMetadata
{
    public static function operation(string $saved, string $entity, string $title): string
    {
        if (in_array($saved, ['insert', 'update', 'delete'], true)) return $saved;
        if (preg_match('/^database_(insert|update|delete)$/D', $entity, $match)) return $match[1];
        if (preg_match('/^(ایجاد|ثبت|ویرایش|حذف|Created|Updated|Removed|Data insert|Data update|Data delete)(?:\s|$)/u', $title, $match)) {
            return ['ایجاد'=>'insert', 'ثبت'=>'insert', 'ویرایش'=>'update', 'حذف'=>'delete', 'Created'=>'insert', 'Updated'=>'update', 'Removed'=>'delete', 'Data insert'=>'insert', 'Data update'=>'update', 'Data delete'=>'delete'][$match[1]];
        }
        return '';
    }

    public static function priority(string $entity, string $operation): string
    {
        if ($operation === 'delete') return 'high';
        if (in_array($entity, ['financial_system_payments', 'financial_system_transactions', 'financial_system_invoices', 'financial_system_installments', 'user_roles', 'user_permissions', 'academy_branch_member_roles', 'academy_branch_member_permissions', 'access_system_roles', 'access_system_permissions'], true)) return 'high';
        if (in_array($entity, ['academy_branch_bookings', 'academy_branch_course_terms', 'academy_branch_course_term_sessions', 'academy_branch_course_term_enrollments', 'academy_branch_member_contracts'], true)) return 'normal';
        return $operation === 'insert' ? 'low' : 'normal';
    }
}
