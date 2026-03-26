-- ============================================================
-- Database: sintem
-- Description: SINTEM (Sistem Informasi Sekolah) - School
--              Information System for managing announcements,
--              events, lost & found, and anonymous reports.
-- Server Version: MySQL 8.x
-- Last Updated: 2026-03-24 (v2 — separated photos vs attachments)
-- ============================================================

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8mb4 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- ============================================================
-- Drop tables in reverse dependency order (safe re-run)
-- ============================================================
DROP TABLE IF EXISTS `attachments`;
DROP TABLE IF EXISTS `photos`;
DROP TABLE IF EXISTS `anonymous_reports`;
DROP TABLE IF EXISTS `lost_founds`;
DROP TABLE IF EXISTS `events`;
DROP TABLE IF EXISTS `event_locations`;
DROP TABLE IF EXISTS `event_categories`;
DROP TABLE IF EXISTS `announcements`;
DROP TABLE IF EXISTS `report_categories`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `roles`;
DROP TABLE IF EXISTS `sessions`;
DROP TABLE IF EXISTS `cache`;
DROP TABLE IF EXISTS `migrations`;

-- ============================================================
-- TABLE: roles
-- Description: Master table for user roles within the system.
--   Each role determines what features and data a user can
--   access. Roles include staff types, teachers, and students.
-- ============================================================
CREATE TABLE `roles` (
  `id`         INT           NOT NULL AUTO_INCREMENT,
  `role_name`  VARCHAR(50)   NOT NULL COMMENT 'Unique name for the role (e.g. superadmin, BK, TU)',
  `description` VARCHAR(255) NULL     COMMENT 'Human-readable explanation of the role responsibilities',
  `created_at` TIMESTAMP     NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP     NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_roles_name` (`role_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
  COMMENT='Master data for all user roles in the SINTEM system';

INSERT INTO `roles` (`id`, `role_name`, `description`) VALUES
  (1, 'superadmin',   'Full system access; manages users, roles, and all content'),
  (2, 'user',         'Regular student or general user with read-only access to public content'),
  (3, 'BK',           'Bimbingan Konseling – handles student counseling and bullying reports'),
  (4, 'TU',           'Tata Usaha – administrative staff managing school facilities and announcements'),
  (5, 'Kesiswaan',    'Student affairs division; handles discipline reports'),
  (6, 'Wali Kelas',   'Homeroom teacher with access to class-specific information'),
  (7, 'Guru NA',      'Subject teacher handling academic (KBM) related reports'),
  (8, 'Guru Jurusan', 'Vocational/department teacher with department-specific access');

-- ============================================================
-- TABLE: users
-- Description: Stores all registered users of the system.
--   Identifier can be a NIS (student number) or a staff code.
--   Passwords are stored as bcrypt hashes.
-- ============================================================
CREATE TABLE `users` (
  `id`         INT           NOT NULL AUTO_INCREMENT,
  `identifier` VARCHAR(50)   NOT NULL COMMENT 'Unique login ID — NIS for students, staff code for teachers/staff',
  `name`       VARCHAR(100)  NOT NULL COMMENT 'Full name of the user',
  `email`      VARCHAR(150)  NULL     COMMENT 'Optional email address for notifications',
  `password`   VARCHAR(255)  NOT NULL COMMENT 'Bcrypt-hashed password',
  `role_id`    INT           NOT NULL COMMENT 'References roles.id to determine permissions',
  `status`     ENUM('active','inactive') NOT NULL DEFAULT 'active' COMMENT 'active = can login; inactive = account disabled',
  `last_login` TIMESTAMP     NULL     COMMENT 'Timestamp of the most recent successful login',
  `created_at` TIMESTAMP     NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP     NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_identifier` (`identifier`),
  KEY `idx_users_role_id` (`role_id`),
  KEY `idx_users_status` (`status`),
  CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
  COMMENT='All system users — students and school staff';

-- Default password for all seed users is "password" (bcrypt hash)
INSERT INTO `users` (`id`, `identifier`, `name`, `email`, `password`, `role_id`, `status`) VALUES
  (1, 'admin_stemba', 'Super Admin Utama',       'admin@stemba.sch.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 'active'),
  (2, '220001',       'Muhammad Rizki',           'rizki@siswa.sch.id',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, 'active'),
  (3, 'bk_budi',      'Drs. Budi Raharjo',        'budi@stemba.sch.id',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'active'),
  (4, 'tu_santi',     'Santi Susanti',             'santi@stemba.sch.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 4, 'active');

-- ============================================================
-- TABLE: announcements
-- Description: School-wide announcements published by staff.
--   Announcements can have attachments (see attachments table).
--   The is_published flag allows drafts before going live.
-- ============================================================
CREATE TABLE `announcements` (
  `id`           INT           NOT NULL AUTO_INCREMENT,
  `title`        VARCHAR(255)  NOT NULL COMMENT 'Short headline of the announcement',
  `content`      TEXT          NOT NULL COMMENT 'Full body text of the announcement',
  `is_published` TINYINT(1)    NOT NULL DEFAULT 1 COMMENT '1 = visible to users; 0 = draft/hidden',
  `created_by`   INT           NOT NULL COMMENT 'User ID of the staff member who created this announcement',
  `created_at`   TIMESTAMP     NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   TIMESTAMP     NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_announcements_created_by` (`created_by`),
  KEY `idx_announcements_published` (`is_published`),
  CONSTRAINT `fk_announcements_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
  COMMENT='School announcements published by staff to all users';

INSERT INTO `announcements` (`id`, `title`, `content`, `is_published`, `created_by`, `created_at`) VALUES
  (1, 'Info Ujian', 'Jadwal ujian semester genap telah ditetapkan. Silakan unduh lampiran untuk melihat jadwal lengkap.', 1, 4, '2026-03-20 04:30:49');

-- ============================================================
-- TABLE: event_locations
-- Description: Master list of named physical locations on
--   school grounds where events can be held. Centralising
--   locations avoids free-text typos and makes filtering
--   events by venue easy (e.g. "semua acara di Aula Utama").
-- ============================================================
CREATE TABLE `event_locations` (
  `id`          INT          NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(150) NOT NULL COMMENT 'Display name of the venue (e.g. Aula Utama, Lapangan Sepak Bola)',
  `description` VARCHAR(255) NULL     COMMENT 'Optional detail about the location (capacity, floor, building)',
  `created_at`  TIMESTAMP    NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  TIMESTAMP    NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_event_locations_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
  COMMENT='Master data of school venues/locations used for events';

INSERT INTO `event_locations` (`id`, `name`, `description`) VALUES
  (1, 'Aula Utama',           'Ruang serbaguna utama, kapasitas ±500 orang'),
  (2, 'Lapangan Sepak Bola',  'Lapangan outdoor di belakang sekolah'),
  (3, 'Lapangan Upacara',     'Lapangan utama untuk upacara bendera'),
  (4, 'Ruang Kelas',          'Kegiatan berlangsung di masing-masing kelas'),
  (5, 'Lab Komputer',         'Laboratorium komputer lantai 2'),
  (6, 'Perpustakaan',         'Ruang baca dan referensi sekolah'),
  (7, 'Masjid Sekolah',       'Mushola/masjid untuk kegiatan keagamaan'),
  (8, 'Lapangan Basket',      'Lapangan olahraga basket di samping gedung');

-- ============================================================
-- TABLE: event_categories
-- Description: Master list of event types/categories to
--   classify what kind of activity an event is. Used for
--   filtering and display grouping on the calendar.
-- ============================================================
CREATE TABLE `event_categories` (
  `id`          INT          NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(100) NOT NULL COMMENT 'Category label (e.g. Upacara, Workshop, Olahraga)',
  `color`       VARCHAR(7)   NULL     COMMENT 'Hex color code for calendar UI display (e.g. #FF5733)',
  `description` VARCHAR(255) NULL     COMMENT 'Brief explanation of what events fall under this category',
  `created_at`  TIMESTAMP    NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  TIMESTAMP    NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_event_categories_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
  COMMENT='Master data of event categories for classification and calendar color-coding';

INSERT INTO `event_categories` (`id`, `name`, `color`, `description`) VALUES
  (1, 'Upacara',      '#4A90D9', 'Upacara bendera dan acara seremonial resmi'),
  (2, 'Workshop',     '#F5A623', 'Pelatihan, seminar, atau kegiatan pengembangan diri'),
  (3, 'Sosial',       '#7ED321', 'Bakti sosial, penggalangan dana, dan kegiatan kemasyarakatan'),
  (4, 'Olahraga',     '#D0021B', 'Pertandingan, turnamen, dan kegiatan olahraga'),
  (5, 'Keagamaan',    '#9B59B6', 'Perayaan hari besar keagamaan dan kegiatan rohani'),
  (6, 'Akademik',     '#1ABC9C', 'Ujian, olimpiade, pameran karya, dan kegiatan akademik'),
  (7, 'Seni & Budaya','#E67E22', 'Pentas seni, lomba, dan kegiatan budaya sekolah'),
  (8, 'Lainnya',      '#95A5A6', 'Kegiatan yang tidak masuk kategori di atas');

-- ============================================================
-- TABLE: events
-- Description: School calendar events such as ceremonies,
--   competitions, or academic activities. Each event has a
--   category (from event_categories) and a location (from
--   event_locations). Events can also span multiple days via
--   event_date_end, and support photo & file attachments.
-- ============================================================
CREATE TABLE `events` (
  `id`           INT           NOT NULL AUTO_INCREMENT,
  `event_name`   VARCHAR(255)  NOT NULL COMMENT 'Name/title of the event',
  `category_id`  INT           NOT NULL COMMENT 'References event_categories.id — type of event',
  `location_id`  INT           NULL     COMMENT 'References event_locations.id — where the event is held (NULL = TBD)',
  `event_date`   DATE          NOT NULL COMMENT 'Start date of the event',
  `event_date_end` DATE        NULL     COMMENT 'End date for multi-day events; NULL means single-day',
  `description`  TEXT          NULL     COMMENT 'Detailed description or agenda of the event',
  `is_published` TINYINT(1)    NOT NULL DEFAULT 1 COMMENT '1 = visible to users; 0 = draft/hidden',
  `created_by`   INT           NOT NULL COMMENT 'User ID of the staff member who created this event',
  `created_at`   TIMESTAMP     NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   TIMESTAMP     NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_events_category_id` (`category_id`),
  KEY `idx_events_location_id` (`location_id`),
  KEY `idx_events_created_by` (`created_by`),
  KEY `idx_events_event_date` (`event_date`),
  KEY `idx_events_published` (`is_published`),
  CONSTRAINT `fk_events_category`  FOREIGN KEY (`category_id`) REFERENCES `event_categories` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_events_location`  FOREIGN KEY (`location_id`) REFERENCES `event_locations`  (`id`) ON UPDATE CASCADE ON DELETE SET NULL,
  CONSTRAINT `fk_events_user`      FOREIGN KEY (`created_by`) REFERENCES `users`             (`id`) ON UPDATE CASCADE,
  CONSTRAINT `chk_events_date_range` CHECK (event_date_end IS NULL OR event_date_end >= event_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
  COMMENT='School events with category classification and named venue location';

-- ============================================================
-- TABLE: lost_founds
-- Description: Lost and Found board where students and staff
--   can post items they have lost or found on school grounds.
--   Items go through a workflow: pending → approved → solved.
-- ============================================================
CREATE TABLE `lost_founds` (
  `id`          INT           NOT NULL AUTO_INCREMENT,
  `user_id`     INT           NOT NULL COMMENT 'User who submitted the lost/found report',
  `type`        ENUM('lost','found') NOT NULL DEFAULT 'lost' COMMENT 'Whether the item was lost or found',
  `item_name`   VARCHAR(100)  NOT NULL COMMENT 'Name or short label of the item (e.g. Tas Ransel Hitam)',
  `description` TEXT          NULL     COMMENT 'Additional details: color, brand, last seen location, etc.',
  `found_at`    VARCHAR(150)  NULL     COMMENT 'Location where the item was lost or found',
  `status`      ENUM('pending','approved','solved') NOT NULL DEFAULT 'pending'
                              COMMENT 'pending = awaiting admin review; approved = publicly listed; solved = item returned',
  `created_at`  TIMESTAMP     NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  TIMESTAMP     NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_lost_founds_user_id` (`user_id`),
  KEY `idx_lost_founds_status` (`status`),
  KEY `idx_lost_founds_type` (`type`),
  CONSTRAINT `fk_lost_founds_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
  COMMENT='Lost and found submissions from students and staff';

-- ============================================================
-- TABLE: report_categories
-- Description: Categories for anonymous reports. Each category
--   is mapped to a responsible role who will handle reports
--   in that category (e.g. BK handles bullying reports).
-- ============================================================
CREATE TABLE `report_categories` (
  `id`                  INT          NOT NULL AUTO_INCREMENT,
  `category_name`       VARCHAR(100) NOT NULL COMMENT 'Display name of the report category',
  `description`         VARCHAR(255) NULL     COMMENT 'What kind of issues belong in this category',
  `responsible_role_id` INT          NOT NULL COMMENT 'Role ID of the staff responsible for reviewing these reports',
  `created_at`          TIMESTAMP    NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`          TIMESTAMP    NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_report_categories_role` (`responsible_role_id`),
  CONSTRAINT `fk_report_categories_role` FOREIGN KEY (`responsible_role_id`) REFERENCES `roles` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
  COMMENT='Category master for anonymous reports, each mapped to a responsible staff role';

INSERT INTO `report_categories` (`id`, `category_name`, `description`, `responsible_role_id`) VALUES
  (1, 'Perundungan/Bullying', 'Laporan terkait intimidasi, kekerasan fisik/verbal antar siswa',           3),
  (2, 'Fasilitas Sekolah',    'Kerusakan atau masalah pada fasilitas dan infrastruktur sekolah',           4),
  (3, 'Kedisiplinan',         'Pelanggaran tata tertib sekolah oleh siswa',                               5),
  (4, 'Masalah KBM',          'Gangguan atau kendala dalam kegiatan belajar mengajar di kelas',            7);

-- ============================================================
-- TABLE: anonymous_reports
-- Description: Allows students to submit reports anonymously.
--   Each report gets a unique ticket number for follow-up
--   without revealing the reporter's identity. Reports are
--   routed to the responsible staff based on category.
-- ============================================================
CREATE TABLE `anonymous_reports` (
  `id`             INT          NOT NULL AUTO_INCREMENT,
  `ticket_number`  VARCHAR(20)  NOT NULL COMMENT 'Unique public-facing code for the reporter to track status (e.g. TKT-001)',
  `category_id`    INT          NOT NULL COMMENT 'References report_categories.id for routing to responsible staff',
  `report_content` TEXT         NOT NULL COMMENT 'The full text of the anonymous report',
  `admin_notes`    TEXT         NULL     COMMENT 'Internal notes added by staff while handling the report',
  `status`         ENUM('pending','in_progress','solved') NOT NULL DEFAULT 'pending'
                               COMMENT 'pending = not yet reviewed; in_progress = being handled; solved = case closed',
  `resolved_at`    TIMESTAMP    NULL     COMMENT 'Timestamp when the report was marked as solved',
  `created_at`     TIMESTAMP    NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     TIMESTAMP    NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_anonymous_reports_ticket` (`ticket_number`),
  KEY `idx_anonymous_reports_category` (`category_id`),
  KEY `idx_anonymous_reports_status` (`status`),
  CONSTRAINT `fk_anonymous_reports_category` FOREIGN KEY (`category_id`) REFERENCES `report_categories` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
  COMMENT='Anonymous student/staff reports routed to responsible roles by category';

INSERT INTO `anonymous_reports` (`id`, `ticket_number`, `category_id`, `report_content`, `status`, `created_at`) VALUES
  (1, 'TKT-001', 1, 'Laporan bullying di parkiran belakang. Ada sekelompok siswa yang mengintimidasi adik kelas setiap pulang sekolah.', 'pending', '2026-03-20 04:30:48');

-- ============================================================
-- TABLE: photos
-- Description: Image/photo uploads linked to announcements,
--   events, lost_founds, and anonymous_reports. Strictly for
--   image files (JPEG, PNG, GIF, WEBP, etc.).
--   Actual files live on the filesystem/object storage.
-- ============================================================
CREATE TABLE `photos` (
  `id`          INT           NOT NULL AUTO_INCREMENT,
  `source_type` ENUM('announcement','event','lost_found','anonymous_report') NOT NULL
                              COMMENT 'Parent entity type this photo belongs to',
  `source_id`   INT           NOT NULL COMMENT 'Primary key of the parent entity row',
  `file_name`   VARCHAR(255)  NOT NULL COMMENT 'Original filename as uploaded (e.g. foto_kejadian.jpg)',
  `file_path`   TEXT          NOT NULL COMMENT 'Relative storage path (e.g. uploads/photos/foto_kejadian.jpg)',
  `file_type`   VARCHAR(100)  NOT NULL COMMENT 'MIME type — must be an image type (e.g. image/jpeg, image/png)',
  `file_size`   INT           NOT NULL COMMENT 'File size in bytes',
  `uploaded_by` INT           NULL     COMMENT 'User who uploaded; NULL for anonymous report photos',
  `created_at`  TIMESTAMP     NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  TIMESTAMP     NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_photos_source` (`source_type`, `source_id`),
  KEY `idx_photos_uploaded_by` (`uploaded_by`),
  CONSTRAINT `fk_photos_user` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
  COMMENT='Image/photo uploads for announcements, events, lost_founds, and reports';

INSERT INTO `photos` (`id`, `source_type`, `source_id`, `file_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`) VALUES
  (1, 'anonymous_report', 1, 'bukti_foto.jpg', 'uploads/photos/reports/bukti_foto.jpg', 'image/jpeg', 2048000, NULL);

-- ============================================================
-- TABLE: attachments
-- Description: Non-image attachments for Announcements and
--   Events ONLY. Supports two attachment types:
--     • file  — uploaded document (PDF, DOCX, XLSX, etc.)
--               max 5 MB per file enforced at application layer
--     • link  — external URL (Google Drive, YouTube, etc.)
--   file_name, file_path, file_type, file_size are required
--   when attachment_type = 'file', and NULL for 'link'.
--   link_url is required when attachment_type = 'link', NULL
--   for 'file'.
-- ============================================================
CREATE TABLE `attachments` (
  `id`              INT           NOT NULL AUTO_INCREMENT,
  `source_type`     ENUM('announcement','event') NOT NULL
                                  COMMENT 'Only announcements and events support attachments',
  `source_id`       INT           NOT NULL COMMENT 'Primary key of the parent announcement or event',
  `attachment_type` ENUM('file','link') NOT NULL
                                  COMMENT 'file = uploaded document; link = external URL',
  -- File fields (used when attachment_type = 'file')
  `file_name`       VARCHAR(255)  NULL     COMMENT 'Original filename (e.g. Jadwal_UAS.pdf)',
  `file_path`       TEXT          NULL     COMMENT 'Relative storage path (e.g. uploads/attachments/Jadwal_UAS.pdf)',
  `file_type`       VARCHAR(100)  NULL     COMMENT 'MIME type (e.g. application/pdf, application/vnd.ms-excel)',
  `file_size`       INT           NULL     COMMENT 'File size in bytes — max 5,242,880 (5 MB) enforced by app',
  -- Link fields (used when attachment_type = 'link')
  `link_url`        TEXT          NULL     COMMENT 'Full URL for external link attachments (e.g. https://drive.google.com/...)',
  `link_label`      VARCHAR(255)  NULL     COMMENT 'Human-friendly display label for the link (e.g. "Lihat di Google Drive")',
  -- Common fields
  `label`           VARCHAR(255)  NULL     COMMENT 'Optional short description of what this attachment is',
  `uploaded_by`     INT           NULL     COMMENT 'User ID who added this attachment',
  `created_at`      TIMESTAMP     NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      TIMESTAMP     NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_attachments_source` (`source_type`, `source_id`),
  KEY `idx_attachments_type` (`attachment_type`),
  KEY `idx_attachments_uploaded_by` (`uploaded_by`),
  CONSTRAINT `fk_attachments_user` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  -- Enforce: file columns must be set for 'file', link_url must be set for 'link'
  CONSTRAINT `chk_attachment_file` CHECK (
    attachment_type != 'file' OR (file_name IS NOT NULL AND file_path IS NOT NULL AND file_size IS NOT NULL)
  ),
  CONSTRAINT `chk_attachment_link` CHECK (
    attachment_type != 'link' OR link_url IS NOT NULL
  ),
  -- Enforce max file size of 5 MB (5,242,880 bytes)
  CONSTRAINT `chk_attachment_max_size` CHECK (
    file_size IS NULL OR file_size <= 5242880
  )
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
  COMMENT='File and link attachments for announcements and events only (max 5 MB per file)';

INSERT INTO `attachments` (`id`, `source_type`, `source_id`, `attachment_type`, `file_name`, `file_path`, `file_type`, `file_size`, `link_url`, `link_label`, `label`, `uploaded_by`) VALUES
  -- File attachment: PDF document on an announcement
  (1, 'announcement', 1, 'file', 'jadwal_uas.pdf', 'uploads/attachments/announcements/jadwal_uas.pdf', 'application/pdf', 512000, NULL, NULL, 'Jadwal UAS Semester Genap', 4),
  -- Link attachment: Google Drive link on the same announcement
  (2, 'announcement', 1, 'link', NULL, NULL, NULL, NULL, 'https://drive.google.com/file/d/example', 'Lihat di Google Drive', 'Versi lengkap jadwal', 4);

-- ============================================================
-- TABLE: notifications
-- Description: In-app notifications sent to specific users
--   when actions occur (e.g. report status updated, new
--   announcement published). Supports read/unread tracking.
-- ============================================================
CREATE TABLE `notifications` (
  `id`          INT           NOT NULL AUTO_INCREMENT,
  `user_id`     INT           NOT NULL COMMENT 'Recipient user ID',
  `title`       VARCHAR(255)  NOT NULL COMMENT 'Short notification headline',
  `body`        TEXT          NULL     COMMENT 'Full notification message',
  `type`        VARCHAR(50)   NOT NULL COMMENT 'Category of notification (e.g. report_update, announcement, lost_found)',
  `reference_id` INT          NULL     COMMENT 'Optional ID of the related entity (e.g. report ID)',
  `is_read`     TINYINT(1)    NOT NULL DEFAULT 0 COMMENT '0 = unread; 1 = read',
  `read_at`     TIMESTAMP     NULL     COMMENT 'Timestamp when the user marked it as read',
  `created_at`  TIMESTAMP     NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_notifications_user_id` (`user_id`),
  KEY `idx_notifications_is_read` (`is_read`),
  CONSTRAINT `fk_notifications_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
  COMMENT='In-app notification records for users, supporting read/unread state';

-- ============================================================
-- TABLE: audit_logs
-- Description: Tracks important actions performed by users
--   for accountability and security review. Logs who did what
--   and when, along with the before/after state for changes.
-- ============================================================
CREATE TABLE `audit_logs` (
  `id`         BIGINT        NOT NULL AUTO_INCREMENT,
  `user_id`    INT           NULL     COMMENT 'User who performed the action (NULL for system/unauthenticated actions)',
  `action`     VARCHAR(100)  NOT NULL COMMENT 'Action performed (e.g. create, update, delete, login, logout)',
  `table_name` VARCHAR(100)  NULL     COMMENT 'Database table affected by the action',
  `record_id`  INT           NULL     COMMENT 'Primary key of the affected record',
  `old_values` JSON          NULL     COMMENT 'Snapshot of the record before the change (for update/delete)',
  `new_values` JSON          NULL     COMMENT 'Snapshot of the record after the change (for create/update)',
  `ip_address` VARCHAR(45)   NULL     COMMENT 'IP address of the user at the time of the action',
  `user_agent` TEXT          NULL     COMMENT 'Browser/client user-agent string',
  `created_at` TIMESTAMP     NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_audit_logs_user_id` (`user_id`),
  KEY `idx_audit_logs_table_record` (`table_name`, `record_id`),
  KEY `idx_audit_logs_created_at` (`created_at`),
  CONSTRAINT `fk_audit_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
  COMMENT='Audit trail for all significant actions within the system';

-- ============================================================
-- TABLE: sessions
-- Description: Laravel session storage. Tracks active user
--   sessions keyed by a unique session ID. Used for
--   authentication state management server-side.
-- ============================================================
CREATE TABLE `sessions` (
  `id`            VARCHAR(255)      NOT NULL COMMENT 'Unique session token',
  `user_id`       BIGINT UNSIGNED   NULL     COMMENT 'Authenticated user ID, NULL for guest sessions',
  `ip_address`    VARCHAR(45)       NULL     COMMENT 'Client IP address at session creation',
  `user_agent`    TEXT              NULL     COMMENT 'Client browser/app user-agent string',
  `payload`       LONGTEXT          NOT NULL COMMENT 'Serialized (base64) session data',
  `last_activity` INT               NOT NULL COMMENT 'Unix timestamp of the last request in this session',
  PRIMARY KEY (`id`),
  KEY `idx_sessions_user_id` (`user_id`),
  KEY `idx_sessions_last_activity` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Laravel server-side session storage';

-- ============================================================
-- TABLE: cache
-- Description: Laravel cache storage used for caching query
--   results, computed data, or API responses to improve
--   performance. Entries expire based on the expiration field.
-- ============================================================
CREATE TABLE `cache` (
  `key`        VARCHAR(255)      NOT NULL COMMENT 'Unique cache key',
  `value`      MEDIUMTEXT        NOT NULL COMMENT 'Serialized cached value',
  `expiration` INT               NOT NULL COMMENT 'Unix timestamp after which the cache entry is considered stale',
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Laravel application cache store';

-- ============================================================
-- TABLE: migrations
-- Description: Laravel migration tracking table. Records which
--   database migration files have been executed and in what
--   batch, preventing duplicate runs.
-- ============================================================
CREATE TABLE `migrations` (
  `id`        INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `migration` VARCHAR(255)  NOT NULL COMMENT 'Migration file name (e.g. 2024_01_01_000000_create_users_table)',
  `batch`     INT           NOT NULL COMMENT 'Batch number grouping migrations run together',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Laravel migration run history';

-- ============================================================
-- Restore original settings
-- ============================================================
/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;