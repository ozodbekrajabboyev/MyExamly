--
-- PostgreSQL database dump
--

\restrict Ax1rV0sKw1R7bYB7akqdfNdWEzwZfvkMatqxGYoFppUEuNc6eJjFk2x3zNbJkZD

-- Dumped from database version 14.19 (Homebrew)
-- Dumped by pg_dump version 14.19 (Homebrew)

DROP SCHEMA public CASCADE;
CREATE SCHEMA public;

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: cache; Type: TABLE; Schema: public; Owner: norbekhomidov
--

CREATE TABLE public.cache (
    key character varying(255) NOT NULL,
    value text NOT NULL,
    expiration integer NOT NULL
);


ALTER TABLE public.cache OWNER TO norbekhomidov;

--
-- Name: cache_locks; Type: TABLE; Schema: public; Owner: norbekhomidov
--

CREATE TABLE public.cache_locks (
    key character varying(255) NOT NULL,
    owner character varying(255) NOT NULL,
    expiration integer NOT NULL
);


ALTER TABLE public.cache_locks OWNER TO norbekhomidov;

--
-- Name: exams; Type: TABLE; Schema: public; Owner: norbekhomidov
--

CREATE TABLE public.exams (
    id bigint NOT NULL,
    maktab_id bigint NOT NULL,
    sinf_id bigint NOT NULL,
    subject_id bigint NOT NULL,
    teacher_id bigint NOT NULL,
    type text NOT NULL,
    serial_number integer NOT NULL,
    metod_id bigint NOT NULL,
    problems jsonb,
    status character varying(255) DEFAULT 'pending'::character varying NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.exams OWNER TO norbekhomidov;

--
-- Name: exams_id_seq; Type: SEQUENCE; Schema: public; Owner: norbekhomidov
--

CREATE SEQUENCE public.exams_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.exams_id_seq OWNER TO norbekhomidov;

--
-- Name: exams_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: norbekhomidov
--

ALTER SEQUENCE public.exams_id_seq OWNED BY public.exams.id;


--
-- Name: exports; Type: TABLE; Schema: public; Owner: norbekhomidov
--

CREATE TABLE public.exports (
    id bigint NOT NULL,
    completed_at timestamp(0) without time zone,
    file_disk character varying(255) NOT NULL,
    file_name character varying(255),
    exporter character varying(255) NOT NULL,
    processed_rows integer DEFAULT 0 NOT NULL,
    total_rows integer NOT NULL,
    successful_rows integer DEFAULT 0 NOT NULL,
    user_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.exports OWNER TO norbekhomidov;

--
-- Name: exports_id_seq; Type: SEQUENCE; Schema: public; Owner: norbekhomidov
--

CREATE SEQUENCE public.exports_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.exports_id_seq OWNER TO norbekhomidov;

--
-- Name: exports_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: norbekhomidov
--

ALTER SEQUENCE public.exports_id_seq OWNED BY public.exports.id;


--
-- Name: failed_import_rows; Type: TABLE; Schema: public; Owner: norbekhomidov
--

CREATE TABLE public.failed_import_rows (
    id bigint NOT NULL,
    data json NOT NULL,
    import_id bigint NOT NULL,
    validation_error text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.failed_import_rows OWNER TO norbekhomidov;

--
-- Name: failed_import_rows_id_seq; Type: SEQUENCE; Schema: public; Owner: norbekhomidov
--

CREATE SEQUENCE public.failed_import_rows_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.failed_import_rows_id_seq OWNER TO norbekhomidov;

--
-- Name: failed_import_rows_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: norbekhomidov
--

ALTER SEQUENCE public.failed_import_rows_id_seq OWNED BY public.failed_import_rows.id;


--
-- Name: failed_jobs; Type: TABLE; Schema: public; Owner: norbekhomidov
--

CREATE TABLE public.failed_jobs (
    id bigint NOT NULL,
    uuid character varying(255) NOT NULL,
    connection text NOT NULL,
    queue text NOT NULL,
    payload text NOT NULL,
    exception text NOT NULL,
    failed_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.failed_jobs OWNER TO norbekhomidov;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: norbekhomidov
--

CREATE SEQUENCE public.failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.failed_jobs_id_seq OWNER TO norbekhomidov;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: norbekhomidov
--

ALTER SEQUENCE public.failed_jobs_id_seq OWNED BY public.failed_jobs.id;


--
-- Name: imports; Type: TABLE; Schema: public; Owner: norbekhomidov
--

CREATE TABLE public.imports (
    id bigint NOT NULL,
    completed_at timestamp(0) without time zone,
    file_name character varying(255) NOT NULL,
    file_path character varying(255) NOT NULL,
    importer character varying(255) NOT NULL,
    processed_rows integer DEFAULT 0 NOT NULL,
    total_rows integer NOT NULL,
    successful_rows integer DEFAULT 0 NOT NULL,
    user_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.imports OWNER TO norbekhomidov;

--
-- Name: imports_id_seq; Type: SEQUENCE; Schema: public; Owner: norbekhomidov
--

CREATE SEQUENCE public.imports_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.imports_id_seq OWNER TO norbekhomidov;

--
-- Name: imports_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: norbekhomidov
--

ALTER SEQUENCE public.imports_id_seq OWNED BY public.imports.id;


--
-- Name: job_batches; Type: TABLE; Schema: public; Owner: norbekhomidov
--

CREATE TABLE public.job_batches (
    id character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    total_jobs integer NOT NULL,
    pending_jobs integer NOT NULL,
    failed_jobs integer NOT NULL,
    failed_job_ids text NOT NULL,
    options text,
    cancelled_at integer,
    created_at integer NOT NULL,
    finished_at integer
);


ALTER TABLE public.job_batches OWNER TO norbekhomidov;

--
-- Name: jobs; Type: TABLE; Schema: public; Owner: norbekhomidov
--

CREATE TABLE public.jobs (
    id bigint NOT NULL,
    queue character varying(255) NOT NULL,
    payload text NOT NULL,
    attempts smallint NOT NULL,
    reserved_at integer,
    available_at integer NOT NULL,
    created_at integer NOT NULL
);


ALTER TABLE public.jobs OWNER TO norbekhomidov;

--
-- Name: jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: norbekhomidov
--

CREATE SEQUENCE public.jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.jobs_id_seq OWNER TO norbekhomidov;

--
-- Name: jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: norbekhomidov
--

ALTER SEQUENCE public.jobs_id_seq OWNED BY public.jobs.id;


--
-- Name: maktabs; Type: TABLE; Schema: public; Owner: norbekhomidov
--

CREATE TABLE public.maktabs (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.maktabs OWNER TO norbekhomidov;

--
-- Name: maktabs_id_seq; Type: SEQUENCE; Schema: public; Owner: norbekhomidov
--

CREATE SEQUENCE public.maktabs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.maktabs_id_seq OWNER TO norbekhomidov;

--
-- Name: maktabs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: norbekhomidov
--

ALTER SEQUENCE public.maktabs_id_seq OWNED BY public.maktabs.id;


--
-- Name: marks; Type: TABLE; Schema: public; Owner: norbekhomidov
--

CREATE TABLE public.marks (
    id bigint NOT NULL,
    student_id bigint NOT NULL,
    exam_id bigint NOT NULL,
    sinf_id bigint NOT NULL,
    maktab_id bigint NOT NULL,
    problem_id integer NOT NULL,
    mark integer DEFAULT 0 NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.marks OWNER TO norbekhomidov;

--
-- Name: marks_id_seq; Type: SEQUENCE; Schema: public; Owner: norbekhomidov
--

CREATE SEQUENCE public.marks_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.marks_id_seq OWNER TO norbekhomidov;

--
-- Name: marks_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: norbekhomidov
--

ALTER SEQUENCE public.marks_id_seq OWNED BY public.marks.id;


--
-- Name: migrations; Type: TABLE; Schema: public; Owner: norbekhomidov
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


ALTER TABLE public.migrations OWNER TO norbekhomidov;

--
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: norbekhomidov
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.migrations_id_seq OWNER TO norbekhomidov;

--
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: norbekhomidov
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- Name: notifications; Type: TABLE; Schema: public; Owner: norbekhomidov
--

CREATE TABLE public.notifications (
    id uuid NOT NULL,
    type character varying(255) NOT NULL,
    notifiable_type character varying(255) NOT NULL,
    notifiable_id bigint NOT NULL,
    data jsonb NOT NULL,
    read_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.notifications OWNER TO norbekhomidov;

--
-- Name: password_reset_tokens; Type: TABLE; Schema: public; Owner: norbekhomidov
--

CREATE TABLE public.password_reset_tokens (
    email character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    created_at timestamp(0) without time zone
);


ALTER TABLE public.password_reset_tokens OWNER TO norbekhomidov;

--
-- Name: roles; Type: TABLE; Schema: public; Owner: norbekhomidov
--

CREATE TABLE public.roles (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.roles OWNER TO norbekhomidov;

--
-- Name: roles_id_seq; Type: SEQUENCE; Schema: public; Owner: norbekhomidov
--

CREATE SEQUENCE public.roles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.roles_id_seq OWNER TO norbekhomidov;

--
-- Name: roles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: norbekhomidov
--

ALTER SEQUENCE public.roles_id_seq OWNED BY public.roles.id;


--
-- Name: sessions; Type: TABLE; Schema: public; Owner: norbekhomidov
--

CREATE TABLE public.sessions (
    id character varying(255) NOT NULL,
    user_id bigint,
    ip_address character varying(45),
    user_agent text,
    payload text NOT NULL,
    last_activity integer NOT NULL
);


ALTER TABLE public.sessions OWNER TO norbekhomidov;

--
-- Name: sinfs; Type: TABLE; Schema: public; Owner: norbekhomidov
--

CREATE TABLE public.sinfs (
    id bigint NOT NULL,
    maktab_id bigint NOT NULL,
    name character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.sinfs OWNER TO norbekhomidov;

--
-- Name: sinfs_id_seq; Type: SEQUENCE; Schema: public; Owner: norbekhomidov
--

CREATE SEQUENCE public.sinfs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.sinfs_id_seq OWNER TO norbekhomidov;

--
-- Name: sinfs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: norbekhomidov
--

ALTER SEQUENCE public.sinfs_id_seq OWNED BY public.sinfs.id;


--
-- Name: students; Type: TABLE; Schema: public; Owner: norbekhomidov
--

CREATE TABLE public.students (
    id bigint NOT NULL,
    maktab_id bigint NOT NULL,
    sinf_id bigint NOT NULL,
    full_name character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.students OWNER TO norbekhomidov;

--
-- Name: students_id_seq; Type: SEQUENCE; Schema: public; Owner: norbekhomidov
--

CREATE SEQUENCE public.students_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.students_id_seq OWNER TO norbekhomidov;

--
-- Name: students_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: norbekhomidov
--

ALTER SEQUENCE public.students_id_seq OWNED BY public.students.id;


--
-- Name: subjects; Type: TABLE; Schema: public; Owner: norbekhomidov
--

CREATE TABLE public.subjects (
    id bigint NOT NULL,
    maktab_id bigint NOT NULL,
    name character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.subjects OWNER TO norbekhomidov;

--
-- Name: subjects_id_seq; Type: SEQUENCE; Schema: public; Owner: norbekhomidov
--

CREATE SEQUENCE public.subjects_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.subjects_id_seq OWNER TO norbekhomidov;

--
-- Name: subjects_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: norbekhomidov
--

ALTER SEQUENCE public.subjects_id_seq OWNED BY public.subjects.id;


--
-- Name: teacher_subject; Type: TABLE; Schema: public; Owner: norbekhomidov
--

CREATE TABLE public.teacher_subject (
    id bigint NOT NULL,
    teacher_id bigint NOT NULL,
    subject_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.teacher_subject OWNER TO norbekhomidov;

--
-- Name: teacher_subject_id_seq; Type: SEQUENCE; Schema: public; Owner: norbekhomidov
--

CREATE SEQUENCE public.teacher_subject_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.teacher_subject_id_seq OWNER TO norbekhomidov;

--
-- Name: teacher_subject_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: norbekhomidov
--

ALTER SEQUENCE public.teacher_subject_id_seq OWNED BY public.teacher_subject.id;


--
-- Name: teachers; Type: TABLE; Schema: public; Owner: norbekhomidov
--

CREATE TABLE public.teachers (
    id bigint NOT NULL,
    maktab_id bigint NOT NULL,
    full_name character varying(255) NOT NULL,
    user_id bigint NOT NULL,
    phone character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    passport_serial_number character varying(255),
    passport_jshshir character varying(255),
    passport_photo_path character varying(255),
    diplom_path character varying(255),
    malaka_toifa_path character varying(255),
    milliy_sertifikat_path character varying(255),
    xalqaro_sertifikat_path character varying(255),
    malumotnoma_path character varying(255),
    signature_path character varying(255),
    telegram_id character varying(255)
);


ALTER TABLE public.teachers OWNER TO norbekhomidov;

--
-- Name: teachers_id_seq; Type: SEQUENCE; Schema: public; Owner: norbekhomidov
--

CREATE SEQUENCE public.teachers_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.teachers_id_seq OWNER TO norbekhomidov;

--
-- Name: teachers_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: norbekhomidov
--

ALTER SEQUENCE public.teachers_id_seq OWNED BY public.teachers.id;


--
-- Name: users; Type: TABLE; Schema: public; Owner: norbekhomidov
--

CREATE TABLE public.users (
    id bigint NOT NULL,
    maktab_id bigint DEFAULT '1'::bigint NOT NULL,
    name character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    role_id bigint DEFAULT '1'::bigint NOT NULL,
    email_verified_at timestamp(0) without time zone,
    password character varying(255) NOT NULL,
    remember_token character varying(100),
    signature_path character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.users OWNER TO norbekhomidov;

--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: norbekhomidov
--

CREATE SEQUENCE public.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.users_id_seq OWNER TO norbekhomidov;

--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: norbekhomidov
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- Name: exams id; Type: DEFAULT; Schema: public; Owner: norbekhomidov
--

ALTER TABLE ONLY public.exams ALTER COLUMN id SET DEFAULT nextval('public.exams_id_seq'::regclass);


--
-- Name: exports id; Type: DEFAULT; Schema: public; Owner: norbekhomidov
--

ALTER TABLE ONLY public.exports ALTER COLUMN id SET DEFAULT nextval('public.exports_id_seq'::regclass);


--
-- Name: failed_import_rows id; Type: DEFAULT; Schema: public; Owner: norbekhomidov
--

ALTER TABLE ONLY public.failed_import_rows ALTER COLUMN id SET DEFAULT nextval('public.failed_import_rows_id_seq'::regclass);


--
-- Name: failed_jobs id; Type: DEFAULT; Schema: public; Owner: norbekhomidov
--

ALTER TABLE ONLY public.failed_jobs ALTER COLUMN id SET DEFAULT nextval('public.failed_jobs_id_seq'::regclass);


--
-- Name: imports id; Type: DEFAULT; Schema: public; Owner: norbekhomidov
--

ALTER TABLE ONLY public.imports ALTER COLUMN id SET DEFAULT nextval('public.imports_id_seq'::regclass);


--
-- Name: jobs id; Type: DEFAULT; Schema: public; Owner: norbekhomidov
--

ALTER TABLE ONLY public.jobs ALTER COLUMN id SET DEFAULT nextval('public.jobs_id_seq'::regclass);


--
-- Name: maktabs id; Type: DEFAULT; Schema: public; Owner: norbekhomidov
--

ALTER TABLE ONLY public.maktabs ALTER COLUMN id SET DEFAULT nextval('public.maktabs_id_seq'::regclass);


--
-- Name: marks id; Type: DEFAULT; Schema: public; Owner: norbekhomidov
--

ALTER TABLE ONLY public.marks ALTER COLUMN id SET DEFAULT nextval('public.marks_id_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: norbekhomidov
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- Name: roles id; Type: DEFAULT; Schema: public; Owner: norbekhomidov
--

ALTER TABLE ONLY public.roles ALTER COLUMN id SET DEFAULT nextval('public.roles_id_seq'::regclass);


--
-- Name: sinfs id; Type: DEFAULT; Schema: public; Owner: norbekhomidov
--

ALTER TABLE ONLY public.sinfs ALTER COLUMN id SET DEFAULT nextval('public.sinfs_id_seq'::regclass);


--
-- Name: students id; Type: DEFAULT; Schema: public; Owner: norbekhomidov
--

ALTER TABLE ONLY public.students ALTER COLUMN id SET DEFAULT nextval('public.students_id_seq'::regclass);


--
-- Name: subjects id; Type: DEFAULT; Schema: public; Owner: norbekhomidov
--

ALTER TABLE ONLY public.subjects ALTER COLUMN id SET DEFAULT nextval('public.subjects_id_seq'::regclass);


--
-- Name: teacher_subject id; Type: DEFAULT; Schema: public; Owner: norbekhomidov
--

ALTER TABLE ONLY public.teacher_subject ALTER COLUMN id SET DEFAULT nextval('public.teacher_subject_id_seq'::regclass);


--
-- Name: teachers id; Type: DEFAULT; Schema: public; Owner: norbekhomidov
--

ALTER TABLE ONLY public.teachers ALTER COLUMN id SET DEFAULT nextval('public.teachers_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: norbekhomidov
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- Data for Name: cache; Type: TABLE DATA; Schema: public; Owner: norbekhomidov
--

COPY public.cache (key, value, expiration) FROM stdin;
laravel_cache_boost.roster.scan	YToyOntzOjY6InJvc3RlciI7TzoyMToiTGFyYXZlbFxSb3N0ZXJcUm9zdGVyIjoyOntzOjEzOiIAKgBhcHByb2FjaGVzIjtPOjI5OiJJbGx1bWluYXRlXFN1cHBvcnRcQ29sbGVjdGlvbiI6Mjp7czo4OiIAKgBpdGVtcyI7YTowOnt9czoyODoiACoAZXNjYXBlV2hlbkNhc3RpbmdUb1N0cmluZyI7YjowO31zOjExOiIAKgBwYWNrYWdlcyI7TzozMjoiTGFyYXZlbFxSb3N0ZXJcUGFja2FnZUNvbGxlY3Rpb24iOjI6e3M6ODoiACoAaXRlbXMiO2E6Njp7aTowO086MjI6IkxhcmF2ZWxcUm9zdGVyXFBhY2thZ2UiOjQ6e3M6MTA6IgAqAHBhY2thZ2UiO0U6Mzg6IkxhcmF2ZWxcUm9zdGVyXEVudW1zXFBhY2thZ2VzOkZJTEFNRU5UIjtzOjE0OiIAKgBwYWNrYWdlTmFtZSI7czoxNzoiZmlsYW1lbnQvZmlsYW1lbnQiO3M6MTA6IgAqAHZlcnNpb24iO3M6NjoiMy4zLjM1IjtzOjY6IgAqAGRldiI7YjowO31pOjE7TzoyMjoiTGFyYXZlbFxSb3N0ZXJcUGFja2FnZSI6NDp7czoxMDoiACoAcGFja2FnZSI7RTozNzoiTGFyYXZlbFxSb3N0ZXJcRW51bXNcUGFja2FnZXM6TEFSQVZFTCI7czoxNDoiACoAcGFja2FnZU5hbWUiO3M6MTc6ImxhcmF2ZWwvZnJhbWV3b3JrIjtzOjEwOiIAKgB2ZXJzaW9uIjtzOjc6IjEyLjIyLjEiO3M6NjoiACoAZGV2IjtiOjA7fWk6MjtPOjIyOiJMYXJhdmVsXFJvc3RlclxQYWNrYWdlIjo0OntzOjEwOiIAKgBwYWNrYWdlIjtFOjM3OiJMYXJhdmVsXFJvc3RlclxFbnVtc1xQYWNrYWdlczpQUk9NUFRTIjtzOjE0OiIAKgBwYWNrYWdlTmFtZSI7czoxNToibGFyYXZlbC9wcm9tcHRzIjtzOjEwOiIAKgB2ZXJzaW9uIjtzOjU6IjAuMy42IjtzOjY6IgAqAGRldiI7YjowO31pOjM7TzoyMjoiTGFyYXZlbFxSb3N0ZXJcUGFja2FnZSI6NDp7czoxMDoiACoAcGFja2FnZSI7RTozODoiTGFyYXZlbFxSb3N0ZXJcRW51bXNcUGFja2FnZXM6TElWRVdJUkUiO3M6MTQ6IgAqAHBhY2thZ2VOYW1lIjtzOjE3OiJsaXZld2lyZS9saXZld2lyZSI7czoxMDoiACoAdmVyc2lvbiI7czo1OiIzLjYuNCI7czo2OiIAKgBkZXYiO2I6MDt9aTo0O086MjI6IkxhcmF2ZWxcUm9zdGVyXFBhY2thZ2UiOjQ6e3M6MTA6IgAqAHBhY2thZ2UiO0U6MzQ6IkxhcmF2ZWxcUm9zdGVyXEVudW1zXFBhY2thZ2VzOlBJTlQiO3M6MTQ6IgAqAHBhY2thZ2VOYW1lIjtzOjEyOiJsYXJhdmVsL3BpbnQiO3M6MTA6IgAqAHZlcnNpb24iO3M6NjoiMS4yNC4wIjtzOjY6IgAqAGRldiI7YjoxO31pOjU7TzoyMjoiTGFyYXZlbFxSb3N0ZXJcUGFja2FnZSI6NDp7czoxMDoiACoAcGFja2FnZSI7RTo0MToiTGFyYXZlbFxSb3N0ZXJcRW51bXNcUGFja2FnZXM6VEFJTFdJTkRDU1MiO3M6MTQ6IgAqAHBhY2thZ2VOYW1lIjtzOjExOiJ0YWlsd2luZGNzcyI7czoxMDoiACoAdmVyc2lvbiI7czo1OiI0LjEuNCI7czo2OiIAKgBkZXYiO2I6MTt9fXM6Mjg6IgAqAGVzY2FwZVdoZW5DYXN0aW5nVG9TdHJpbmciO2I6MDt9fXM6OToidGltZXN0YW1wIjtpOjE3NTU4NDM2NjU7fQ==	1755930065
laravel_cache_1b6453892473a467d07372d45eb05abc2031647a:timer	i:1755845551;	1755845551
laravel_cache_1b6453892473a467d07372d45eb05abc2031647a	i:6;	1755845551
laravel_cache_livewire-rate-limiter:a17961fa74e9275d529f489537f179c05d50c2f3:timer	i:1755845927;	1755845927
laravel_cache_livewire-rate-limiter:a17961fa74e9275d529f489537f179c05d50c2f3	i:2;	1755845927
\.


--
-- Data for Name: cache_locks; Type: TABLE DATA; Schema: public; Owner: norbekhomidov
--

COPY public.cache_locks (key, owner, expiration) FROM stdin;
\.


--
-- Data for Name: exams; Type: TABLE DATA; Schema: public; Owner: norbekhomidov
--

COPY public.exams (id, maktab_id, sinf_id, subject_id, teacher_id, type, serial_number, metod_id, problems, status, created_at, updated_at) FROM stdin;
2	2	1	1	1	CHSB	2	1	[{"id": 1, "max_mark": "10"}, {"id": 2, "max_mark": "10"}, {"id": 3, "max_mark": "10"}, {"id": 4, "max_mark": "10"}, {"id": 5, "max_mark": "10"}, {"id": 6, "max_mark": "10"}, {"id": 7, "max_mark": "10"}, {"id": 8, "max_mark": "10"}, {"id": 9, "max_mark": "10"}, {"id": 10, "max_mark": "10"}, {"id": 11, "max_mark": "10"}, {"id": 12, "max_mark": "10"}, {"id": 13, "max_mark": "10"}, {"id": 14, "max_mark": "10"}, {"id": 15, "max_mark": "10"}, {"id": 16, "max_mark": "10"}, {"id": 17, "max_mark": "10"}, {"id": 18, "max_mark": "10"}, {"id": 19, "max_mark": "10"}, {"id": 20, "max_mark": "10"}]	pending	2025-08-22 02:20:11	2025-08-22 02:20:11
1	2	1	1	1	BSB	1	1	[{"id": 1, "max_mark": "10"}, {"id": 2, "max_mark": "10"}]	approved	2025-08-22 02:00:47	2025-08-22 02:44:03
3	2	2	2	2	BSB	1	2	[{"id": 1, "max_mark": "15"}, {"id": 2, "max_mark": "10"}]	approved	2025-08-22 02:37:17	2025-08-22 02:44:29
4	2	1	2	2	BSB	2	2	[{"id": 1, "max_mark": "10"}, {"id": 2, "max_mark": "10"}, {"id": 3, "max_mark": "10"}, {"id": null, "max_mark": "10"}]	pending	2025-08-22 11:10:00	2025-08-22 11:10:00
5	2	2	2	2	BSB	4	1	[{"id": 1, "max_mark": "10"}, {"id": 2, "max_mark": "20"}, {"id": 3, "max_mark": "20"}, {"id": 4, "max_mark": "10"}]	pending	2025-08-22 11:49:47	2025-08-22 11:49:47
\.


--
-- Data for Name: exports; Type: TABLE DATA; Schema: public; Owner: norbekhomidov
--

COPY public.exports (id, completed_at, file_disk, file_name, exporter, processed_rows, total_rows, successful_rows, user_id, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: failed_import_rows; Type: TABLE DATA; Schema: public; Owner: norbekhomidov
--

COPY public.failed_import_rows (id, data, import_id, validation_error, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: failed_jobs; Type: TABLE DATA; Schema: public; Owner: norbekhomidov
--

COPY public.failed_jobs (id, uuid, connection, queue, payload, exception, failed_at) FROM stdin;
\.


--
-- Data for Name: imports; Type: TABLE DATA; Schema: public; Owner: norbekhomidov
--

COPY public.imports (id, completed_at, file_name, file_path, importer, processed_rows, total_rows, successful_rows, user_id, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: job_batches; Type: TABLE DATA; Schema: public; Owner: norbekhomidov
--

COPY public.job_batches (id, name, total_jobs, pending_jobs, failed_jobs, failed_job_ids, options, cancelled_at, created_at, finished_at) FROM stdin;
\.


--
-- Data for Name: jobs; Type: TABLE DATA; Schema: public; Owner: norbekhomidov
--

COPY public.jobs (id, queue, payload, attempts, reserved_at, available_at, created_at) FROM stdin;
\.


--
-- Data for Name: maktabs; Type: TABLE DATA; Schema: public; Owner: norbekhomidov
--

COPY public.maktabs (id, name, created_at, updated_at) FROM stdin;
1	MyExamly Platform	2025-08-22 01:57:55	2025-08-22 01:57:55
2	Al-Xorazmiy nomidagi ixtisoslashtirilgan maktabi Buxoro filiali	2025-08-22 02:26:45	2025-08-22 02:26:45
\.


--
-- Data for Name: marks; Type: TABLE DATA; Schema: public; Owner: norbekhomidov
--

COPY public.marks (id, student_id, exam_id, sinf_id, maktab_id, problem_id, mark, created_at, updated_at) FROM stdin;
163	10	3	2	2	1	2.00	2025-08-22 02:45:18	2025-08-22 02:45:18
164	10	3	2	2	2	3.00	2025-08-22 02:45:18	2025-08-22 02:45:18
165	9	3	2	2	1	2.00	2025-08-22 02:45:18	2025-08-22 02:45:18
166	9	3	2	2	2	9.00	2025-08-22 02:45:18	2025-08-22 02:45:18
35	6	2	1	2	13	7.00	2025-08-22 02:21:40	2025-08-22 02:21:40
149	4	2	1	2	7	9.00	2025-08-22 02:21:40	2025-08-22 02:21:40
25	6	2	1	2	3	5.00	2025-08-22 02:21:40	2025-08-22 02:21:40
124	1	2	1	2	2	8.00	2025-08-22 02:21:40	2025-08-22 02:21:40
46	8	2	1	2	4	5.00	2025-08-22 02:21:40	2025-08-22 02:21:40
79	2	2	1	2	17	5.00	2025-08-22 02:21:40	2025-08-22 02:21:40
105	5	2	1	2	3	7.00	2025-08-22 02:21:40	2025-08-22 02:21:40
62	8	2	1	2	20	6.00	2025-08-22 02:21:40	2025-08-22 02:21:40
63	2	2	1	2	1	6.00	2025-08-22 02:21:40	2025-08-22 02:21:40
24	6	2	1	2	2	6.00	2025-08-22 02:21:40	2025-08-22 02:21:40
154	4	2	1	2	12	9.00	2025-08-22 02:21:40	2025-08-22 02:21:40
133	1	2	1	2	11	3.00	2025-08-22 02:21:40	2025-08-22 02:21:40
78	2	2	1	2	16	5.00	2025-08-22 02:21:40	2025-08-22 02:21:40
111	5	2	1	2	9	8.00	2025-08-22 02:21:40	2025-08-22 02:21:40
109	5	2	1	2	7	8.00	2025-08-22 02:21:40	2025-08-22 02:21:40
9	3	2	1	2	7	4.00	2025-08-22 02:21:39	2025-08-22 02:21:39
108	5	2	1	2	6	8.00	2025-08-22 02:21:40	2025-08-22 02:21:40
138	1	2	1	2	16	9.00	2025-08-22 02:21:40	2025-08-22 02:21:40
136	1	2	1	2	14	3.00	2025-08-22 02:21:40	2025-08-22 02:21:40
30	6	2	1	2	8	7.00	2025-08-22 02:21:40	2025-08-22 02:21:40
139	1	2	1	2	17	9.00	2025-08-22 02:21:40	2025-08-22 02:21:40
31	6	2	1	2	9	8.00	2025-08-22 02:21:40	2025-08-22 02:21:40
126	1	2	1	2	4	8.00	2025-08-22 02:21:40	2025-08-22 02:21:40
33	6	2	1	2	11	0.00	2025-08-22 02:21:40	2025-08-22 02:21:40
56	8	2	1	2	14	5.00	2025-08-22 02:21:40	2025-08-22 02:21:40
155	4	2	1	2	13	9.00	2025-08-22 02:21:40	2025-08-22 02:21:40
44	8	2	1	2	2	7.00	2025-08-22 02:21:40	2025-08-22 02:21:40
66	2	2	1	2	4	6.00	2025-08-22 02:21:40	2025-08-22 02:21:40
151	4	2	1	2	9	9.00	2025-08-22 02:21:40	2025-08-22 02:21:40
107	5	2	1	2	5	8.00	2025-08-22 02:21:40	2025-08-22 02:21:40
148	4	2	1	2	6	9.00	2025-08-22 02:21:40	2025-08-22 02:21:40
104	5	2	1	2	2	7.00	2025-08-22 02:21:40	2025-08-22 02:21:40
127	1	2	1	2	5	5.00	2025-08-22 02:21:40	2025-08-22 02:21:40
60	8	2	1	2	18	6.00	2025-08-22 02:21:40	2025-08-22 02:21:40
38	6	2	1	2	16	4.00	2025-08-22 02:21:40	2025-08-22 02:21:40
34	6	2	1	2	12	8.00	2025-08-22 02:21:40	2025-08-22 02:21:40
59	8	2	1	2	17	5.00	2025-08-22 02:21:40	2025-08-22 02:21:40
57	8	2	1	2	15	4.00	2025-08-22 02:21:40	2025-08-22 02:21:40
125	1	2	1	2	3	8.00	2025-08-22 02:21:40	2025-08-22 02:21:40
32	6	2	1	2	10	9.00	2025-08-22 02:21:40	2025-08-22 02:21:40
45	8	2	1	2	3	6.00	2025-08-22 02:21:40	2025-08-22 02:21:40
47	8	2	1	2	5	4.00	2025-08-22 02:21:40	2025-08-22 02:21:40
67	2	2	1	2	5	6.00	2025-08-22 02:21:40	2025-08-22 02:21:40
73	2	2	1	2	11	8.00	2025-08-22 02:21:40	2025-08-22 02:21:40
91	7	2	1	2	9	6.00	2025-08-22 02:21:40	2025-08-22 02:21:40
145	4	2	1	2	3	9.00	2025-08-22 02:21:40	2025-08-22 02:21:40
153	4	2	1	2	11	9.00	2025-08-22 02:21:40	2025-08-22 02:21:40
106	5	2	1	2	4	8.00	2025-08-22 02:21:40	2025-08-22 02:21:40
83	7	2	1	2	1	5.00	2025-08-22 02:21:40	2025-08-22 02:21:40
39	6	2	1	2	17	6.00	2025-08-22 02:21:40	2025-08-22 02:21:40
121	5	2	1	2	19	8.00	2025-08-22 02:21:40	2025-08-22 02:21:40
20	3	2	1	2	18	9.00	2025-08-22 02:21:40	2025-08-22 02:21:40
123	1	2	1	2	1	8.00	2025-08-22 02:21:40	2025-08-22 02:21:40
61	8	2	1	2	19	6.00	2025-08-22 02:21:40	2025-08-22 02:21:40
3	3	2	1	2	1	3.00	2025-08-22 02:21:39	2025-08-22 02:21:39
159	4	2	1	2	17	9.00	2025-08-22 02:21:40	2025-08-22 02:21:40
137	1	2	1	2	15	3.00	2025-08-22 02:21:40	2025-08-22 02:21:40
26	6	2	1	2	4	4.00	2025-08-22 02:21:40	2025-08-22 02:21:40
11	3	2	1	2	9	7.00	2025-08-22 02:21:39	2025-08-22 02:21:39
135	1	2	1	2	13	3.00	2025-08-22 02:21:40	2025-08-22 02:21:40
113	5	2	1	2	11	8.00	2025-08-22 02:21:40	2025-08-22 02:21:40
98	7	2	1	2	16	7.00	2025-08-22 02:21:40	2025-08-22 02:21:40
28	6	2	1	2	6	5.00	2025-08-22 02:21:40	2025-08-22 02:21:40
115	5	2	1	2	13	8.00	2025-08-22 02:21:40	2025-08-22 02:21:40
5	3	2	1	2	3	4.00	2025-08-22 02:21:39	2025-08-22 02:21:39
89	7	2	1	2	7	6.00	2025-08-22 02:21:40	2025-08-22 02:21:40
93	7	2	1	2	11	6.00	2025-08-22 02:21:40	2025-08-22 02:21:40
161	4	2	1	2	19	9.00	2025-08-22 02:21:40	2025-08-22 02:21:40
85	7	2	1	2	3	5.00	2025-08-22 02:21:40	2025-08-22 02:21:40
129	1	2	1	2	7	5.00	2025-08-22 02:21:40	2025-08-22 02:21:40
77	2	2	1	2	15	6.00	2025-08-22 02:21:40	2025-08-22 02:21:40
81	2	2	1	2	19	5.00	2025-08-22 02:21:40	2025-08-22 02:21:40
18	3	2	1	2	16	9.00	2025-08-22 02:21:40	2025-08-22 02:21:40
12	3	2	1	2	10	6.00	2025-08-22 02:21:39	2025-08-22 02:21:39
14	3	2	1	2	12	5.00	2025-08-22 02:21:40	2025-08-22 02:21:40
95	7	2	1	2	13	6.00	2025-08-22 02:21:40	2025-08-22 02:21:40
141	1	2	1	2	19	9.00	2025-08-22 02:21:40	2025-08-22 02:21:40
87	7	2	1	2	5	5.00	2025-08-22 02:21:40	2025-08-22 02:21:40
54	8	2	1	2	12	7.00	2025-08-22 02:21:40	2025-08-22 02:21:40
43	8	2	1	2	1	8.00	2025-08-22 02:21:40	2025-08-22 02:21:40
97	7	2	1	2	15	6.00	2025-08-22 02:21:40	2025-08-22 02:21:40
116	5	2	1	2	14	8.00	2025-08-22 02:21:40	2025-08-22 02:21:40
130	1	2	1	2	8	5.00	2025-08-22 02:21:40	2025-08-22 02:21:40
102	7	2	1	2	20	7.00	2025-08-22 02:21:40	2025-08-22 02:21:40
131	1	2	1	2	9	3.00	2025-08-22 02:21:40	2025-08-22 02:21:40
16	3	2	1	2	14	7.00	2025-08-22 02:21:40	2025-08-22 02:21:40
17	3	2	1	2	15	8.00	2025-08-22 02:21:40	2025-08-22 02:21:40
156	4	2	1	2	14	9.00	2025-08-22 02:21:40	2025-08-22 02:21:40
22	3	2	1	2	20	9.00	2025-08-22 02:21:40	2025-08-22 02:21:40
119	5	2	1	2	17	8.00	2025-08-22 02:21:40	2025-08-22 02:21:40
117	5	2	1	2	15	8.00	2025-08-22 02:21:40	2025-08-22 02:21:40
42	6	2	1	2	20	9.00	2025-08-22 02:21:40	2025-08-22 02:21:40
1	1	1	1	2	1	8.00	2025-08-22 02:01:13	2025-08-22 02:01:13
96	7	2	1	2	14	6.00	2025-08-22 02:21:40	2025-08-22 02:21:40
40	6	2	1	2	18	7.00	2025-08-22 02:21:40	2025-08-22 02:21:40
6	3	2	1	2	4	4.00	2025-08-22 02:21:39	2025-08-22 02:21:39
4	3	2	1	2	2	5.00	2025-08-22 02:21:39	2025-08-22 02:21:39
7	3	2	1	2	5	4.00	2025-08-22 02:21:39	2025-08-22 02:21:39
157	4	2	1	2	15	9.00	2025-08-22 02:21:40	2025-08-22 02:21:40
147	4	2	1	2	5	9.00	2025-08-22 02:21:40	2025-08-22 02:21:40
143	4	2	1	2	1	9.00	2025-08-22 02:21:40	2025-08-22 02:21:40
122	5	2	1	2	20	8.00	2025-08-22 02:21:40	2025-08-22 02:21:40
52	8	2	1	2	10	9.00	2025-08-22 02:21:40	2025-08-22 02:21:40
118	5	2	1	2	16	8.00	2025-08-22 02:21:40	2025-08-22 02:21:40
37	6	2	1	2	15	5.00	2025-08-22 02:21:40	2025-08-22 02:21:40
99	7	2	1	2	17	7.00	2025-08-22 02:21:40	2025-08-22 02:21:40
41	6	2	1	2	19	8.00	2025-08-22 02:21:40	2025-08-22 02:21:40
100	7	2	1	2	18	7.00	2025-08-22 02:21:40	2025-08-22 02:21:40
103	5	2	1	2	1	7.00	2025-08-22 02:21:40	2025-08-22 02:21:40
146	4	2	1	2	4	9.00	2025-08-22 02:21:40	2025-08-22 02:21:40
144	4	2	1	2	2	9.00	2025-08-22 02:21:40	2025-08-22 02:21:40
142	1	2	1	2	20	9.00	2025-08-22 02:21:40	2025-08-22 02:21:40
53	8	2	1	2	11	8.00	2025-08-22 02:21:40	2025-08-22 02:21:40
140	1	2	1	2	18	9.00	2025-08-22 02:21:40	2025-08-22 02:21:40
21	3	2	1	2	19	9.00	2025-08-22 02:21:40	2025-08-22 02:21:40
55	8	2	1	2	13	6.00	2025-08-22 02:21:40	2025-08-22 02:21:40
65	2	2	1	2	3	6.00	2025-08-22 02:21:40	2025-08-22 02:21:40
48	8	2	1	2	6	5.00	2025-08-22 02:21:40	2025-08-22 02:21:40
49	8	2	1	2	7	6.00	2025-08-22 02:21:40	2025-08-22 02:21:40
51	8	2	1	2	9	8.00	2025-08-22 02:21:40	2025-08-22 02:21:40
50	8	2	1	2	8	7.00	2025-08-22 02:21:40	2025-08-22 02:21:40
72	2	2	1	2	10	7.00	2025-08-22 02:21:40	2025-08-22 02:21:40
74	2	2	1	2	12	8.00	2025-08-22 02:21:40	2025-08-22 02:21:40
75	2	2	1	2	13	6.00	2025-08-22 02:21:40	2025-08-22 02:21:40
70	2	2	1	2	8	7.00	2025-08-22 02:21:40	2025-08-22 02:21:40
71	2	2	1	2	9	7.00	2025-08-22 02:21:40	2025-08-22 02:21:40
69	2	2	1	2	7	7.00	2025-08-22 02:21:40	2025-08-22 02:21:40
68	2	2	1	2	6	6.00	2025-08-22 02:21:40	2025-08-22 02:21:40
58	8	2	1	2	16	3.00	2025-08-22 02:21:40	2025-08-22 02:21:40
120	5	2	1	2	18	8.00	2025-08-22 02:21:40	2025-08-22 02:21:40
13	3	2	1	2	11	5.00	2025-08-22 02:21:40	2025-08-22 02:21:40
2	1	1	1	2	2	6.00	2025-08-22 02:01:13	2025-08-22 02:01:13
64	2	2	1	2	2	6.00	2025-08-22 02:21:40	2025-08-22 02:21:40
101	7	2	1	2	19	7.00	2025-08-22 02:21:40	2025-08-22 02:21:40
36	6	2	1	2	14	6.00	2025-08-22 02:21:40	2025-08-22 02:21:40
82	2	2	1	2	20	5.00	2025-08-22 02:21:40	2025-08-22 02:21:40
150	4	2	1	2	8	9.00	2025-08-22 02:21:40	2025-08-22 02:21:40
158	4	2	1	2	16	9.00	2025-08-22 02:21:40	2025-08-22 02:21:40
152	4	2	1	2	10	9.00	2025-08-22 02:21:40	2025-08-22 02:21:40
134	1	2	1	2	12	3.00	2025-08-22 02:21:40	2025-08-22 02:21:40
10	3	2	1	2	8	6.00	2025-08-22 02:21:39	2025-08-22 02:21:39
132	1	2	1	2	10	3.00	2025-08-22 02:21:40	2025-08-22 02:21:40
112	5	2	1	2	10	8.00	2025-08-22 02:21:40	2025-08-22 02:21:40
8	3	2	1	2	6	4.00	2025-08-22 02:21:39	2025-08-22 02:21:39
29	6	2	1	2	7	6.00	2025-08-22 02:21:40	2025-08-22 02:21:40
114	5	2	1	2	12	8.00	2025-08-22 02:21:40	2025-08-22 02:21:40
27	6	2	1	2	5	3.00	2025-08-22 02:21:40	2025-08-22 02:21:40
23	6	2	1	2	1	7.00	2025-08-22 02:21:40	2025-08-22 02:21:40
15	3	2	1	2	13	4.00	2025-08-22 02:21:40	2025-08-22 02:21:40
110	5	2	1	2	8	8.00	2025-08-22 02:21:40	2025-08-22 02:21:40
128	1	2	1	2	6	5.00	2025-08-22 02:21:40	2025-08-22 02:21:40
76	2	2	1	2	14	6.00	2025-08-22 02:21:40	2025-08-22 02:21:40
80	2	2	1	2	18	5.00	2025-08-22 02:21:40	2025-08-22 02:21:40
19	3	2	1	2	17	9.00	2025-08-22 02:21:40	2025-08-22 02:21:40
88	7	2	1	2	6	5.00	2025-08-22 02:21:40	2025-08-22 02:21:40
90	7	2	1	2	8	6.00	2025-08-22 02:21:40	2025-08-22 02:21:40
92	7	2	1	2	10	6.00	2025-08-22 02:21:40	2025-08-22 02:21:40
86	7	2	1	2	4	5.00	2025-08-22 02:21:40	2025-08-22 02:21:40
160	4	2	1	2	18	9.00	2025-08-22 02:21:40	2025-08-22 02:21:40
162	4	2	1	2	20	9.00	2025-08-22 02:21:40	2025-08-22 02:21:40
84	7	2	1	2	2	5.00	2025-08-22 02:21:40	2025-08-22 02:21:40
94	7	2	1	2	12	6.00	2025-08-22 02:21:40	2025-08-22 02:21:40
\.


--
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: norbekhomidov
--

COPY public.migrations (id, migration, batch) FROM stdin;
1	0000_00_00_000000_roles	1
2	0001_01_00_000000_create_maktabs_table	1
3	0001_01_01_000000_create_users_table	1
4	0001_01_01_000001_create_cache_table	1
5	0001_01_01_000002_create_jobs_table	1
6	2025_03_01_181838_create_sinfs_table	1
7	2025_03_01_181903_create_students_table	1
8	2025_03_01_181920_create_subjects_table	1
9	2025_03_01_181931_create_teachers_table	1
10	2025_03_07_143114_teacher_subject	1
11	2025_03_17_131658_create_exams_table	1
12	2025_03_17_131724_create_marks_table	1
13	2025_05_20_160116_create_notifications_table	1
14	2025_05_20_160147_create_imports_table	1
15	2025_05_20_160148_create_exports_table	1
16	2025_05_20_160149_create_failed_import_rows_table	1
17	2025_08_20_095010_add_profile_fields_to_teachers_table	1
\.


--
-- Data for Name: notifications; Type: TABLE DATA; Schema: public; Owner: norbekhomidov
--

COPY public.notifications (id, type, notifiable_type, notifiable_id, data, read_at, created_at, updated_at) FROM stdin;
11ffc304-0240-4343-93b3-9291c8fb18b7	Filament\\Notifications\\DatabaseNotification	App\\Models\\User	1	{"body": "Email: yolqinmuqimov@gmail.com \\n Password: 8@K@jnl8K!pP", "icon": null, "view": "filament-notifications::notification", "color": "green", "title": "Your credentials are ready!", "format": "filament", "status": null, "actions": [], "duration": "persistent", "viewData": [], "iconColor": null}	\N	2025-08-22 02:00:09	2025-08-22 02:00:09
b0704fcd-6dc7-445c-b216-dfeab75b3c3d	Filament\\Notifications\\DatabaseNotification	App\\Models\\User	3	{"body": "1-sinf | English | 1-BSB imtihonini tasdiqlash uchun so‘rov yuborildi.", "icon": "heroicon-o-document-check", "view": "filament-notifications::notification", "color": null, "title": "Imtihonni tasdiqlash so‘rovi", "format": "filament", "status": null, "actions": [], "duration": "persistent", "viewData": [], "iconColor": "warning"}	\N	2025-08-22 02:27:40	2025-08-22 02:27:40
fdc13e0e-4986-42d6-be01-ebee5a2737e8	Filament\\Notifications\\DatabaseNotification	App\\Models\\User	3	{"body": "Email: quvonchabdusobirov@gmail.com \\n Password: r-6vzCGD0j53", "icon": null, "view": "filament-notifications::notification", "color": "green", "title": "Your credentials are ready!", "format": "filament", "status": null, "actions": [], "duration": "persistent", "viewData": [], "iconColor": null}	\N	2025-08-22 02:35:47	2025-08-22 02:35:47
1fc4dc05-68a5-463a-a5fe-820e50d9da67	Filament\\Notifications\\DatabaseNotification	App\\Models\\User	2	{"body": "1-sinf | English | 1-BSB imtihoningiz tasdiqlandi va yuklab olish uchun tayyor.", "icon": "heroicon-o-check-badge", "view": "filament-notifications::notification", "color": null, "title": "🎉 Imtihon tasdiqlandi!", "format": "filament", "status": null, "actions": [], "duration": "persistent", "viewData": [], "iconColor": "success"}	\N	2025-08-22 02:44:06	2025-08-22 02:44:06
6a3ddf42-6267-4caa-b40f-1c2a1a22bcb4	Filament\\Notifications\\DatabaseNotification	App\\Models\\User	4	{"body": "2-A-sinf | Matematika | 1-BSB imtihoningiz tasdiqlandi va yuklab olish uchun tayyor.", "icon": "heroicon-o-check-badge", "view": "filament-notifications::notification", "color": null, "title": "🎉 Imtihon tasdiqlandi!", "format": "filament", "status": null, "actions": [], "duration": "persistent", "viewData": [], "iconColor": "success"}	\N	2025-08-22 02:44:30	2025-08-22 02:44:30
\.


--
-- Data for Name: password_reset_tokens; Type: TABLE DATA; Schema: public; Owner: norbekhomidov
--

COPY public.password_reset_tokens (email, token, created_at) FROM stdin;
\.


--
-- Data for Name: roles; Type: TABLE DATA; Schema: public; Owner: norbekhomidov
--

COPY public.roles (id, name, created_at, updated_at) FROM stdin;
1	teacher	2025-08-22 01:57:55	2025-08-22 01:57:55
2	admin	2025-08-22 01:57:55	2025-08-22 01:57:55
3	superadmin	2025-08-22 01:57:55	2025-08-22 01:57:55
\.


--
-- Data for Name: sessions; Type: TABLE DATA; Schema: public; Owner: norbekhomidov
--

COPY public.sessions (id, user_id, ip_address, user_agent, payload, last_activity) FROM stdin;
gRi9zoyKxGOD0Vhxd97gt0dTbrja2IfFnFpLOVVb	4	127.0.0.1	Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36	YTo3OntzOjY6Il90b2tlbiI7czo0MDoiTFJSZk9xdWJtZkVqcWI2Y3QwaXRXVWNWV2c3SXhiWTg3T1NXRzdsWiI7czozOiJ1cmwiO2E6MDp7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjQ7czoxNzoicGFzc3dvcmRfaGFzaF93ZWIiO3M6NjA6IiQyeSQxMiQ3cUU3dnR3Snl3LzlhR1JGM1FNWkN1Y0xKY2pwY2VnREkuRHpCcUwweEhVL2dPMWJmT2tGbSI7czo0MDoiNDgwNDBlZjdmMjU0MmIzOWI5YmE5YTcyOTgzYjBkODhfZmlsdGVycyI7TjtzOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czozNzoiaHR0cDovL2xvY2FsaG9zdDo4MDAwL3RlYWNoZXItcHJvZmlsZSI7fX0=	1755845883
c0PjVDiOUcmWq5Sqse55NgikI4eRHBGQSGim3j0N	4	127.0.0.1	Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36	YTo3OntzOjY6Il90b2tlbiI7czo0MDoidExqQVdKTFE2ZXlqZ0hJVmVRYjhmb2ZKU3haT0REQUFKMnBKNUpsUCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjk6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9wcm9maWxlIjt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6NDtzOjE3OiJwYXNzd29yZF9oYXNoX3dlYiI7czo2MDoiJDJ5JDEyJDdxRTd2dHdKeXcvOWFHUkYzUU1aQ3VjTEpjanBjZWdESS5EekJxTDB4SFUvZ08xYmZPa0ZtIjtzOjQwOiI0ODA0MGVmN2YyNTQyYjM5YjliYTlhNzI5ODNiMGQ4OF9maWx0ZXJzIjtOO3M6ODoiZmlsYW1lbnQiO2E6MDp7fX0=	1755845888
\.


--
-- Data for Name: sinfs; Type: TABLE DATA; Schema: public; Owner: norbekhomidov
--

COPY public.sinfs (id, maktab_id, name, created_at, updated_at) FROM stdin;
1	2	1	2025-08-22 01:59:29	2025-08-22 01:59:29
2	2	2-A	2025-08-22 02:36:03	2025-08-22 02:36:03
\.


--
-- Data for Name: students; Type: TABLE DATA; Schema: public; Owner: norbekhomidov
--

COPY public.students (id, maktab_id, sinf_id, full_name, created_at, updated_at) FROM stdin;
1	1	1	Ulugbek Sodiqov	2025-08-22 01:59:48	2025-08-22 01:59:48
2	1	1	Lazizjon	2025-08-22 02:17:57	2025-08-22 02:17:57
3	1	1	Asilbek	2025-08-22 02:18:08	2025-08-22 02:18:08
4	1	1	Xalil Qosimov	2025-08-22 02:18:16	2025-08-22 02:18:16
5	1	1	Pierre Goyette	2025-08-22 02:18:24	2025-08-22 02:18:24
6	1	1	Gulirano	2025-08-22 02:18:36	2025-08-22 02:18:36
7	1	1	Paxlavon	2025-08-22 02:18:44	2025-08-22 02:18:44
8	1	1	Iroda	2025-08-22 02:18:50	2025-08-22 02:18:50
9	2	2	Lazizjon	2025-08-22 02:36:19	2025-08-22 02:36:19
10	2	2	Asilbek Baxtiyorov	2025-08-22 02:36:29	2025-08-22 02:36:29
\.


--
-- Data for Name: subjects; Type: TABLE DATA; Schema: public; Owner: norbekhomidov
--

COPY public.subjects (id, maktab_id, name, created_at, updated_at) FROM stdin;
1	2	English	2025-08-22 01:59:37	2025-08-22 01:59:37
2	2	Matematika	2025-08-22 02:35:29	2025-08-22 02:35:29
\.


--
-- Data for Name: teacher_subject; Type: TABLE DATA; Schema: public; Owner: norbekhomidov
--

COPY public.teacher_subject (id, teacher_id, subject_id, created_at, updated_at) FROM stdin;
1	1	1	\N	\N
2	2	2	\N	\N
\.


--
-- Data for Name: teachers; Type: TABLE DATA; Schema: public; Owner: norbekhomidov
--

COPY public.teachers (id, maktab_id, full_name, user_id, phone, created_at, updated_at, passport_serial_number, passport_jshshir, passport_photo_path, diplom_path, malaka_toifa_path, milliy_sertifikat_path, xalqaro_sertifikat_path, malumotnoma_path, telegram_id) FROM stdin;
1	2	Yolqin Muqimov	2	947319933	2025-08-22 02:00:08	2025-08-22 02:00:08	\N	\N	\N	\N	\N	\N	\N	\N	\N
2	2	Quvonchbek Abdusobirov	4	947319933	2025-08-22 02:35:46	2025-08-22 11:52:07	AB4880064	51691872873823636	teacher-documents/passport-photos/01K388S2BDRQ8SV5Y39DF30AQP.png	teacher-documents/diplomas/01K388S2BDRQ8SV5Y39DF30AQQ.png	teacher-documents/malaka-toifa/01K388S2BDRQ8SV5Y39DF30AQR.png	teacher-documents/milliy-sertifikat/01K388S2BE4QDWW8W4H1KGV3FM.png	teacher-documents/xalqaro-sertifikat/01K388S2BE4QDWW8W4H1KGV3FN.png	teacher-documents/malumotnoma/01K388S2BE4QDWW8W4H1KGV3FP.png	quvonchbek777
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: norbekhomidov
--

COPY public.users (id, maktab_id, name, email, role_id, email_verified_at, password, remember_token, created_at, updated_at) FROM stdin;
1	1	Admin	admin@example.com	3	\N	$2y$12$NrDlKDTgc1n/8RtL8stWduQgUfDo7F2ITmjWQ3783R1UoGu.ahvOm	\N	2025-08-22 01:57:56	2025-08-22 01:57:56
3	2	Farid Raxmonov	faridraxmonov@gmail.com	2	\N	$2y$12$I6ebL9giPJX7ttv0OtOULufffkWgi/KIewgwdZSq/bthpSpc3li/e	\N	2025-08-22 02:27:04	2025-08-22 02:27:04
2	2	Yolqin Muqimov	yolqinmuqimov@gmail.com	1	\N	$2y$12$8OA/avZHi6KfPl8DHRPm5.oDGrK/YWzzRXrLQsyITPZ/HqymeHARm	\N	2025-08-22 02:00:08	2025-08-22 02:27:29
4	2	Quvonchbek Abdusobirov	quvonchabdusobirov@gmail.com	1	\N	$2y$12$7qE7vtwJyw/9aGRF3QMZCucLJcjpcegDI.DzBqL0xHU/gO1bfOkFm	\N	2025-08-22 02:35:46	2025-08-22 11:57:53
\.


--
-- Name: exams_id_seq; Type: SEQUENCE SET; Schema: public; Owner: norbekhomidov
--

SELECT pg_catalog.setval('public.exams_id_seq', 5, true);


--
-- Name: exports_id_seq; Type: SEQUENCE SET; Schema: public; Owner: norbekhomidov
--

SELECT pg_catalog.setval('public.exports_id_seq', 1, false);


--
-- Name: failed_import_rows_id_seq; Type: SEQUENCE SET; Schema: public; Owner: norbekhomidov
--

SELECT pg_catalog.setval('public.failed_import_rows_id_seq', 1, false);


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: norbekhomidov
--

SELECT pg_catalog.setval('public.failed_jobs_id_seq', 1, false);


--
-- Name: imports_id_seq; Type: SEQUENCE SET; Schema: public; Owner: norbekhomidov
--

SELECT pg_catalog.setval('public.imports_id_seq', 1, false);


--
-- Name: jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: norbekhomidov
--

SELECT pg_catalog.setval('public.jobs_id_seq', 7, true);


--
-- Name: maktabs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: norbekhomidov
--

SELECT pg_catalog.setval('public.maktabs_id_seq', 2, true);


--
-- Name: marks_id_seq; Type: SEQUENCE SET; Schema: public; Owner: norbekhomidov
--

SELECT pg_catalog.setval('public.marks_id_seq', 166, true);


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: norbekhomidov
--

SELECT pg_catalog.setval('public.migrations_id_seq', 17, true);


--
-- Name: roles_id_seq; Type: SEQUENCE SET; Schema: public; Owner: norbekhomidov
--

SELECT pg_catalog.setval('public.roles_id_seq', 3, true);


--
-- Name: sinfs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: norbekhomidov
--

SELECT pg_catalog.setval('public.sinfs_id_seq', 2, true);


--
-- Name: students_id_seq; Type: SEQUENCE SET; Schema: public; Owner: norbekhomidov
--

SELECT pg_catalog.setval('public.students_id_seq', 10, true);


--
-- Name: subjects_id_seq; Type: SEQUENCE SET; Schema: public; Owner: norbekhomidov
--

SELECT pg_catalog.setval('public.subjects_id_seq', 2, true);


--
-- Name: teacher_subject_id_seq; Type: SEQUENCE SET; Schema: public; Owner: norbekhomidov
--

SELECT pg_catalog.setval('public.teacher_subject_id_seq', 2, true);


--
-- Name: teachers_id_seq; Type: SEQUENCE SET; Schema: public; Owner: norbekhomidov
--

SELECT pg_catalog.setval('public.teachers_id_seq', 2, true);


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: norbekhomidov
--

SELECT pg_catalog.setval('public.users_id_seq', 4, true);


--
-- Name: cache_locks cache_locks_pkey; Type: CONSTRAINT; Schema: public; Owner: norbekhomidov
--

ALTER TABLE ONLY public.cache_locks
    ADD CONSTRAINT cache_locks_pkey PRIMARY KEY (key);


--
-- Name: cache cache_pkey; Type: CONSTRAINT; Schema: public; Owner: norbekhomidov
--

ALTER TABLE ONLY public.cache
    ADD CONSTRAINT cache_pkey PRIMARY KEY (key);


--
-- Name: exams exams_pkey; Type: CONSTRAINT; Schema: public; Owner: norbekhomidov
--

ALTER TABLE ONLY public.exams
    ADD CONSTRAINT exams_pkey PRIMARY KEY (id);


--
-- Name: exports exports_pkey; Type: CONSTRAINT; Schema: public; Owner: norbekhomidov
--

ALTER TABLE ONLY public.exports
    ADD CONSTRAINT exports_pkey PRIMARY KEY (id);


--
-- Name: failed_import_rows failed_import_rows_pkey; Type: CONSTRAINT; Schema: public; Owner: norbekhomidov
--

ALTER TABLE ONLY public.failed_import_rows
    ADD CONSTRAINT failed_import_rows_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: norbekhomidov
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: norbekhomidov
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_uuid_unique UNIQUE (uuid);


--
-- Name: imports imports_pkey; Type: CONSTRAINT; Schema: public; Owner: norbekhomidov
--

ALTER TABLE ONLY public.imports
    ADD CONSTRAINT imports_pkey PRIMARY KEY (id);


--
-- Name: job_batches job_batches_pkey; Type: CONSTRAINT; Schema: public; Owner: norbekhomidov
--

ALTER TABLE ONLY public.job_batches
    ADD CONSTRAINT job_batches_pkey PRIMARY KEY (id);


--
-- Name: jobs jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: norbekhomidov
--

ALTER TABLE ONLY public.jobs
    ADD CONSTRAINT jobs_pkey PRIMARY KEY (id);


--
-- Name: maktabs maktabs_pkey; Type: CONSTRAINT; Schema: public; Owner: norbekhomidov
--

ALTER TABLE ONLY public.maktabs
    ADD CONSTRAINT maktabs_pkey PRIMARY KEY (id);


--
-- Name: marks marks_pkey; Type: CONSTRAINT; Schema: public; Owner: norbekhomidov
--

ALTER TABLE ONLY public.marks
    ADD CONSTRAINT marks_pkey PRIMARY KEY (id);


--
-- Name: marks marks_student_id_exam_id_problem_id_unique; Type: CONSTRAINT; Schema: public; Owner: norbekhomidov
--

ALTER TABLE ONLY public.marks
    ADD CONSTRAINT marks_student_id_exam_id_problem_id_unique UNIQUE (student_id, exam_id, problem_id);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: norbekhomidov
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: notifications notifications_pkey; Type: CONSTRAINT; Schema: public; Owner: norbekhomidov
--

ALTER TABLE ONLY public.notifications
    ADD CONSTRAINT notifications_pkey PRIMARY KEY (id);


--
-- Name: password_reset_tokens password_reset_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: norbekhomidov
--

ALTER TABLE ONLY public.password_reset_tokens
    ADD CONSTRAINT password_reset_tokens_pkey PRIMARY KEY (email);


--
-- Name: roles roles_pkey; Type: CONSTRAINT; Schema: public; Owner: norbekhomidov
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_pkey PRIMARY KEY (id);


--
-- Name: sessions sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: norbekhomidov
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT sessions_pkey PRIMARY KEY (id);


--
-- Name: sinfs sinfs_pkey; Type: CONSTRAINT; Schema: public; Owner: norbekhomidov
--

ALTER TABLE ONLY public.sinfs
    ADD CONSTRAINT sinfs_pkey PRIMARY KEY (id);


--
-- Name: students students_pkey; Type: CONSTRAINT; Schema: public; Owner: norbekhomidov
--

ALTER TABLE ONLY public.students
    ADD CONSTRAINT students_pkey PRIMARY KEY (id);


--
-- Name: subjects subjects_pkey; Type: CONSTRAINT; Schema: public; Owner: norbekhomidov
--

ALTER TABLE ONLY public.subjects
    ADD CONSTRAINT subjects_pkey PRIMARY KEY (id);


--
-- Name: teacher_subject teacher_subject_pkey; Type: CONSTRAINT; Schema: public; Owner: norbekhomidov
--

ALTER TABLE ONLY public.teacher_subject
    ADD CONSTRAINT teacher_subject_pkey PRIMARY KEY (id);


--
-- Name: teachers teachers_pkey; Type: CONSTRAINT; Schema: public; Owner: norbekhomidov
--

ALTER TABLE ONLY public.teachers
    ADD CONSTRAINT teachers_pkey PRIMARY KEY (id);


--
-- Name: users users_email_unique; Type: CONSTRAINT; Schema: public; Owner: norbekhomidov
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_unique UNIQUE (email);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: norbekhomidov
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: jobs_queue_index; Type: INDEX; Schema: public; Owner: norbekhomidov
--

CREATE INDEX jobs_queue_index ON public.jobs USING btree (queue);


--
-- Name: marks_exam_id_student_id_index; Type: INDEX; Schema: public; Owner: norbekhomidov
--

CREATE INDEX marks_exam_id_student_id_index ON public.marks USING btree (exam_id, student_id);


--
-- Name: marks_maktab_id_exam_id_index; Type: INDEX; Schema: public; Owner: norbekhomidov
--

CREATE INDEX marks_maktab_id_exam_id_index ON public.marks USING btree (maktab_id, exam_id);


--
-- Name: marks_sinf_id_exam_id_index; Type: INDEX; Schema: public; Owner: norbekhomidov
--

CREATE INDEX marks_sinf_id_exam_id_index ON public.marks USING btree (sinf_id, exam_id);


--
-- Name: notifications_notifiable_type_notifiable_id_index; Type: INDEX; Schema: public; Owner: norbekhomidov
--

CREATE INDEX notifications_notifiable_type_notifiable_id_index ON public.notifications USING btree (notifiable_type, notifiable_id);


--
-- Name: sessions_last_activity_index; Type: INDEX; Schema: public; Owner: norbekhomidov
--

CREATE INDEX sessions_last_activity_index ON public.sessions USING btree (last_activity);


--
-- Name: sessions_user_id_index; Type: INDEX; Schema: public; Owner: norbekhomidov
--

CREATE INDEX sessions_user_id_index ON public.sessions USING btree (user_id);


--
-- Name: exams exams_metod_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: norbekhomidov
--

ALTER TABLE ONLY public.exams
    ADD CONSTRAINT exams_metod_id_foreign FOREIGN KEY (metod_id) REFERENCES public.teachers(id) ON DELETE CASCADE;


--
-- Name: exams exams_sinf_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: norbekhomidov
--

ALTER TABLE ONLY public.exams
    ADD CONSTRAINT exams_sinf_id_foreign FOREIGN KEY (sinf_id) REFERENCES public.sinfs(id) ON DELETE CASCADE;


--
-- Name: exams exams_subject_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: norbekhomidov
--

ALTER TABLE ONLY public.exams
    ADD CONSTRAINT exams_subject_id_foreign FOREIGN KEY (subject_id) REFERENCES public.subjects(id) ON DELETE CASCADE;


--
-- Name: exams exams_teacher_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: norbekhomidov
--

ALTER TABLE ONLY public.exams
    ADD CONSTRAINT exams_teacher_id_foreign FOREIGN KEY (teacher_id) REFERENCES public.teachers(id) ON DELETE CASCADE;


--
-- Name: exports exports_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: norbekhomidov
--

ALTER TABLE ONLY public.exports
    ADD CONSTRAINT exports_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: failed_import_rows failed_import_rows_import_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: norbekhomidov
--

ALTER TABLE ONLY public.failed_import_rows
    ADD CONSTRAINT failed_import_rows_import_id_foreign FOREIGN KEY (import_id) REFERENCES public.imports(id) ON DELETE CASCADE;


--
-- Name: imports imports_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: norbekhomidov
--

ALTER TABLE ONLY public.imports
    ADD CONSTRAINT imports_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: marks marks_exam_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: norbekhomidov
--

ALTER TABLE ONLY public.marks
    ADD CONSTRAINT marks_exam_id_foreign FOREIGN KEY (exam_id) REFERENCES public.exams(id) ON DELETE CASCADE;


--
-- Name: marks marks_maktab_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: norbekhomidov
--

ALTER TABLE ONLY public.marks
    ADD CONSTRAINT marks_maktab_id_foreign FOREIGN KEY (maktab_id) REFERENCES public.maktabs(id) ON DELETE CASCADE;


--
-- Name: marks marks_sinf_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: norbekhomidov
--

ALTER TABLE ONLY public.marks
    ADD CONSTRAINT marks_sinf_id_foreign FOREIGN KEY (sinf_id) REFERENCES public.sinfs(id) ON DELETE CASCADE;


--
-- Name: marks marks_student_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: norbekhomidov
--

ALTER TABLE ONLY public.marks
    ADD CONSTRAINT marks_student_id_foreign FOREIGN KEY (student_id) REFERENCES public.students(id) ON DELETE CASCADE;


--
-- Name: teacher_subject teacher_subject_subject_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: norbekhomidov
--

ALTER TABLE ONLY public.teacher_subject
    ADD CONSTRAINT teacher_subject_subject_id_foreign FOREIGN KEY (subject_id) REFERENCES public.subjects(id) ON DELETE CASCADE;


--
-- Name: teacher_subject teacher_subject_teacher_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: norbekhomidov
--

ALTER TABLE ONLY public.teacher_subject
    ADD CONSTRAINT teacher_subject_teacher_id_foreign FOREIGN KEY (teacher_id) REFERENCES public.teachers(id) ON DELETE CASCADE;


--
-- Name: teachers teachers_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: norbekhomidov
--

ALTER TABLE ONLY public.teachers
    ADD CONSTRAINT teachers_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id);


--
-- Name: users users_maktab_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: norbekhomidov
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_maktab_id_foreign FOREIGN KEY (maktab_id) REFERENCES public.maktabs(id);


--
-- Name: users users_role_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: norbekhomidov
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_role_id_foreign FOREIGN KEY (role_id) REFERENCES public.roles(id);


--
-- PostgreSQL database dump complete
--

\unrestrict Ax1rV0sKw1R7bYB7akqdfNdWEzwZfvkMatqxGYoFppUEuNc6eJjFk2x3zNbJkZD

