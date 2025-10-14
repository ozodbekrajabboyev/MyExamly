--
-- PostgreSQL database dump
--

-- Dumped from database version 14.17 (Homebrew)
-- Dumped by pg_dump version 14.17 (Homebrew)

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
-- Name: cache; Type: TABLE; Schema: public; Owner: ozodbek
--

CREATE TABLE public.cache (
    key character varying(255) NOT NULL,
    value text NOT NULL,
    expiration integer NOT NULL
);


ALTER TABLE public.cache OWNER TO ozodbek;

--
-- Name: cache_locks; Type: TABLE; Schema: public; Owner: ozodbek
--

CREATE TABLE public.cache_locks (
    key character varying(255) NOT NULL,
    owner character varying(255) NOT NULL,
    expiration integer NOT NULL
);


ALTER TABLE public.cache_locks OWNER TO ozodbek;

--
-- Name: districts; Type: TABLE; Schema: public; Owner: ozodbek
--

CREATE TABLE public.districts (
    id bigint NOT NULL,
    region_id bigint NOT NULL,
    name character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.districts OWNER TO ozodbek;

--
-- Name: districts_id_seq; Type: SEQUENCE; Schema: public; Owner: ozodbek
--

CREATE SEQUENCE public.districts_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.districts_id_seq OWNER TO ozodbek;

--
-- Name: districts_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ozodbek
--

ALTER SEQUENCE public.districts_id_seq OWNED BY public.districts.id;


--
-- Name: exams; Type: TABLE; Schema: public; Owner: ozodbek
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


ALTER TABLE public.exams OWNER TO ozodbek;

--
-- Name: exams_id_seq; Type: SEQUENCE; Schema: public; Owner: ozodbek
--

CREATE SEQUENCE public.exams_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.exams_id_seq OWNER TO ozodbek;

--
-- Name: exams_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ozodbek
--

ALTER SEQUENCE public.exams_id_seq OWNED BY public.exams.id;


--
-- Name: exports; Type: TABLE; Schema: public; Owner: ozodbek
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


ALTER TABLE public.exports OWNER TO ozodbek;

--
-- Name: exports_id_seq; Type: SEQUENCE; Schema: public; Owner: ozodbek
--

CREATE SEQUENCE public.exports_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.exports_id_seq OWNER TO ozodbek;

--
-- Name: exports_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ozodbek
--

ALTER SEQUENCE public.exports_id_seq OWNED BY public.exports.id;


--
-- Name: failed_import_rows; Type: TABLE; Schema: public; Owner: ozodbek
--

CREATE TABLE public.failed_import_rows (
    id bigint NOT NULL,
    data json NOT NULL,
    import_id bigint NOT NULL,
    validation_error text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.failed_import_rows OWNER TO ozodbek;

--
-- Name: failed_import_rows_id_seq; Type: SEQUENCE; Schema: public; Owner: ozodbek
--

CREATE SEQUENCE public.failed_import_rows_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.failed_import_rows_id_seq OWNER TO ozodbek;

--
-- Name: failed_import_rows_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ozodbek
--

ALTER SEQUENCE public.failed_import_rows_id_seq OWNED BY public.failed_import_rows.id;


--
-- Name: failed_jobs; Type: TABLE; Schema: public; Owner: ozodbek
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


ALTER TABLE public.failed_jobs OWNER TO ozodbek;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: ozodbek
--

CREATE SEQUENCE public.failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.failed_jobs_id_seq OWNER TO ozodbek;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ozodbek
--

ALTER SEQUENCE public.failed_jobs_id_seq OWNED BY public.failed_jobs.id;


--
-- Name: imports; Type: TABLE; Schema: public; Owner: ozodbek
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


ALTER TABLE public.imports OWNER TO ozodbek;

--
-- Name: imports_id_seq; Type: SEQUENCE; Schema: public; Owner: ozodbek
--

CREATE SEQUENCE public.imports_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.imports_id_seq OWNER TO ozodbek;

--
-- Name: imports_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ozodbek
--

ALTER SEQUENCE public.imports_id_seq OWNED BY public.imports.id;


--
-- Name: job_batches; Type: TABLE; Schema: public; Owner: ozodbek
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


ALTER TABLE public.job_batches OWNER TO ozodbek;

--
-- Name: jobs; Type: TABLE; Schema: public; Owner: ozodbek
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


ALTER TABLE public.jobs OWNER TO ozodbek;

--
-- Name: jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: ozodbek
--

CREATE SEQUENCE public.jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.jobs_id_seq OWNER TO ozodbek;

--
-- Name: jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ozodbek
--

ALTER SEQUENCE public.jobs_id_seq OWNED BY public.jobs.id;


--
-- Name: maktabs; Type: TABLE; Schema: public; Owner: ozodbek
--

CREATE TABLE public.maktabs (
    id bigint NOT NULL,
    region_id bigint NOT NULL,
    district_id bigint NOT NULL,
    name character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.maktabs OWNER TO ozodbek;

--
-- Name: maktabs_id_seq; Type: SEQUENCE; Schema: public; Owner: ozodbek
--

CREATE SEQUENCE public.maktabs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.maktabs_id_seq OWNER TO ozodbek;

--
-- Name: maktabs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ozodbek
--

ALTER SEQUENCE public.maktabs_id_seq OWNED BY public.maktabs.id;


--
-- Name: marks; Type: TABLE; Schema: public; Owner: ozodbek
--

CREATE TABLE public.marks (
    id bigint NOT NULL,
    student_id bigint NOT NULL,
    exam_id bigint NOT NULL,
    sinf_id bigint NOT NULL,
    maktab_id bigint NOT NULL,
    problem_id integer NOT NULL,
    mark numeric(5,2) DEFAULT '0'::numeric NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.marks OWNER TO ozodbek;

--
-- Name: marks_id_seq; Type: SEQUENCE; Schema: public; Owner: ozodbek
--

CREATE SEQUENCE public.marks_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.marks_id_seq OWNER TO ozodbek;

--
-- Name: marks_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ozodbek
--

ALTER SEQUENCE public.marks_id_seq OWNED BY public.marks.id;


--
-- Name: migrations; Type: TABLE; Schema: public; Owner: ozodbek
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


ALTER TABLE public.migrations OWNER TO ozodbek;

--
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: ozodbek
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.migrations_id_seq OWNER TO ozodbek;

--
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ozodbek
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- Name: notifications; Type: TABLE; Schema: public; Owner: ozodbek
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


ALTER TABLE public.notifications OWNER TO ozodbek;

--
-- Name: password_reset_tokens; Type: TABLE; Schema: public; Owner: ozodbek
--

CREATE TABLE public.password_reset_tokens (
    email character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    created_at timestamp(0) without time zone
);


ALTER TABLE public.password_reset_tokens OWNER TO ozodbek;

--
-- Name: regions; Type: TABLE; Schema: public; Owner: ozodbek
--

CREATE TABLE public.regions (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.regions OWNER TO ozodbek;

--
-- Name: regions_id_seq; Type: SEQUENCE; Schema: public; Owner: ozodbek
--

CREATE SEQUENCE public.regions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.regions_id_seq OWNER TO ozodbek;

--
-- Name: regions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ozodbek
--

ALTER SEQUENCE public.regions_id_seq OWNED BY public.regions.id;


--
-- Name: roles; Type: TABLE; Schema: public; Owner: ozodbek
--

CREATE TABLE public.roles (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.roles OWNER TO ozodbek;

--
-- Name: roles_id_seq; Type: SEQUENCE; Schema: public; Owner: ozodbek
--

CREATE SEQUENCE public.roles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.roles_id_seq OWNER TO ozodbek;

--
-- Name: roles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ozodbek
--

ALTER SEQUENCE public.roles_id_seq OWNED BY public.roles.id;


--
-- Name: sessions; Type: TABLE; Schema: public; Owner: ozodbek
--

CREATE TABLE public.sessions (
    id character varying(255) NOT NULL,
    user_id bigint,
    ip_address character varying(45),
    user_agent text,
    payload text NOT NULL,
    last_activity integer NOT NULL
);


ALTER TABLE public.sessions OWNER TO ozodbek;

--
-- Name: sinfs; Type: TABLE; Schema: public; Owner: ozodbek
--

CREATE TABLE public.sinfs (
    id bigint NOT NULL,
    maktab_id bigint NOT NULL,
    name character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.sinfs OWNER TO ozodbek;

--
-- Name: sinfs_id_seq; Type: SEQUENCE; Schema: public; Owner: ozodbek
--

CREATE SEQUENCE public.sinfs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.sinfs_id_seq OWNER TO ozodbek;

--
-- Name: sinfs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ozodbek
--

ALTER SEQUENCE public.sinfs_id_seq OWNED BY public.sinfs.id;


--
-- Name: students; Type: TABLE; Schema: public; Owner: ozodbek
--

CREATE TABLE public.students (
    id bigint NOT NULL,
    maktab_id bigint NOT NULL,
    sinf_id bigint NOT NULL,
    full_name character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.students OWNER TO ozodbek;

--
-- Name: students_id_seq; Type: SEQUENCE; Schema: public; Owner: ozodbek
--

CREATE SEQUENCE public.students_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.students_id_seq OWNER TO ozodbek;

--
-- Name: students_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ozodbek
--

ALTER SEQUENCE public.students_id_seq OWNED BY public.students.id;


--
-- Name: subjects; Type: TABLE; Schema: public; Owner: ozodbek
--

CREATE TABLE public.subjects (
    id bigint NOT NULL,
    maktab_id bigint NOT NULL,
    name character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.subjects OWNER TO ozodbek;

--
-- Name: subjects_id_seq; Type: SEQUENCE; Schema: public; Owner: ozodbek
--

CREATE SEQUENCE public.subjects_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.subjects_id_seq OWNER TO ozodbek;

--
-- Name: subjects_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ozodbek
--

ALTER SEQUENCE public.subjects_id_seq OWNED BY public.subjects.id;


--
-- Name: teacher_subject; Type: TABLE; Schema: public; Owner: ozodbek
--

CREATE TABLE public.teacher_subject (
    id bigint NOT NULL,
    teacher_id bigint NOT NULL,
    subject_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.teacher_subject OWNER TO ozodbek;

--
-- Name: teacher_subject_id_seq; Type: SEQUENCE; Schema: public; Owner: ozodbek
--

CREATE SEQUENCE public.teacher_subject_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.teacher_subject_id_seq OWNER TO ozodbek;

--
-- Name: teacher_subject_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ozodbek
--

ALTER SEQUENCE public.teacher_subject_id_seq OWNED BY public.teacher_subject.id;


--
-- Name: teachers; Type: TABLE; Schema: public; Owner: ozodbek
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
    malaka_toifa_daraja character varying(255),
    malaka_toifa_path character varying(255),
    milliy_sertifikat1_path character varying(255),
    milliy_sertifikat2_path character varying(255),
    xalqaro_sertifikat_path character varying(255),
    malumotnoma_path character varying(255),
    ustama_sertifikat_path character varying(255),
    vazir_buyruq_path character varying(255),
    signature_path character varying(255),
    telegram_id character varying(255)
);


ALTER TABLE public.teachers OWNER TO ozodbek;

--
-- Name: teachers_id_seq; Type: SEQUENCE; Schema: public; Owner: ozodbek
--

CREATE SEQUENCE public.teachers_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.teachers_id_seq OWNER TO ozodbek;

--
-- Name: teachers_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ozodbek
--

ALTER SEQUENCE public.teachers_id_seq OWNED BY public.teachers.id;


--
-- Name: users; Type: TABLE; Schema: public; Owner: ozodbek
--

CREATE TABLE public.users (
    id bigint NOT NULL,
    maktab_id bigint DEFAULT '1'::bigint NOT NULL,
    name character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    role_id bigint DEFAULT '1'::bigint NOT NULL,
    email_verified_at timestamp(0) without time zone,
    password character varying(255) NOT NULL,
    signature_path character varying(255),
    remember_token character varying(100),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.users OWNER TO ozodbek;

--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: ozodbek
--

CREATE SEQUENCE public.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.users_id_seq OWNER TO ozodbek;

--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ozodbek
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- Name: districts id; Type: DEFAULT; Schema: public; Owner: ozodbek
--

ALTER TABLE ONLY public.districts ALTER COLUMN id SET DEFAULT nextval('public.districts_id_seq'::regclass);


--
-- Name: exams id; Type: DEFAULT; Schema: public; Owner: ozodbek
--

ALTER TABLE ONLY public.exams ALTER COLUMN id SET DEFAULT nextval('public.exams_id_seq'::regclass);


--
-- Name: exports id; Type: DEFAULT; Schema: public; Owner: ozodbek
--

ALTER TABLE ONLY public.exports ALTER COLUMN id SET DEFAULT nextval('public.exports_id_seq'::regclass);


--
-- Name: failed_import_rows id; Type: DEFAULT; Schema: public; Owner: ozodbek
--

ALTER TABLE ONLY public.failed_import_rows ALTER COLUMN id SET DEFAULT nextval('public.failed_import_rows_id_seq'::regclass);


--
-- Name: failed_jobs id; Type: DEFAULT; Schema: public; Owner: ozodbek
--

ALTER TABLE ONLY public.failed_jobs ALTER COLUMN id SET DEFAULT nextval('public.failed_jobs_id_seq'::regclass);


--
-- Name: imports id; Type: DEFAULT; Schema: public; Owner: ozodbek
--

ALTER TABLE ONLY public.imports ALTER COLUMN id SET DEFAULT nextval('public.imports_id_seq'::regclass);


--
-- Name: jobs id; Type: DEFAULT; Schema: public; Owner: ozodbek
--

ALTER TABLE ONLY public.jobs ALTER COLUMN id SET DEFAULT nextval('public.jobs_id_seq'::regclass);


--
-- Name: maktabs id; Type: DEFAULT; Schema: public; Owner: ozodbek
--

ALTER TABLE ONLY public.maktabs ALTER COLUMN id SET DEFAULT nextval('public.maktabs_id_seq'::regclass);


--
-- Name: marks id; Type: DEFAULT; Schema: public; Owner: ozodbek
--

ALTER TABLE ONLY public.marks ALTER COLUMN id SET DEFAULT nextval('public.marks_id_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: ozodbek
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- Name: regions id; Type: DEFAULT; Schema: public; Owner: ozodbek
--

ALTER TABLE ONLY public.regions ALTER COLUMN id SET DEFAULT nextval('public.regions_id_seq'::regclass);


--
-- Name: roles id; Type: DEFAULT; Schema: public; Owner: ozodbek
--

ALTER TABLE ONLY public.roles ALTER COLUMN id SET DEFAULT nextval('public.roles_id_seq'::regclass);


--
-- Name: sinfs id; Type: DEFAULT; Schema: public; Owner: ozodbek
--

ALTER TABLE ONLY public.sinfs ALTER COLUMN id SET DEFAULT nextval('public.sinfs_id_seq'::regclass);


--
-- Name: students id; Type: DEFAULT; Schema: public; Owner: ozodbek
--

ALTER TABLE ONLY public.students ALTER COLUMN id SET DEFAULT nextval('public.students_id_seq'::regclass);


--
-- Name: subjects id; Type: DEFAULT; Schema: public; Owner: ozodbek
--

ALTER TABLE ONLY public.subjects ALTER COLUMN id SET DEFAULT nextval('public.subjects_id_seq'::regclass);


--
-- Name: teacher_subject id; Type: DEFAULT; Schema: public; Owner: ozodbek
--

ALTER TABLE ONLY public.teacher_subject ALTER COLUMN id SET DEFAULT nextval('public.teacher_subject_id_seq'::regclass);


--
-- Name: teachers id; Type: DEFAULT; Schema: public; Owner: ozodbek
--

ALTER TABLE ONLY public.teachers ALTER COLUMN id SET DEFAULT nextval('public.teachers_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: ozodbek
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- Data for Name: cache; Type: TABLE DATA; Schema: public; Owner: ozodbek
--

COPY public.cache (key, value, expiration) FROM stdin;
myexamly_cache_teacher_stats_0e5f18d31a0bb1aaf6f3811262a2c9b4	a:6:{s:5:"total";i:1;s:10:"oliy_toifa";i:0;s:14:"birinchi_toifa";i:1;s:14:"ikkinchi_toifa";i:0;s:9:"mutaxasis";i:0;s:13:"belgilanmagan";i:0;}	1758454657
myexamly_cache_teacher_stats_3df0309731137f2ba128844fd203e240	a:6:{s:5:"total";i:6;s:10:"oliy_toifa";i:3;s:14:"birinchi_toifa";i:0;s:14:"ikkinchi_toifa";i:1;s:9:"mutaxasis";i:2;s:13:"belgilanmagan";i:0;}	1758454696
myexamly_cache_teacher_stats_773108d020913735960369bdbaa637d9	a:6:{s:5:"total";i:4;s:10:"oliy_toifa";i:0;s:14:"birinchi_toifa";i:1;s:14:"ikkinchi_toifa";i:1;s:9:"mutaxasis";i:2;s:13:"belgilanmagan";i:0;}	1758454760
myexamly_cache_teacher_stats_595f3c102bbd65dcda6b08aa2bfffd19	a:6:{s:5:"total";i:1;s:10:"oliy_toifa";i:0;s:14:"birinchi_toifa";i:0;s:14:"ikkinchi_toifa";i:0;s:9:"mutaxasis";i:1;s:13:"belgilanmagan";i:0;}	1758454765
myexamly_cache_teacher_stats_469c5c36109f9db8f949ca4f4b7bb77d	a:6:{s:5:"total";i:3;s:10:"oliy_toifa";i:3;s:14:"birinchi_toifa";i:0;s:14:"ikkinchi_toifa";i:0;s:9:"mutaxasis";i:0;s:13:"belgilanmagan";i:0;}	1758454768
myexamly_cache_teacher_stats_36e0f922907e4a20b09968267866f136	a:6:{s:5:"total";i:0;s:10:"oliy_toifa";i:0;s:14:"birinchi_toifa";i:0;s:14:"ikkinchi_toifa";i:0;s:9:"mutaxasis";i:0;s:13:"belgilanmagan";i:0;}	1758456058
myexamly_cache_teacher_stats_edf5c54c39066babdb6101a8d6a0cb7c	a:6:{s:5:"total";i:0;s:10:"oliy_toifa";i:0;s:14:"birinchi_toifa";i:0;s:14:"ikkinchi_toifa";i:0;s:9:"mutaxasis";i:0;s:13:"belgilanmagan";i:0;}	1758456279
myexamly_cache_teacher_stats_37c6c2cc7d6c1d99df55086e7a80ba06	a:6:{s:5:"total";i:6;s:10:"oliy_toifa";i:3;s:14:"birinchi_toifa";i:0;s:14:"ikkinchi_toifa";i:1;s:9:"mutaxasis";i:2;s:13:"belgilanmagan";i:0;}	1758470773
myexamly_cache_teacher_stats_e7119a2b9bb4726445408bcacb87a716	a:6:{s:5:"total";i:0;s:10:"oliy_toifa";i:0;s:14:"birinchi_toifa";i:0;s:14:"ikkinchi_toifa";i:0;s:9:"mutaxasis";i:0;s:13:"belgilanmagan";i:0;}	1758470797
myexamly_cache_teacher_stats_db6a315b9707c58ff46b3214ccd42e41	a:6:{s:5:"total";i:7;s:10:"oliy_toifa";i:3;s:14:"birinchi_toifa";i:1;s:14:"ikkinchi_toifa";i:1;s:9:"mutaxasis";i:2;s:13:"belgilanmagan";i:0;}	1758471116
myexamly_cache_teacher_stats_9029acb82be6c140b900d9ea4bea5585	a:6:{s:5:"total";i:3;s:10:"oliy_toifa";i:3;s:14:"birinchi_toifa";i:0;s:14:"ikkinchi_toifa";i:0;s:9:"mutaxasis";i:0;s:13:"belgilanmagan";i:0;}	1758471149
myexamly_cache_teacher_stats_637db85286358abcad366c720bf219b4	a:6:{s:5:"total";i:2;s:10:"oliy_toifa";i:0;s:14:"birinchi_toifa";i:0;s:14:"ikkinchi_toifa";i:0;s:9:"mutaxasis";i:2;s:13:"belgilanmagan";i:0;}	1758471153
myexamly_cache_teacher_stats_2aa367ad0ceb10d9f640f1087eadd975	a:6:{s:5:"total";i:7;s:10:"oliy_toifa";i:3;s:14:"birinchi_toifa";i:1;s:14:"ikkinchi_toifa";i:1;s:9:"mutaxasis";i:2;s:13:"belgilanmagan";i:0;}	1758471220
myexamly_cache_teacher_stats_4a2049268db774a050c12a4216d4e387	a:6:{s:5:"total";i:6;s:10:"oliy_toifa";i:3;s:14:"birinchi_toifa";i:0;s:14:"ikkinchi_toifa";i:1;s:9:"mutaxasis";i:2;s:13:"belgilanmagan";i:0;}	1758471299
myexamly_cache_teacher_stats_647456219afc5140498782c30bfad1ab	a:6:{s:5:"total";i:6;s:10:"oliy_toifa";i:3;s:14:"birinchi_toifa";i:0;s:14:"ikkinchi_toifa";i:1;s:9:"mutaxasis";i:2;s:13:"belgilanmagan";i:0;}	1758471302
myexamly_cache_teacher_stats_da306b24c982950d5dcb6361a323eab9	a:6:{s:5:"total";i:6;s:10:"oliy_toifa";i:3;s:14:"birinchi_toifa";i:0;s:14:"ikkinchi_toifa";i:1;s:9:"mutaxasis";i:2;s:13:"belgilanmagan";i:0;}	1758471306
myexamly_cache_teacher_stats_025ecfd67c64be8cc831aa46d02a9962	a:6:{s:5:"total";i:0;s:10:"oliy_toifa";i:0;s:14:"birinchi_toifa";i:0;s:14:"ikkinchi_toifa";i:0;s:9:"mutaxasis";i:0;s:13:"belgilanmagan";i:0;}	1758471347
myexamly_cache_teacher_stats_7d61c91220ff9224511f58f0253b72c6	a:6:{s:5:"total";i:3;s:10:"oliy_toifa";i:3;s:14:"birinchi_toifa";i:0;s:14:"ikkinchi_toifa";i:0;s:9:"mutaxasis";i:0;s:13:"belgilanmagan";i:0;}	1758471350
myexamly_cache_livewire-rate-limiter:a17961fa74e9275d529f489537f179c05d50c2f3:timer	i:1758632001;	1758632001
myexamly_cache_f1abd670358e036c31296e66b3b66c382ac00812	i:7;	1758474089
myexamly_cache_f1abd670358e036c31296e66b3b66c382ac00812:timer	i:1758474089;	1758474089
myexamly_cache_livewire-rate-limiter:a17961fa74e9275d529f489537f179c05d50c2f3	i:1;	1758632001
myexamly_cache_teacher_stats_bc9ffceefab88fabdf52b2bf7440a74b	a:6:{s:5:"total";i:8;s:10:"oliy_toifa";i:4;s:14:"birinchi_toifa";i:1;s:14:"ikkinchi_toifa";i:1;s:9:"mutaxasis";i:2;s:13:"belgilanmagan";i:0;}	1758632247
\.


--
-- Data for Name: cache_locks; Type: TABLE DATA; Schema: public; Owner: ozodbek
--

COPY public.cache_locks (key, owner, expiration) FROM stdin;
\.


--
-- Data for Name: districts; Type: TABLE DATA; Schema: public; Owner: ozodbek
--

COPY public.districts (id, region_id, name, created_at, updated_at) FROM stdin;
1	1	Amudaryo tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
2	1	Beruniy tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
3	1	Chimboy tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
4	1	Ellikqal’a tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
5	1	Kegeyli tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
6	1	Mo‘ynoq tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
7	1	Nukus shahri	2025-09-16 19:55:21	2025-09-16 19:55:21
8	1	Nukus tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
9	1	Qonliko‘l tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
10	1	Qo‘ng‘irot tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
11	1	Qorao‘zak tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
12	1	Shumanay tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
13	1	Taxtako‘pir tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
14	1	To‘rtko‘l tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
15	1	Xo‘jayli tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
16	2	Andijon shahri	2025-09-16 19:55:21	2025-09-16 19:55:21
17	2	Andijon tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
18	2	Asaka tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
19	2	Baliqchi tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
20	2	Bo‘z tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
21	2	Buloqboshi tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
22	2	Izboskan tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
23	2	Jalaquduq tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
24	2	Marhamat tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
25	2	Oltinko‘l tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
26	2	Paxtaobod tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
27	2	Shahrixon tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
28	2	Ulug‘nor tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
29	2	Xo‘jaobod tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
30	2	Qo‘rg‘ontepa tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
31	3	Buxoro shahri	2025-09-16 19:55:21	2025-09-16 19:55:21
32	3	Buxoro tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
33	3	G‘ijduvon tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
34	3	Jondor tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
35	3	Kogon shahri	2025-09-16 19:55:21	2025-09-16 19:55:21
36	3	Kogon tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
37	3	Olot tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
38	3	Peshku tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
39	3	Qorako‘l tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
40	3	Qorovulbozor tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
41	3	Romitan tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
42	3	Shofirkon tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
43	3	Vobkent tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
44	4	Farg‘ona shahri	2025-09-16 19:55:21	2025-09-16 19:55:21
45	4	Bag‘dod tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
46	4	Beshariq tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
47	4	Buvayda tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
48	4	Dang‘ara tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
49	4	Farg‘ona tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
50	4	Furqat tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
51	4	Qo‘qon shahri	2025-09-16 19:55:21	2025-09-16 19:55:21
52	4	Qo‘qon tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
53	4	Quva tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
54	4	Quvasoy shahri	2025-09-16 19:55:21	2025-09-16 19:55:21
55	4	Quvasoy tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
56	4	Rishton tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
57	4	So‘x tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
58	4	Toshloq tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
59	4	Uchko‘prik tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
60	4	Yozyovon tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
61	5	Jizzax shahri	2025-09-16 19:55:21	2025-09-16 19:55:21
62	5	Arnasoy tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
63	5	Baxmal tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
64	5	Do‘stlik tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
65	5	Forish tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
66	5	G‘allaorol tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
67	5	Jizzax tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
68	5	Mirzacho‘l tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
69	5	Paxtakor tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
70	5	Yangiobod tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
71	5	Zafarobod tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
72	5	Zarbdor tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
73	6	Urganch shahri	2025-09-16 19:55:21	2025-09-16 19:55:21
74	6	Bog‘ot tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
75	6	Gurlan tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
76	6	Xazorasp tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
77	6	Xonqa tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
78	6	Qo‘shko‘pir tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
79	6	Shovot tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
80	6	Urganch tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
81	6	Yangiariq tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
82	6	Yangibozor tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
83	7	Namangan shahri	2025-09-16 19:55:21	2025-09-16 19:55:21
84	7	Chortoq tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
85	7	Chust tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
86	7	Kosonsoy tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
87	7	Mingbuloq tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
88	7	Namangan tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
89	7	Norin tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
90	7	Pop tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
91	7	To‘raqo‘rg‘on tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
92	7	Uchqo‘rg‘on tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
93	7	Yangiqo‘rg‘on tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
94	8	Navoiy shahri	2025-09-16 19:55:21	2025-09-16 19:55:21
95	8	Konimex tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
96	8	Karmana tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
97	8	Navbahor tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
98	8	Nurota tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
99	8	Qiziltepa tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
100	8	Tomdi tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
101	8	Uchquduq tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
102	8	Xatirchi tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
103	9	Qarshi shahri	2025-09-16 19:55:21	2025-09-16 19:55:21
104	9	Chiroqchi tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
105	9	Dehqonobod tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
106	9	G‘uzor tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
107	9	Kasbi tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
108	9	Kitob tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
109	9	Koson tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
110	9	Mirishkor tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
111	9	Muborak tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
112	9	Nishon tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
113	9	Qamashi tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
114	9	Qarshi tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
115	9	Shahrisabz shahri	2025-09-16 19:55:21	2025-09-16 19:55:21
116	9	Shahrisabz tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
117	9	Yakkabog‘ tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
118	10	Samarqand shahri	2025-09-16 19:55:21	2025-09-16 19:55:21
119	10	Bulung‘ur tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
120	10	Ishtixon tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
121	10	Jomboy tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
122	10	Kattaqo‘rg‘on shahri	2025-09-16 19:55:21	2025-09-16 19:55:21
123	10	Kattaqo‘rg‘on tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
124	10	Narpay tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
125	10	Nurobod tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
126	10	Oqdaryo tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
127	10	Paxtachi tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
128	10	Payariq tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
129	10	Pastdarg‘om tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
130	10	Qo‘shrabot tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
131	10	Samarqand tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
132	10	Toyloq tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
133	10	Urgut tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
134	11	Guliston shahri	2025-09-16 19:55:21	2025-09-16 19:55:21
135	11	Boyovut tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
136	11	Guliston tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
137	11	Mirzaobod tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
138	11	Oqoltin tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
139	11	Sardoba tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
140	11	Sayxunobod tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
141	11	Sirdaryo tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
142	11	Xovos tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
143	12	Termiz shahri	2025-09-16 19:55:21	2025-09-16 19:55:21
144	12	Angor tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
145	12	Bandixon tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
146	12	Boysun tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
147	12	Denov tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
148	12	Jarqo‘rg‘on tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
149	12	Qiziriq tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
150	12	Qumqo‘rg‘on tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
151	12	Muzrabot tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
152	12	Oltinsoy tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
153	12	Sariosiyo tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
154	12	Sherobod tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
155	12	Sho‘rchi tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
156	12	Termiz tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
157	12	Uzun tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
158	13	Angren shahri	2025-09-16 19:55:21	2025-09-16 19:55:21
159	13	Bekobod shahri	2025-09-16 19:55:21	2025-09-16 19:55:21
160	13	Bekobod tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
161	13	Bo‘ka tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
162	13	Chinoz tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
163	13	Ohangaron shahri	2025-09-16 19:55:21	2025-09-16 19:55:21
164	13	Ohangaron tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
165	13	Oqqo‘rg‘on tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
166	13	Parkent tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
167	13	Piskent tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
168	13	Quyichirchiq tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
169	13	O‘rtachirchiq tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
170	13	Yuqorichirchiq tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
171	13	Chirchiq shahri	2025-09-16 19:55:21	2025-09-16 19:55:21
172	13	Zangiota tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
173	14	Bektemir tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
174	14	Chilonzor tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
175	14	Mirzo Ulug‘bek tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
176	14	Mirobod tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
177	14	Olmazor tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
178	14	Sergeli tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
179	14	Shayxontohur tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
180	14	Uchtepa tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
181	14	Yakkasaroy tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
182	14	Yashnobod tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
183	14	Yunusobod tumani	2025-09-16 19:55:21	2025-09-16 19:55:21
\.


--
-- Data for Name: exams; Type: TABLE DATA; Schema: public; Owner: ozodbek
--

COPY public.exams (id, maktab_id, sinf_id, subject_id, teacher_id, type, serial_number, metod_id, problems, status, created_at, updated_at) FROM stdin;
1	2	1	2	2	CHSB	1	2	[{"id": 1, "max_mark": "10"}, {"id": 2, "max_mark": "10"}]	approved	2025-09-17 11:50:00	2025-09-17 11:51:24
2	3	2	8	8	BSB	1	8	[{"id": 1, "max_mark": "10"}, {"id": 2, "max_mark": "15"}, {"id": 3, "max_mark": "15"}]	approved	2025-09-21 22:07:59	2025-09-21 22:09:30
\.


--
-- Data for Name: exports; Type: TABLE DATA; Schema: public; Owner: ozodbek
--

COPY public.exports (id, completed_at, file_disk, file_name, exporter, processed_rows, total_rows, successful_rows, user_id, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: failed_import_rows; Type: TABLE DATA; Schema: public; Owner: ozodbek
--

COPY public.failed_import_rows (id, data, import_id, validation_error, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: failed_jobs; Type: TABLE DATA; Schema: public; Owner: ozodbek
--

COPY public.failed_jobs (id, uuid, connection, queue, payload, exception, failed_at) FROM stdin;
\.


--
-- Data for Name: imports; Type: TABLE DATA; Schema: public; Owner: ozodbek
--

COPY public.imports (id, completed_at, file_name, file_path, importer, processed_rows, total_rows, successful_rows, user_id, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: job_batches; Type: TABLE DATA; Schema: public; Owner: ozodbek
--

COPY public.job_batches (id, name, total_jobs, pending_jobs, failed_jobs, failed_job_ids, options, cancelled_at, created_at, finished_at) FROM stdin;
\.


--
-- Data for Name: jobs; Type: TABLE DATA; Schema: public; Owner: ozodbek
--

COPY public.jobs (id, queue, payload, attempts, reserved_at, available_at, created_at) FROM stdin;
\.


--
-- Data for Name: maktabs; Type: TABLE DATA; Schema: public; Owner: ozodbek
--

COPY public.maktabs (id, region_id, district_id, name, created_at, updated_at) FROM stdin;
1	14	183	MyExamly Platform	2025-09-16 19:55:31	2025-09-16 19:55:31
2	3	31	Al-Xorazmiy nomidagi ixtisoslashtirilgan maktabi Buxoro filiali	2025-09-16 22:19:55	2025-09-16 22:19:55
3	10	122	Kattaqo'rg'on shahar ixtisoslashtirilgan maktab	2025-09-21 21:35:17	2025-09-21 21:38:13
\.


--
-- Data for Name: marks; Type: TABLE DATA; Schema: public; Owner: ozodbek
--

COPY public.marks (id, student_id, exam_id, sinf_id, maktab_id, problem_id, mark, created_at, updated_at) FROM stdin;
1	2	1	1	2	1	10.00	2025-09-17 11:50:19	2025-09-17 11:50:19
2	2	1	1	2	2	5.00	2025-09-17 11:50:19	2025-09-17 11:50:19
3	1	1	1	2	1	3.00	2025-09-17 11:50:19	2025-09-17 11:50:19
4	1	1	1	2	2	4.00	2025-09-17 11:50:19	2025-09-17 11:50:19
5	4	1	1	2	1	5.00	2025-09-17 11:50:19	2025-09-17 11:50:19
6	4	1	1	2	2	8.00	2025-09-17 11:50:19	2025-09-17 11:50:19
7	3	1	1	2	1	8.00	2025-09-17 11:50:19	2025-09-17 11:50:19
8	3	1	1	2	2	5.00	2025-09-17 11:50:19	2025-09-17 11:50:19
9	5	2	2	3	1	8.00	2025-09-21 22:08:30	2025-09-21 22:08:30
10	5	2	2	3	2	8.00	2025-09-21 22:08:30	2025-09-21 22:08:30
11	5	2	2	3	3	8.00	2025-09-21 22:08:30	2025-09-21 22:08:30
12	8	2	2	3	1	7.00	2025-09-21 22:08:30	2025-09-21 22:08:30
13	8	2	2	3	2	7.00	2025-09-21 22:08:30	2025-09-21 22:08:30
14	8	2	2	3	3	7.00	2025-09-21 22:08:30	2025-09-21 22:08:30
15	7	2	2	3	1	6.00	2025-09-21 22:08:30	2025-09-21 22:08:30
16	7	2	2	3	2	6.00	2025-09-21 22:08:30	2025-09-21 22:08:30
17	7	2	2	3	3	6.00	2025-09-21 22:08:30	2025-09-21 22:08:30
18	6	2	2	3	1	5.00	2025-09-21 22:08:30	2025-09-21 22:08:30
19	6	2	2	3	2	5.00	2025-09-21 22:08:30	2025-09-21 22:08:30
20	6	2	2	3	3	5.00	2025-09-21 22:08:30	2025-09-21 22:08:30
\.


--
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: ozodbek
--

COPY public.migrations (id, migration, batch) FROM stdin;
1	0000_00_00_000000_roles	1
2	0000_00_00_000001_create_regions_table	1
3	0000_00_00_000002_create_districts_table	1
4	0001_01_00_000000_create_maktabs_table	1
5	0001_01_01_000000_create_users_table	1
6	0001_01_01_000001_create_cache_table	1
7	0001_01_01_000002_create_jobs_table	1
8	2025_03_01_181838_create_sinfs_table	1
9	2025_03_01_181903_create_students_table	1
10	2025_03_01_181920_create_subjects_table	1
11	2025_03_01_181931_create_teachers_table	1
12	2025_03_07_143114_teacher_subject	1
13	2025_03_17_131658_create_exams_table	1
14	2025_03_17_131724_create_marks_table	1
15	2025_05_20_160116_create_notifications_table	1
16	2025_05_20_160147_create_imports_table	1
17	2025_05_20_160148_create_exports_table	1
18	2025_05_20_160149_create_failed_import_rows_table	1
19	2025_08_20_095010_add_profile_fields_to_teachers_table	1
\.


--
-- Data for Name: notifications; Type: TABLE DATA; Schema: public; Owner: ozodbek
--

COPY public.notifications (id, type, notifiable_type, notifiable_id, data, read_at, created_at, updated_at) FROM stdin;
d03a29f2-7e9c-4717-a126-49db92c03548	Filament\\Notifications\\DatabaseNotification	App\\Models\\User	1	{"body": "Email: asilbek@gmail.com \\n Password: dd8JbSV;uW=p", "icon": null, "view": "filament-notifications::notification", "color": "green", "title": "Your credentials are ready!", "format": "filament", "status": null, "actions": [], "duration": "persistent", "viewData": [], "iconColor": null}	2025-09-16 22:52:08	2025-09-16 19:58:02	2025-09-16 22:52:08
e3adf2fa-285b-490b-ad6f-04f9367615d4	Filament\\Notifications\\DatabaseNotification	App\\Models\\User	4	{"body": "1-sinf | Ingliz tili | 1-CHSB imtihoningiz tasdiqlandi va yuklab olish uchun tayyor.", "icon": "heroicon-o-check-badge", "view": "filament-notifications::notification", "color": null, "title": "🎉 Imtihon tasdiqlandi!", "format": "filament", "status": null, "actions": [], "duration": "persistent", "viewData": [], "iconColor": "success"}	\N	2025-09-17 11:51:27	2025-09-17 11:51:27
094854d2-5ab1-4d7e-8c25-c1c5528d74b5	Filament\\Notifications\\DatabaseNotification	App\\Models\\User	3	{"body": "Email: yolqinmuqimov@gmail.com \\n Password: S5rJ-Y,%Sr1(", "icon": null, "view": "filament-notifications::notification", "color": "green", "title": "Your credentials are ready!", "format": "filament", "status": null, "actions": [], "duration": "persistent", "viewData": [], "iconColor": null}	2025-09-17 11:58:30	2025-09-16 22:28:30	2025-09-17 11:58:30
25f11601-e538-4596-995a-1ecc7e8dac7b	Filament\\Notifications\\DatabaseNotification	App\\Models\\User	3	{"body": "Email: ozod@gmail.com \\n Password: 0!5#nzc=Noe#", "icon": null, "view": "filament-notifications::notification", "color": "green", "title": "Your credentials are ready!", "format": "filament", "status": null, "actions": [], "duration": "persistent", "viewData": [], "iconColor": null}	2025-09-17 11:58:30	2025-09-16 22:28:30	2025-09-17 11:58:30
58076684-60e8-4110-a1bf-ec6a03de4d0c	Filament\\Notifications\\DatabaseNotification	App\\Models\\User	3	{"body": "1-sinf | Ingliz tili | 1-CHSB imtihonini tasdiqlash uchun so‘rov yuborildi.", "icon": "heroicon-o-document-check", "view": "filament-notifications::notification", "color": null, "title": "Imtihonni tasdiqlash so‘rovi", "format": "filament", "status": null, "actions": [], "duration": "persistent", "viewData": [], "iconColor": "warning"}	2025-09-17 11:58:30	2025-09-17 11:51:06	2025-09-17 11:58:30
d8a8a58d-c271-4cdb-8687-cc0bad538399	Filament\\Notifications\\DatabaseNotification	App\\Models\\User	3	{"body": "Email: nafosat@gmail.com \\n Password: F:k,CKRO&D89", "icon": null, "view": "filament-notifications::notification", "color": "green", "title": "Your credentials are ready!", "format": "filament", "status": null, "actions": [], "duration": "persistent", "viewData": [], "iconColor": null}	\N	2025-09-17 13:02:28	2025-09-17 13:02:28
729acd5d-60b6-4d9a-a1a0-044821ab088b	Filament\\Notifications\\DatabaseNotification	App\\Models\\User	3	{"body": "Email: adizbek@gmail.com \\n Password: bEl9fiu7TH1i", "icon": null, "view": "filament-notifications::notification", "color": "green", "title": "Your credentials are ready!", "format": "filament", "status": null, "actions": [], "duration": "persistent", "viewData": [], "iconColor": null}	\N	2025-09-17 15:02:46	2025-09-17 15:02:46
3af7d5e2-122e-4ca7-811b-1204b20b7692	Filament\\Notifications\\DatabaseNotification	App\\Models\\User	3	{"body": "Email: gulchexra@gmail.com \\n Password: V4@)?Ef_MY#B", "icon": null, "view": "filament-notifications::notification", "color": "green", "title": "Your credentials are ready!", "format": "filament", "status": null, "actions": [], "duration": "persistent", "viewData": [], "iconColor": null}	\N	2025-09-17 21:47:19	2025-09-17 21:47:19
6b951d44-a03b-4e0b-ba6b-3c27d07f40ec	Filament\\Notifications\\DatabaseNotification	App\\Models\\User	3	{"body": "Email: husniddin@gmail.com \\n Password: !Hl0=zJkjnGR", "icon": null, "view": "filament-notifications::notification", "color": "green", "title": "Your credentials are ready!", "format": "filament", "status": null, "actions": [], "duration": "persistent", "viewData": [], "iconColor": null}	\N	2025-09-17 22:14:01	2025-09-17 22:14:01
76a704a3-203e-4ed5-a1ab-92d4789f9b5f	Filament\\Notifications\\DatabaseNotification	App\\Models\\User	1	{"body": "Email: doston@gmail.com \\n Password: j8Ftm9fp*4AY", "icon": null, "view": "filament-notifications::notification", "color": "green", "title": "Your credentials are ready!", "format": "filament", "status": null, "actions": [], "duration": "persistent", "viewData": [], "iconColor": null}	2025-09-19 17:30:49	2025-09-17 12:56:40	2025-09-19 17:30:49
9f39d9bf-7d16-41f3-8b28-194458f54fe5	Filament\\Notifications\\DatabaseNotification	App\\Models\\User	14	{"body": "Email: commo1318@gmail.com \\n Password: $5w+cfK$Fk@g", "icon": null, "view": "filament-notifications::notification", "color": "green", "title": "Your credentials are ready!", "format": "filament", "status": null, "actions": [], "duration": "persistent", "viewData": [], "iconColor": null}	\N	2025-09-21 21:44:16	2025-09-21 21:44:16
2cbfd7b4-fd38-423e-bdf6-2f93d37022c7	Filament\\Notifications\\DatabaseNotification	App\\Models\\User	14	{"body": "5 RED-sinf | Ingliz tili | 1-BSB imtihonini tasdiqlash uchun so‘rov yuborildi.", "icon": "heroicon-o-document-check", "view": "filament-notifications::notification", "color": null, "title": "Imtihonni tasdiqlash so‘rovi", "format": "filament", "status": null, "actions": [], "duration": "persistent", "viewData": [], "iconColor": "warning"}	\N	2025-09-21 22:09:05	2025-09-21 22:09:05
e1c045d1-35a1-415d-a6d1-eab1993a11b9	Filament\\Notifications\\DatabaseNotification	App\\Models\\User	15	{"body": "5 RED-sinf | Ingliz tili | 1-BSB imtihoningiz tasdiqlandi va yuklab olish uchun tayyor.", "icon": "heroicon-o-check-badge", "view": "filament-notifications::notification", "color": null, "title": "🎉 Imtihon tasdiqlandi!", "format": "filament", "status": null, "actions": [], "duration": "persistent", "viewData": [], "iconColor": "success"}	\N	2025-09-21 22:09:31	2025-09-21 22:09:31
\.


--
-- Data for Name: password_reset_tokens; Type: TABLE DATA; Schema: public; Owner: ozodbek
--

COPY public.password_reset_tokens (email, token, created_at) FROM stdin;
\.


--
-- Data for Name: regions; Type: TABLE DATA; Schema: public; Owner: ozodbek
--

COPY public.regions (id, name, created_at, updated_at) FROM stdin;
1	Qoraqalpogʻiston Respublikasi	2025-09-16 19:55:21	2025-09-16 19:55:21
2	Andijon viloyati	2025-09-16 19:55:21	2025-09-16 19:55:21
3	Buxoro viloyati	2025-09-16 19:55:21	2025-09-16 19:55:21
4	Fargʻona viloyati	2025-09-16 19:55:21	2025-09-16 19:55:21
5	Jizzax viloyati	2025-09-16 19:55:21	2025-09-16 19:55:21
6	Xorazm viloyati	2025-09-16 19:55:21	2025-09-16 19:55:21
7	Namangan viloyati	2025-09-16 19:55:21	2025-09-16 19:55:21
8	Navoiy viloyati	2025-09-16 19:55:21	2025-09-16 19:55:21
9	Qashqadaryo viloyati	2025-09-16 19:55:21	2025-09-16 19:55:21
10	Samarqand viloyati	2025-09-16 19:55:21	2025-09-16 19:55:21
11	Sirdaryo viloyati	2025-09-16 19:55:21	2025-09-16 19:55:21
12	Surxondaryo viloyati	2025-09-16 19:55:21	2025-09-16 19:55:21
13	Toshkent viloyati	2025-09-16 19:55:21	2025-09-16 19:55:21
14	Toshkent shahri	2025-09-16 19:55:21	2025-09-16 19:55:21
\.


--
-- Data for Name: roles; Type: TABLE DATA; Schema: public; Owner: ozodbek
--

COPY public.roles (id, name, created_at, updated_at) FROM stdin;
1	teacher	2025-09-16 19:55:31	2025-09-16 19:55:31
2	admin	2025-09-16 19:55:31	2025-09-16 19:55:31
3	superadmin	2025-09-16 19:55:31	2025-09-16 19:55:31
\.


--
-- Data for Name: sessions; Type: TABLE DATA; Schema: public; Owner: ozodbek
--

COPY public.sessions (id, user_id, ip_address, user_agent, payload, last_activity) FROM stdin;
08tltMss49kpXiYMnixvsBIyFOmSyXUjl65iTF8y	1	127.0.0.1	Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36	YTo2OntzOjY6Il90b2tlbiI7czo0MDoiUnVGRmdoWkZ3NU5IeU1NbnhJdmZRMVBjMXU4dlhXdFlDcWRiZU5QbiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDE6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9tYWxha2Etc3RhdGlzdGlrYXNpIjt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjE3OiJwYXNzd29yZF9oYXNoX3dlYiI7czo2MDoiJDJ5JDEyJEkud05BYXF2LnJHbE90TFdtb1ZRME94d3hPZGJabk4zd0xJNXFKSHpsUTZkUnMza3g3bkJlIjtzOjQwOiI0ODA0MGVmN2YyNTQyYjM5YjliYTlhNzI5ODNiMGQ4OF9maWx0ZXJzIjtOO30=	1758475124
DYYuXodpAmAlLmzZqmM90qpNM2Q7D9RFe0hF769p	1	127.0.0.1	Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36	YTo3OntzOjY6Il90b2tlbiI7czo0MDoiTVVIWFRZdHFodld3VGIwdEYxVk5jdUU5WnVVZDE1YjkyZkhVTXRLUyI7czozOiJ1cmwiO2E6MDp7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjQxOiJodHRwOi8vbG9jYWxob3N0OjgwMDAvbWFsYWthLXN0YXRpc3Rpa2FzaSI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7czoxNzoicGFzc3dvcmRfaGFzaF93ZWIiO3M6NjA6IiQyeSQxMiRJLndOQWFxdi5yR2xPdExXbW9WUTBPeHd4T2RiWm5OM3dMSTVxSkh6bFE2ZFJzM2t4N25CZSI7czo0MDoiNDgwNDBlZjdmMjU0MmIzOWI5YmE5YTcyOTgzYjBkODhfZmlsdGVycyI7Tjt9	1758631948
\.


--
-- Data for Name: sinfs; Type: TABLE DATA; Schema: public; Owner: ozodbek
--

COPY public.sinfs (id, maktab_id, name, created_at, updated_at) FROM stdin;
1	2	1	2025-09-17 11:48:40	2025-09-17 11:48:40
2	3	5 RED	2025-09-21 22:04:55	2025-09-21 22:04:55
3	3	7 GREEN	2025-09-21 22:05:52	2025-09-21 22:05:52
\.


--
-- Data for Name: students; Type: TABLE DATA; Schema: public; Owner: ozodbek
--

COPY public.students (id, maktab_id, sinf_id, full_name, created_at, updated_at) FROM stdin;
1	2	1	Inez Guy	2025-09-17 11:48:48	2025-09-17 11:48:48
2	2	1	Charde Richard	2025-09-17 11:48:53	2025-09-17 11:48:53
3	2	1	Timothy Merrill	2025-09-17 11:48:56	2025-09-17 11:48:56
4	2	1	Ivana Kerr	2025-09-17 11:48:58	2025-09-17 11:48:58
5	3	2	Doston Ubadullayev	2025-09-21 22:06:51	2025-09-21 22:06:51
6	3	2	Qodir Ismoilov	2025-09-21 22:06:57	2025-09-21 22:06:57
7	3	2	John Doe	2025-09-21 22:07:02	2025-09-21 22:07:02
8	3	2	Jack Johnson	2025-09-21 22:07:07	2025-09-21 22:07:07
\.


--
-- Data for Name: subjects; Type: TABLE DATA; Schema: public; Owner: ozodbek
--

COPY public.subjects (id, maktab_id, name, created_at, updated_at) FROM stdin;
1	1	Math	2025-09-16 19:57:30	2025-09-16 19:57:38
2	2	Ingliz tili	2025-09-16 22:21:49	2025-09-16 22:21:49
3	2	Fizika	2025-09-17 15:01:56	2025-09-17 15:01:56
4	2	Ona tili	2025-09-17 21:46:27	2025-09-17 21:46:27
5	2	Tarix	2025-09-17 22:12:55	2025-09-17 22:12:55
6	3	Matematika	2025-09-21 21:43:26	2025-09-21 21:43:26
7	3	Informatika	2025-09-21 21:43:32	2025-09-21 21:43:32
8	3	Ingliz tili	2025-09-21 21:43:38	2025-09-21 21:43:38
9	3	Fizika	2025-09-21 21:43:41	2025-09-21 21:43:41
\.


--
-- Data for Name: teacher_subject; Type: TABLE DATA; Schema: public; Owner: ozodbek
--

COPY public.teacher_subject (id, teacher_id, subject_id, created_at, updated_at) FROM stdin;
1	1	1	\N	\N
2	2	2	\N	\N
3	3	2	\N	\N
4	4	2	\N	\N
5	5	3	\N	\N
6	6	4	\N	\N
7	7	5	\N	\N
8	8	8	\N	\N
\.


--
-- Data for Name: teachers; Type: TABLE DATA; Schema: public; Owner: ozodbek
--

COPY public.teachers (id, maktab_id, full_name, user_id, phone, created_at, updated_at, passport_serial_number, passport_jshshir, passport_photo_path, diplom_path, malaka_toifa_daraja, malaka_toifa_path, milliy_sertifikat1_path, milliy_sertifikat2_path, xalqaro_sertifikat_path, malumotnoma_path, ustama_sertifikat_path, vazir_buyruq_path, signature_path, telegram_id) FROM stdin;
1	1	Asilbek Baxtiyorov	2	947319933	2025-09-16 19:58:01	2025-09-17 12:55:08	\N	\N	\N	\N	1-toifa	teacher-documents/malaka-toifa/01K5BAQ4EJJXCTQW79674WPRP4.pdf	\N	\N	\N	\N	\N	\N	\N	\N
3	2	Norbek Hamidov	5	947319933	2025-09-16 22:22:56	2025-09-17 13:01:15	\N	\N	\N	\N	2-toifa	teacher-documents/malaka-toifa/01K5BB2ABM66S3ZD1A2MAVS7GV.pdf	\N	\N	\N	\N	\N	\N	\N	\N
4	2	Gadoyeva Nafosat	10	947319933	2025-09-17 13:02:26	2025-09-17 13:03:01	\N	\N	\N	\N	mutaxasis	\N	\N	\N	\N	\N	\N	\N	\N	\N
6	2	Ortiqova Gulchexra	12	947319933	2025-09-17 21:47:18	2025-09-17 22:02:32	AB4880064	51691872873823636	teacher-documents/passport-photos/01K5CA1EW8D204HEQFBMX6F927.pdf	teacher-documents/diplomas/01K5C9WDP4NV1XBRMK7JB27PFT.JPG	oliy-toifa	teacher-documents/malaka-toifa/01K5C9WDP5XGBB415RZJMFJJFE.pdf	teacher-documents/milliy-sertifikat1/01K5C9WDP6FHNMXR6K7FRG4CA6.pdf	teacher-documents/milliy-sertifikat2/01K5C9WDP74C7YC71QR4EJKE4H.pdf	teacher-documents/xalqaro-sertifikat/01K5CA0FA2MCCYK4CGAN3QPHP2.pdf	teacher-documents/malumotnoma/01K5C9WDP8QR7AXN4HSSFFC4TR.pdf	\N	teacher-documents/vazir-buyruq/01K5C9WDP74C7YC71QR4EJKE4J.pdf	signatures/01K5C9WDP3GF6EDXN4VEDG04GF.png	gulchexra
7	2	Hotamov Husniddin	13	947319933	2025-09-17 22:13:58	2025-09-17 22:19:33	AB4880064	51691872873823636	teacher-documents/passport-photos/01K5CB0KB9Y3E5XH50YW93MNA4.pdf	teacher-documents/diplomas/01K5CB0KBB6BA34Z76C57T5R78.pdf	oliy-toifa	teacher-documents/malaka-toifa/01K5CB0KBCPQK6C0EK3TEY6WEC.pdf	teacher-documents/milliy-sertifikat1/01K5CB0KBDHKTP0X2NWM8FCPZZ.pdf	teacher-documents/milliy-sertifikat2/01K5CB0KBDHKTP0X2NWM8FCQ00.pdf	\N	teacher-documents/malumotnoma/01K5CB0KBEHAMN2AWHYYZW5C4G.pdf	teacher-documents/ustama-sertifikat/01K5CB0KBEHAMN2AWHYYZW5C4F.pdf	\N	signatures/01K5CB0KBB6BA34Z76C57T5R77.png	husniddin
2	2	Yolqin Muqimov	4	947319933	2025-09-16 22:22:04	2025-09-18 00:01:45	AB4880064	51691872873823636	teacher-documents/passport-photos/01K59S8KYCNGEJAD0YYZ3A92H5.png	teacher-documents/diplomas/01K59S8KYFZBV41HEQAZSPKGZK.pdf	oliy-toifa	teacher-documents/malaka-toifa/01K59S8KYFZBV41HEQAZSPKGZM.pdf	teacher-documents/milliy-sertifikat1/01K59S8KYGCZNKQR6G221F4SC6.png	teacher-documents/milliy-sertifikat2/01K5CGVQX4WBC4DY1TKYS9P1T7.pdf	teacher-documents/xalqaro-sertifikat/01K59S8KYGCZNKQR6G221F4SC7.pdf	teacher-documents/malumotnoma/01K59S8KYH3G5NKWDD394EM7JN.pdf	teacher-documents/ustama-sertifikat/01K59S8KYH3G5NKWDD394EM7JK.pdf	teacher-documents/vazir-buyruq/01K59S8KYH3G5NKWDD394EM7JM.png	signatures/01K59S8KYE5DT54BZ94PGYNDYN.png	quvonchbek777
5	2	Hojiyev Adizbek	11	947319933	2025-09-17 15:02:42	2025-09-21 14:06:40	AB1234567	51691872873823636	teacher-documents/passport-photos/01K5NRCZX4ZYF69N91AA88SFGJ.pdf	teacher-documents/diplomas/01K5NRCZX6SN3VYZ0R45QV7XEZ.pdf	mutaxasis	\N	teacher-documents/milliy-sertifikat1/01K5NRCZX6SN3VYZ0R45QV7XF0.pdf	\N	\N	\N	teacher-documents/ustama-sertifikat/01K5NRCZX7PYKQAHWQD5DHSP1N.pdf	\N	signatures/01K5NRCZX5KFH461QP7KQ4XP1X.png	@adizbek_001
8	3	Davronov Oybek Jamolovich	15	998882524	2025-09-21 21:44:10	2025-09-21 22:01:42	AE1781234	51691872873823636	teacher-documents/passport-photos/01K5PKJSMN53107TWMTTKR4DGZ.pdf	teacher-documents/diplomas/01K5PKJSMRX6NCJWTVS86D5THS.JPG	oliy-toifa	teacher-documents/malaka-toifa/01K5PKJSMT9XQ2ZWZM2GRGPYDB.pdf	teacher-documents/milliy-sertifikat1/01K5PKJSMWW8AYJAHGX0XC91NQ.pdf	teacher-documents/milliy-sertifikat2/01K5PKJSMX7W5Q0264RMJG6V6D.pdf	\N	teacher-documents/malumotnoma/01K5PKJSMQMCBKV3S8T8QN1M6D.pdf	\N	\N	signatures/01K5PKJSMPB7WFAJ7N52K9CHA6.png	oybek111
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: ozodbek
--

COPY public.users (id, maktab_id, name, email, role_id, email_verified_at, password, signature_path, remember_token, created_at, updated_at) FROM stdin;
2	1	Asilbek Baxtiyorov	asilbek@gmail.com	1	\N	$2y$12$gvHGJW2SmzpOoTaK9sf97efICy6CzoLDAvNunbic4Gj6BDD7XFpMW	\N	\N	2025-09-16 19:58:01	2025-09-16 19:59:00
3	2	Farid Raxmonov	faridraxmonov@gmail.com	2	\N	$2y$12$RfNd51GgEc0oqzuj13Hv4.ynnaSBnuUAleT4vPRWOqMTuumysC1d2	signatures/01K59RNGEN3462F6MKN2H4EW3N.png	\N	2025-09-16 22:20:27	2025-09-16 22:20:27
6	2	Quvonchbek Abdusobirov	quvonchabdusobirov@gmail.com	1	\N	$2y$12$cwjpJSlwkq0zc1kmbQR0ZuGcqMPEHyX2GVekRviXCt37/yMCav3.u	\N	\N	2025-09-16 22:26:50	2025-09-16 22:26:50
4	2	Yolqin Muqimov	yolqinmuqimov@gmail.com	1	\N	$2y$12$mfHeHK6Bzh16PsQ12RjyPuWATwAL4C6V2pFkLfqlEqa/nXNJr6E9O	\N	\N	2025-09-16 22:22:04	2025-09-16 22:29:14
5	2	Norbek Hamidov	ozod@gmail.com	1	\N	$2y$12$yYJlwJUtQSY1NIeu30bItOAjQTjrsT7zT707l6IW/9CocarDrdFni	\N	\N	2025-09-16 22:22:55	2025-09-17 13:00:36
10	2	Gadoyeva Nafosat	nafosat@gmail.com	1	\N	$2y$12$/moSdlKTKjY8n00G7354VeEqdAY1/Gaw64Yv4irxArJC.VoPg6edK	\N	\N	2025-09-17 13:02:26	2025-09-17 13:03:13
11	2	Hojiyev Adizbek	adizbek@gmail.com	1	\N	$2y$12$X2sWnO8B5XCclxgn/Ua9h.rX/OL9Q7UVzwCD9Fq/4qT4hfOVc0bZm	\N	\N	2025-09-17 15:02:41	2025-09-17 15:03:13
12	2	Ortiqova Gulchexra	gulchexra@gmail.com	1	\N	$2y$12$NnYGsjHa9MvrMr4S04e.rOvuqWDo.qY3ZYCncmPtRhOvbb6xY07Di	\N	\N	2025-09-17 21:47:18	2025-09-17 21:47:18
13	2	Hotamov Husniddin	husniddin@gmail.com	1	\N	$2y$12$SzAJ50DA8eIWhLUm/cLSou7blpQaSoJn9373p8KPBPOGd8z4T4Cy.	\N	\N	2025-09-17 22:13:58	2025-09-17 22:13:58
1	1	Admin	admin@example.com	3	\N	$2y$12$I.wNAaqv.rGlOtLWmoVQ0OxwxOdbZnN3wLI5qJHzlQ6dRs3kx7nBe	signatures/01K5NW2ACKQ4TDHDPDD57YXBC3.png	\N	2025-09-16 19:55:31	2025-09-21 15:10:45
14	3	QUVONOV SHAXZODBEK SHOKIR O‘G‘LI	quvonov@gmail.com	2	\N	$2y$12$vxHAYu7fHaWEM0s5omxxaeHqzH9WFRun7Q5C0iGAs0qXPAJxXAxn2	\N	\N	2025-09-21 21:41:06	2025-09-21 21:41:06
15	3	Davronov Oybek Jamolovich	commo1318@gmail.com	1	\N	$2y$12$ZEEkthbqMD1aV5h7RaXWe.bYHvCvadhBf/W5QqAE3Q1C2sAuD8BCq	\N	\N	2025-09-21 21:44:10	2025-09-21 21:46:37
\.


--
-- Name: districts_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ozodbek
--

SELECT pg_catalog.setval('public.districts_id_seq', 183, true);


--
-- Name: exams_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ozodbek
--

SELECT pg_catalog.setval('public.exams_id_seq', 2, true);


--
-- Name: exports_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ozodbek
--

SELECT pg_catalog.setval('public.exports_id_seq', 1, false);


--
-- Name: failed_import_rows_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ozodbek
--

SELECT pg_catalog.setval('public.failed_import_rows_id_seq', 1, false);


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ozodbek
--

SELECT pg_catalog.setval('public.failed_jobs_id_seq', 1, false);


--
-- Name: imports_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ozodbek
--

SELECT pg_catalog.setval('public.imports_id_seq', 1, false);


--
-- Name: jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ozodbek
--

SELECT pg_catalog.setval('public.jobs_id_seq', 23, true);


--
-- Name: maktabs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ozodbek
--

SELECT pg_catalog.setval('public.maktabs_id_seq', 3, true);


--
-- Name: marks_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ozodbek
--

SELECT pg_catalog.setval('public.marks_id_seq', 20, true);


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ozodbek
--

SELECT pg_catalog.setval('public.migrations_id_seq', 19, true);


--
-- Name: regions_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ozodbek
--

SELECT pg_catalog.setval('public.regions_id_seq', 14, true);


--
-- Name: roles_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ozodbek
--

SELECT pg_catalog.setval('public.roles_id_seq', 3, true);


--
-- Name: sinfs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ozodbek
--

SELECT pg_catalog.setval('public.sinfs_id_seq', 3, true);


--
-- Name: students_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ozodbek
--

SELECT pg_catalog.setval('public.students_id_seq', 8, true);


--
-- Name: subjects_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ozodbek
--

SELECT pg_catalog.setval('public.subjects_id_seq', 9, true);


--
-- Name: teacher_subject_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ozodbek
--

SELECT pg_catalog.setval('public.teacher_subject_id_seq', 8, true);


--
-- Name: teachers_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ozodbek
--

SELECT pg_catalog.setval('public.teachers_id_seq', 8, true);


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ozodbek
--

SELECT pg_catalog.setval('public.users_id_seq', 15, true);


--
-- Name: cache_locks cache_locks_pkey; Type: CONSTRAINT; Schema: public; Owner: ozodbek
--

ALTER TABLE ONLY public.cache_locks
    ADD CONSTRAINT cache_locks_pkey PRIMARY KEY (key);


--
-- Name: cache cache_pkey; Type: CONSTRAINT; Schema: public; Owner: ozodbek
--

ALTER TABLE ONLY public.cache
    ADD CONSTRAINT cache_pkey PRIMARY KEY (key);


--
-- Name: districts districts_name_unique; Type: CONSTRAINT; Schema: public; Owner: ozodbek
--

ALTER TABLE ONLY public.districts
    ADD CONSTRAINT districts_name_unique UNIQUE (name);


--
-- Name: districts districts_pkey; Type: CONSTRAINT; Schema: public; Owner: ozodbek
--

ALTER TABLE ONLY public.districts
    ADD CONSTRAINT districts_pkey PRIMARY KEY (id);


--
-- Name: exams exams_pkey; Type: CONSTRAINT; Schema: public; Owner: ozodbek
--

ALTER TABLE ONLY public.exams
    ADD CONSTRAINT exams_pkey PRIMARY KEY (id);


--
-- Name: exports exports_pkey; Type: CONSTRAINT; Schema: public; Owner: ozodbek
--

ALTER TABLE ONLY public.exports
    ADD CONSTRAINT exports_pkey PRIMARY KEY (id);


--
-- Name: failed_import_rows failed_import_rows_pkey; Type: CONSTRAINT; Schema: public; Owner: ozodbek
--

ALTER TABLE ONLY public.failed_import_rows
    ADD CONSTRAINT failed_import_rows_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: ozodbek
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: ozodbek
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_uuid_unique UNIQUE (uuid);


--
-- Name: imports imports_pkey; Type: CONSTRAINT; Schema: public; Owner: ozodbek
--

ALTER TABLE ONLY public.imports
    ADD CONSTRAINT imports_pkey PRIMARY KEY (id);


--
-- Name: job_batches job_batches_pkey; Type: CONSTRAINT; Schema: public; Owner: ozodbek
--

ALTER TABLE ONLY public.job_batches
    ADD CONSTRAINT job_batches_pkey PRIMARY KEY (id);


--
-- Name: jobs jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: ozodbek
--

ALTER TABLE ONLY public.jobs
    ADD CONSTRAINT jobs_pkey PRIMARY KEY (id);


--
-- Name: maktabs maktabs_pkey; Type: CONSTRAINT; Schema: public; Owner: ozodbek
--

ALTER TABLE ONLY public.maktabs
    ADD CONSTRAINT maktabs_pkey PRIMARY KEY (id);


--
-- Name: marks marks_pkey; Type: CONSTRAINT; Schema: public; Owner: ozodbek
--

ALTER TABLE ONLY public.marks
    ADD CONSTRAINT marks_pkey PRIMARY KEY (id);


--
-- Name: marks marks_student_id_exam_id_problem_id_unique; Type: CONSTRAINT; Schema: public; Owner: ozodbek
--

ALTER TABLE ONLY public.marks
    ADD CONSTRAINT marks_student_id_exam_id_problem_id_unique UNIQUE (student_id, exam_id, problem_id);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: ozodbek
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: notifications notifications_pkey; Type: CONSTRAINT; Schema: public; Owner: ozodbek
--

ALTER TABLE ONLY public.notifications
    ADD CONSTRAINT notifications_pkey PRIMARY KEY (id);


--
-- Name: password_reset_tokens password_reset_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: ozodbek
--

ALTER TABLE ONLY public.password_reset_tokens
    ADD CONSTRAINT password_reset_tokens_pkey PRIMARY KEY (email);


--
-- Name: regions regions_name_unique; Type: CONSTRAINT; Schema: public; Owner: ozodbek
--

ALTER TABLE ONLY public.regions
    ADD CONSTRAINT regions_name_unique UNIQUE (name);


--
-- Name: regions regions_pkey; Type: CONSTRAINT; Schema: public; Owner: ozodbek
--

ALTER TABLE ONLY public.regions
    ADD CONSTRAINT regions_pkey PRIMARY KEY (id);


--
-- Name: roles roles_pkey; Type: CONSTRAINT; Schema: public; Owner: ozodbek
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_pkey PRIMARY KEY (id);


--
-- Name: sessions sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: ozodbek
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT sessions_pkey PRIMARY KEY (id);


--
-- Name: sinfs sinfs_pkey; Type: CONSTRAINT; Schema: public; Owner: ozodbek
--

ALTER TABLE ONLY public.sinfs
    ADD CONSTRAINT sinfs_pkey PRIMARY KEY (id);


--
-- Name: students students_pkey; Type: CONSTRAINT; Schema: public; Owner: ozodbek
--

ALTER TABLE ONLY public.students
    ADD CONSTRAINT students_pkey PRIMARY KEY (id);


--
-- Name: subjects subjects_pkey; Type: CONSTRAINT; Schema: public; Owner: ozodbek
--

ALTER TABLE ONLY public.subjects
    ADD CONSTRAINT subjects_pkey PRIMARY KEY (id);


--
-- Name: teacher_subject teacher_subject_pkey; Type: CONSTRAINT; Schema: public; Owner: ozodbek
--

ALTER TABLE ONLY public.teacher_subject
    ADD CONSTRAINT teacher_subject_pkey PRIMARY KEY (id);


--
-- Name: teachers teachers_pkey; Type: CONSTRAINT; Schema: public; Owner: ozodbek
--

ALTER TABLE ONLY public.teachers
    ADD CONSTRAINT teachers_pkey PRIMARY KEY (id);


--
-- Name: users users_email_unique; Type: CONSTRAINT; Schema: public; Owner: ozodbek
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_unique UNIQUE (email);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: ozodbek
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: jobs_queue_index; Type: INDEX; Schema: public; Owner: ozodbek
--

CREATE INDEX jobs_queue_index ON public.jobs USING btree (queue);


--
-- Name: marks_exam_id_student_id_index; Type: INDEX; Schema: public; Owner: ozodbek
--

CREATE INDEX marks_exam_id_student_id_index ON public.marks USING btree (exam_id, student_id);


--
-- Name: marks_maktab_id_exam_id_index; Type: INDEX; Schema: public; Owner: ozodbek
--

CREATE INDEX marks_maktab_id_exam_id_index ON public.marks USING btree (maktab_id, exam_id);


--
-- Name: marks_sinf_id_exam_id_index; Type: INDEX; Schema: public; Owner: ozodbek
--

CREATE INDEX marks_sinf_id_exam_id_index ON public.marks USING btree (sinf_id, exam_id);


--
-- Name: notifications_notifiable_type_notifiable_id_index; Type: INDEX; Schema: public; Owner: ozodbek
--

CREATE INDEX notifications_notifiable_type_notifiable_id_index ON public.notifications USING btree (notifiable_type, notifiable_id);


--
-- Name: sessions_last_activity_index; Type: INDEX; Schema: public; Owner: ozodbek
--

CREATE INDEX sessions_last_activity_index ON public.sessions USING btree (last_activity);


--
-- Name: sessions_user_id_index; Type: INDEX; Schema: public; Owner: ozodbek
--

CREATE INDEX sessions_user_id_index ON public.sessions USING btree (user_id);


--
-- Name: exams exams_metod_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ozodbek
--

ALTER TABLE ONLY public.exams
    ADD CONSTRAINT exams_metod_id_foreign FOREIGN KEY (metod_id) REFERENCES public.teachers(id) ON DELETE CASCADE;


--
-- Name: exams exams_sinf_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ozodbek
--

ALTER TABLE ONLY public.exams
    ADD CONSTRAINT exams_sinf_id_foreign FOREIGN KEY (sinf_id) REFERENCES public.sinfs(id) ON DELETE CASCADE;


--
-- Name: exams exams_subject_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ozodbek
--

ALTER TABLE ONLY public.exams
    ADD CONSTRAINT exams_subject_id_foreign FOREIGN KEY (subject_id) REFERENCES public.subjects(id) ON DELETE CASCADE;


--
-- Name: exams exams_teacher_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ozodbek
--

ALTER TABLE ONLY public.exams
    ADD CONSTRAINT exams_teacher_id_foreign FOREIGN KEY (teacher_id) REFERENCES public.teachers(id) ON DELETE CASCADE;


--
-- Name: exports exports_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ozodbek
--

ALTER TABLE ONLY public.exports
    ADD CONSTRAINT exports_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: failed_import_rows failed_import_rows_import_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ozodbek
--

ALTER TABLE ONLY public.failed_import_rows
    ADD CONSTRAINT failed_import_rows_import_id_foreign FOREIGN KEY (import_id) REFERENCES public.imports(id) ON DELETE CASCADE;


--
-- Name: imports imports_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ozodbek
--

ALTER TABLE ONLY public.imports
    ADD CONSTRAINT imports_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: maktabs maktabs_district_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ozodbek
--

ALTER TABLE ONLY public.maktabs
    ADD CONSTRAINT maktabs_district_id_foreign FOREIGN KEY (district_id) REFERENCES public.districts(id);


--
-- Name: maktabs maktabs_region_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ozodbek
--

ALTER TABLE ONLY public.maktabs
    ADD CONSTRAINT maktabs_region_id_foreign FOREIGN KEY (region_id) REFERENCES public.regions(id);


--
-- Name: marks marks_exam_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ozodbek
--

ALTER TABLE ONLY public.marks
    ADD CONSTRAINT marks_exam_id_foreign FOREIGN KEY (exam_id) REFERENCES public.exams(id) ON DELETE CASCADE;


--
-- Name: marks marks_maktab_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ozodbek
--

ALTER TABLE ONLY public.marks
    ADD CONSTRAINT marks_maktab_id_foreign FOREIGN KEY (maktab_id) REFERENCES public.maktabs(id) ON DELETE CASCADE;


--
-- Name: marks marks_sinf_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ozodbek
--

ALTER TABLE ONLY public.marks
    ADD CONSTRAINT marks_sinf_id_foreign FOREIGN KEY (sinf_id) REFERENCES public.sinfs(id) ON DELETE CASCADE;


--
-- Name: marks marks_student_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ozodbek
--

ALTER TABLE ONLY public.marks
    ADD CONSTRAINT marks_student_id_foreign FOREIGN KEY (student_id) REFERENCES public.students(id) ON DELETE CASCADE;


--
-- Name: teacher_subject teacher_subject_subject_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ozodbek
--

ALTER TABLE ONLY public.teacher_subject
    ADD CONSTRAINT teacher_subject_subject_id_foreign FOREIGN KEY (subject_id) REFERENCES public.subjects(id) ON DELETE CASCADE;


--
-- Name: teacher_subject teacher_subject_teacher_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ozodbek
--

ALTER TABLE ONLY public.teacher_subject
    ADD CONSTRAINT teacher_subject_teacher_id_foreign FOREIGN KEY (teacher_id) REFERENCES public.teachers(id) ON DELETE CASCADE;


--
-- Name: teachers teachers_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ozodbek
--

ALTER TABLE ONLY public.teachers
    ADD CONSTRAINT teachers_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id);


--
-- Name: users users_maktab_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ozodbek
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_maktab_id_foreign FOREIGN KEY (maktab_id) REFERENCES public.maktabs(id);


--
-- Name: users users_role_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ozodbek
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_role_id_foreign FOREIGN KEY (role_id) REFERENCES public.roles(id);


--
-- PostgreSQL database dump complete
--

