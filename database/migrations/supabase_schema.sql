-- Supabase PostgreSQL Schema
-- Run this in Supabase SQL Editor

CREATE EXTENSION IF NOT EXISTS "pgcrypto";

-- roles
CREATE TABLE IF NOT EXISTS roles (
    id          BIGSERIAL PRIMARY KEY,
    role_name   VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    created_at  TIMESTAMP DEFAULT NOW(),
    updated_at  TIMESTAMP DEFAULT NOW()
);

-- users
CREATE TABLE IF NOT EXISTS users (
    id         BIGSERIAL PRIMARY KEY,
    identifier VARCHAR(50) NOT NULL UNIQUE,
    name       VARCHAR(100) NOT NULL,
    email      VARCHAR(255),
    password   VARCHAR(255) NOT NULL,
    role_id    BIGINT REFERENCES roles(id),
    status     VARCHAR(20) DEFAULT 'active',
    last_login TIMESTAMP,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW()
);

-- sessions
CREATE TABLE IF NOT EXISTS sessions (
    id            VARCHAR(255) PRIMARY KEY,
    user_id       BIGINT REFERENCES users(id) ON DELETE CASCADE,
    ip_address    VARCHAR(45),
    user_agent    TEXT,
    payload       TEXT NOT NULL,
    last_activity BIGINT NOT NULL
);

-- cache
CREATE TABLE IF NOT EXISTS cache (
    key        VARCHAR(255) PRIMARY KEY,
    value      TEXT NOT NULL,
    expiration INTEGER NOT NULL
);

CREATE TABLE IF NOT EXISTS cache_locks (
    key        VARCHAR(255) PRIMARY KEY,
    owner      VARCHAR(255) NOT NULL,
    expiration INTEGER NOT NULL
);

-- jobs
CREATE TABLE IF NOT EXISTS jobs (
    id           BIGSERIAL PRIMARY KEY,
    queue        VARCHAR(255) NOT NULL,
    payload      TEXT NOT NULL,
    attempts     SMALLINT NOT NULL DEFAULT 0,
    reserved_at  INTEGER,
    available_at INTEGER NOT NULL,
    created_at   INTEGER NOT NULL
);

CREATE TABLE IF NOT EXISTS failed_jobs (
    id         BIGSERIAL PRIMARY KEY,
    uuid       VARCHAR(255) UNIQUE NOT NULL,
    connection TEXT NOT NULL,
    queue      TEXT NOT NULL,
    payload    TEXT NOT NULL,
    exception  TEXT NOT NULL,
    failed_at  TIMESTAMP DEFAULT NOW()
);

CREATE TABLE IF NOT EXISTS job_batches (
    id             VARCHAR(255) PRIMARY KEY,
    name           VARCHAR(255) NOT NULL,
    total_jobs     INTEGER NOT NULL,
    pending_jobs   INTEGER NOT NULL,
    failed_jobs    INTEGER NOT NULL,
    failed_job_ids TEXT NOT NULL,
    options        TEXT,
    cancelled_at   INTEGER,
    created_at     INTEGER NOT NULL,
    finished_at    INTEGER
);

-- password_reset_tokens
CREATE TABLE IF NOT EXISTS password_reset_tokens (
    email      VARCHAR(255) PRIMARY KEY,
    token      VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT NOW()
);

-- migrations
CREATE TABLE IF NOT EXISTS migrations (
    id        SERIAL PRIMARY KEY,
    migration VARCHAR(255) NOT NULL,
    batch     INTEGER NOT NULL
);

-- event_categories
CREATE TABLE IF NOT EXISTS event_categories (
    id          BIGSERIAL PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    color       VARCHAR(50),
    description TEXT,
    created_at  TIMESTAMP DEFAULT NOW(),
    updated_at  TIMESTAMP DEFAULT NOW()
);

-- event_locations
CREATE TABLE IF NOT EXISTS event_locations (
    id          BIGSERIAL PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    description TEXT,
    created_at  TIMESTAMP DEFAULT NOW(),
    updated_at  TIMESTAMP DEFAULT NOW()
);

-- report_categories
CREATE TABLE IF NOT EXISTS report_categories (
    id                   BIGSERIAL PRIMARY KEY,
    category_name        VARCHAR(100) NOT NULL UNIQUE,
    description          TEXT,
    responsible_role_id  BIGINT REFERENCES roles(id),
    created_at           TIMESTAMP DEFAULT NOW(),
    updated_at           TIMESTAMP DEFAULT NOW()
);

-- announcements
CREATE TABLE IF NOT EXISTS announcements (
    id           BIGSERIAL PRIMARY KEY,
    title        VARCHAR(255) NOT NULL,
    content      TEXT NOT NULL,
    is_published SMALLINT DEFAULT 0,
    created_by   BIGINT REFERENCES users(id),
    created_at   TIMESTAMP DEFAULT NOW(),
    updated_at   TIMESTAMP DEFAULT NOW()
);

-- events
CREATE TABLE IF NOT EXISTS events (
    id             BIGSERIAL PRIMARY KEY,
    event_name     VARCHAR(255) NOT NULL,
    category_id    BIGINT REFERENCES event_categories(id),
    location_id    BIGINT REFERENCES event_locations(id),
    event_date     DATE NOT NULL,
    event_date_end DATE,
    description    TEXT,
    is_published   SMALLINT DEFAULT 0,
    created_by     BIGINT REFERENCES users(id),
    created_at     TIMESTAMP DEFAULT NOW(),
    updated_at     TIMESTAMP DEFAULT NOW()
);

-- anonymous_reports
CREATE TABLE IF NOT EXISTS anonymous_reports (
    id             BIGSERIAL PRIMARY KEY,
    ticket_number  VARCHAR(20) UNIQUE NOT NULL,
    category_id    BIGINT REFERENCES report_categories(id),
    report_content TEXT NOT NULL,
    admin_notes    TEXT,
    status         VARCHAR(20) DEFAULT 'pending',
    resolved_at    TIMESTAMP,
    created_at     TIMESTAMP DEFAULT NOW(),
    updated_at     TIMESTAMP DEFAULT NOW()
);

-- lost_founds
CREATE TABLE IF NOT EXISTS lost_founds (
    id            BIGSERIAL PRIMARY KEY,
    user_id       BIGINT REFERENCES users(id),
    type          VARCHAR(10) NOT NULL CHECK (type IN ('found','lost')),
    item_name     VARCHAR(100) NOT NULL,
    found_at      VARCHAR(150),
    description   TEXT,
    status        VARCHAR(20) DEFAULT 'pending',
    reject_reason TEXT,
    created_at    TIMESTAMP DEFAULT NOW(),
    updated_at    TIMESTAMP DEFAULT NOW()
);

-- photos (file_path = Supabase Storage path, file_data = public URL)
CREATE TABLE IF NOT EXISTS photos (
    id          BIGSERIAL PRIMARY KEY,
    source_type VARCHAR(50) NOT NULL,
    source_id   BIGINT NOT NULL,
    file_name   VARCHAR(255),
    file_path   VARCHAR(500),
    file_data   TEXT,
    file_type   VARCHAR(100),
    file_size   BIGINT,
    uploaded_by BIGINT REFERENCES users(id),
    created_at  TIMESTAMP DEFAULT NOW(),
    updated_at  TIMESTAMP DEFAULT NOW()
);

-- attachments (file_path = Supabase Storage path, link_url = public URL)
CREATE TABLE IF NOT EXISTS attachments (
    id              BIGSERIAL PRIMARY KEY,
    source_type     VARCHAR(50) NOT NULL,
    source_id       BIGINT NOT NULL,
    attachment_type VARCHAR(20) DEFAULT 'file',
    file_name       VARCHAR(255),
    file_path       VARCHAR(500),
    file_type       VARCHAR(100),
    file_size       BIGINT,
    link_url        TEXT,
    link_label      VARCHAR(255),
    label           VARCHAR(255),
    uploaded_by     BIGINT REFERENCES users(id),
    created_at      TIMESTAMP DEFAULT NOW(),
    updated_at      TIMESTAMP DEFAULT NOW()
);

-- notifications
CREATE TABLE IF NOT EXISTS notifications (
    id           BIGSERIAL PRIMARY KEY,
    user_id      BIGINT REFERENCES users(id) ON DELETE CASCADE,
    title        VARCHAR(255) NOT NULL,
    body         TEXT,
    type         VARCHAR(50),
    reference_id BIGINT,
    is_read      BOOLEAN DEFAULT FALSE,
    read_at      TIMESTAMP,
    created_at   TIMESTAMP DEFAULT NOW()
);

-- audit_logs
CREATE TABLE IF NOT EXISTS audit_logs (
    id         BIGSERIAL PRIMARY KEY,
    user_id    BIGINT REFERENCES users(id) ON DELETE SET NULL,
    action     VARCHAR(50) NOT NULL,
    table_name VARCHAR(100),
    record_id  BIGINT,
    old_values JSONB,
    new_values JSONB,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT NOW()
);

-- Indexes for performance
CREATE INDEX IF NOT EXISTS idx_photos_source      ON photos(source_type, source_id);
CREATE INDEX IF NOT EXISTS idx_attachments_source ON attachments(source_type, source_id);
CREATE INDEX IF NOT EXISTS idx_events_date        ON events(event_date);
CREATE INDEX IF NOT EXISTS idx_lost_founds_status ON lost_founds(status);
CREATE INDEX IF NOT EXISTS idx_anon_reports_ticket ON anonymous_reports(ticket_number);
CREATE INDEX IF NOT EXISTS idx_sessions_user_id   ON sessions(user_id);

-- Supabase Storage bucket setup (run in Supabase dashboard or via API):
-- Bucket name: sintem-files
-- Public: true (so public URLs work without auth)
-- Allowed MIME types: image/jpeg, image/png, image/gif, image/webp,
--                     application/pdf, application/msword,
--                     application/vnd.openxmlformats-officedocument.wordprocessingml.document,
--                     application/vnd.ms-excel,
--                     application/vnd.openxmlformats-officedocument.spreadsheetml.sheet

-- Default data: seed roles
INSERT INTO roles (role_name, description) VALUES
    ('Superadmin', 'Administrator sistem penuh'),
    ('Siswa', 'Pengguna siswa'),
    ('Admin', 'Administrator sekolah')
ON CONFLICT (role_name) DO NOTHING;
