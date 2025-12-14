--
-- PostgreSQL database dump
--

-- Dumped from database version 17.2
-- Dumped by pg_dump version 17.2

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
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
-- Name: attachements; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.attachements (
    id bigint NOT NULL,
    attachable_type character varying(255) NOT NULL,
    attachable_id bigint NOT NULL,
    uploaded_by bigint NOT NULL,
    file_type character varying(255) NOT NULL,
    file_name character varying(255) NOT NULL,
    file_path character varying(255),
    url character varying(255),
    format character varying(255) DEFAULT 'file'::character varying NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT attachements_file_type_check CHECK (((file_type)::text = ANY ((ARRAY['attachment'::character varying, 'deliverable'::character varying])::text[]))),
    CONSTRAINT attachements_format_check CHECK (((format)::text = ANY ((ARRAY['file'::character varying, 'link'::character varying])::text[])))
);


ALTER TABLE public.attachements OWNER TO postgres;

--
-- Name: attachements_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.attachements_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.attachements_id_seq OWNER TO postgres;

--
-- Name: attachements_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.attachements_id_seq OWNED BY public.attachements.id;


--
-- Name: cache; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.cache (
    key character varying(255) NOT NULL,
    value text NOT NULL,
    expiration integer NOT NULL
);


ALTER TABLE public.cache OWNER TO postgres;

--
-- Name: cache_locks; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.cache_locks (
    key character varying(255) NOT NULL,
    owner character varying(255) NOT NULL,
    expiration integer NOT NULL
);


ALTER TABLE public.cache_locks OWNER TO postgres;

--
-- Name: categories; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.categories (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.categories OWNER TO postgres;

--
-- Name: categories_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.categories_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.categories_id_seq OWNER TO postgres;

--
-- Name: categories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.categories_id_seq OWNED BY public.categories.id;


--
-- Name: clients; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.clients (
    id bigint NOT NULL,
    user_id bigint NOT NULL,
    company_name character varying(255),
    company_description text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.clients OWNER TO postgres;

--
-- Name: clients_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.clients_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.clients_id_seq OWNER TO postgres;

--
-- Name: clients_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.clients_id_seq OWNED BY public.clients.id;


--
-- Name: contracts; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.contracts (
    id bigint NOT NULL,
    project_id bigint NOT NULL,
    proposal_id bigint NOT NULL,
    client_id bigint NOT NULL,
    freelance_id bigint NOT NULL,
    amount numeric(10,2) NOT NULL,
    duration integer NOT NULL,
    start_date date NOT NULL,
    end_date date,
    terms text,
    status character varying(255) DEFAULT 'active'::character varying NOT NULL,
    signed_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT contracts_status_check CHECK (((status)::text = ANY ((ARRAY['active'::character varying, 'completed'::character varying, 'cancelled'::character varying, 'disputed'::character varying])::text[])))
);


ALTER TABLE public.contracts OWNER TO postgres;

--
-- Name: contracts_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.contracts_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.contracts_id_seq OWNER TO postgres;

--
-- Name: contracts_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.contracts_id_seq OWNED BY public.contracts.id;


--
-- Name: conversations; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.conversations (
    id bigint NOT NULL,
    client_id bigint NOT NULL,
    freelance_id bigint NOT NULL,
    status character varying(255) DEFAULT 'active'::character varying NOT NULL,
    last_message_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT conversations_status_check CHECK (((status)::text = ANY ((ARRAY['active'::character varying, 'archived'::character varying])::text[])))
);


ALTER TABLE public.conversations OWNER TO postgres;

--
-- Name: conversations_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.conversations_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.conversations_id_seq OWNER TO postgres;

--
-- Name: conversations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.conversations_id_seq OWNED BY public.conversations.id;


--
-- Name: device_tokens; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.device_tokens (
    id bigint NOT NULL,
    user_id bigint NOT NULL,
    token character varying(255) NOT NULL,
    device_type character varying(255) DEFAULT 'web'::character varying NOT NULL,
    device_name character varying(100),
    is_active boolean DEFAULT true NOT NULL,
    last_used_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT device_tokens_device_type_check CHECK (((device_type)::text = ANY ((ARRAY['web'::character varying, 'android'::character varying, 'ios'::character varying])::text[])))
);


ALTER TABLE public.device_tokens OWNER TO postgres;

--
-- Name: device_tokens_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.device_tokens_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.device_tokens_id_seq OWNER TO postgres;

--
-- Name: device_tokens_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.device_tokens_id_seq OWNED BY public.device_tokens.id;


--
-- Name: failed_jobs; Type: TABLE; Schema: public; Owner: postgres
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


ALTER TABLE public.failed_jobs OWNER TO postgres;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.failed_jobs_id_seq OWNER TO postgres;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.failed_jobs_id_seq OWNED BY public.failed_jobs.id;


--
-- Name: freelance_skills; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.freelance_skills (
    id bigint NOT NULL,
    freelance_id bigint NOT NULL,
    skill_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.freelance_skills OWNER TO postgres;

--
-- Name: freelance_skills_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.freelance_skills_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.freelance_skills_id_seq OWNER TO postgres;

--
-- Name: freelance_skills_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.freelance_skills_id_seq OWNED BY public.freelance_skills.id;


--
-- Name: freelances; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.freelances (
    id bigint NOT NULL,
    user_id bigint NOT NULL,
    title character varying(255) NOT NULL,
    description text,
    hourly_rate numeric(10,2),
    experience_years integer,
    availability character varying(255) DEFAULT 'available'::character varying NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT freelances_availability_check CHECK (((availability)::text = ANY ((ARRAY['available'::character varying, 'busy'::character varying, 'unavailable'::character varying])::text[])))
);


ALTER TABLE public.freelances OWNER TO postgres;

--
-- Name: freelances_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.freelances_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.freelances_id_seq OWNER TO postgres;

--
-- Name: freelances_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.freelances_id_seq OWNED BY public.freelances.id;


--
-- Name: jobs; Type: TABLE; Schema: public; Owner: postgres
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


ALTER TABLE public.jobs OWNER TO postgres;

--
-- Name: jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.jobs_id_seq OWNER TO postgres;

--
-- Name: jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.jobs_id_seq OWNED BY public.jobs.id;


--
-- Name: messages; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.messages (
    id bigint NOT NULL,
    conversation_id bigint NOT NULL,
    sender_id bigint NOT NULL,
    message_type character varying(255) DEFAULT 'text'::character varying NOT NULL,
    content text,
    is_read boolean DEFAULT false NOT NULL,
    read_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT messages_message_type_check CHECK (((message_type)::text = ANY ((ARRAY['text'::character varying, 'voice'::character varying, 'file'::character varying])::text[])))
);


ALTER TABLE public.messages OWNER TO postgres;

--
-- Name: messages_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.messages_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.messages_id_seq OWNER TO postgres;

--
-- Name: messages_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.messages_id_seq OWNED BY public.messages.id;


--
-- Name: migrations; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


ALTER TABLE public.migrations OWNER TO postgres;

--
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.migrations_id_seq OWNER TO postgres;

--
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- Name: orders; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.orders (
    id bigint NOT NULL,
    client_id bigint NOT NULL,
    amount numeric(10,2) NOT NULL,
    due_date date,
    delivered_at date,
    requirements text,
    status character varying(255) DEFAULT 'pending'::character varying NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    service_offer_id bigint NOT NULL,
    CONSTRAINT orders_status_check CHECK (((status)::text = ANY ((ARRAY['pending'::character varying, 'in_progress'::character varying, 'delivered'::character varying, 'completed'::character varying, 'cancelled'::character varying, 'revision'::character varying])::text[])))
);


ALTER TABLE public.orders OWNER TO postgres;

--
-- Name: orders_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.orders_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.orders_id_seq OWNER TO postgres;

--
-- Name: orders_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.orders_id_seq OWNED BY public.orders.id;


--
-- Name: password_reset_tokens; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.password_reset_tokens (
    email character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    created_at timestamp(0) without time zone
);


ALTER TABLE public.password_reset_tokens OWNER TO postgres;

--
-- Name: personal_access_tokens; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.personal_access_tokens (
    id bigint NOT NULL,
    tokenable_type character varying(255) NOT NULL,
    tokenable_id bigint NOT NULL,
    name character varying(255) NOT NULL,
    token character varying(64) NOT NULL,
    abilities text,
    last_used_at timestamp(0) without time zone,
    expires_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.personal_access_tokens OWNER TO postgres;

--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.personal_access_tokens_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.personal_access_tokens_id_seq OWNER TO postgres;

--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.personal_access_tokens_id_seq OWNED BY public.personal_access_tokens.id;


--
-- Name: project_skills; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.project_skills (
    id bigint NOT NULL,
    project_id bigint NOT NULL,
    skill_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.project_skills OWNER TO postgres;

--
-- Name: project_skills_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.project_skills_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.project_skills_id_seq OWNER TO postgres;

--
-- Name: project_skills_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.project_skills_id_seq OWNED BY public.project_skills.id;


--
-- Name: project_tasks; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.project_tasks (
    id bigint NOT NULL,
    project_id bigint NOT NULL,
    title character varying(255) NOT NULL,
    description text,
    "order" integer DEFAULT 0 NOT NULL,
    status character varying(255) DEFAULT 'pending'::character varying NOT NULL,
    priority character varying(255) DEFAULT 'moyenne'::character varying NOT NULL,
    completed_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT project_tasks_priority_check CHECK (((priority)::text = ANY ((ARRAY['basse'::character varying, 'moyenne'::character varying, 'haute'::character varying, 'urgente'::character varying])::text[]))),
    CONSTRAINT project_tasks_status_check CHECK (((status)::text = ANY ((ARRAY['pending'::character varying, 'in_progress'::character varying, 'completed'::character varying])::text[])))
);


ALTER TABLE public.project_tasks OWNER TO postgres;

--
-- Name: project_tasks_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.project_tasks_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.project_tasks_id_seq OWNER TO postgres;

--
-- Name: project_tasks_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.project_tasks_id_seq OWNED BY public.project_tasks.id;


--
-- Name: projects; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.projects (
    id bigint NOT NULL,
    client_id bigint NOT NULL,
    title character varying(255) NOT NULL,
    description text NOT NULL,
    budget numeric(10,2),
    deadline date,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    category_id bigint,
    duration integer,
    start_date date,
    progress integer DEFAULT 0 NOT NULL,
    status character varying(255) DEFAULT 'en_attente'::character varying NOT NULL,
    CONSTRAINT projects_status_check CHECK (((status)::text = ANY ((ARRAY['en_attente'::character varying, 'open'::character varying, 'in_progress'::character varying, 'completed'::character varying, 'cancelled'::character varying, 'archived'::character varying])::text[])))
);


ALTER TABLE public.projects OWNER TO postgres;

--
-- Name: projects_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.projects_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.projects_id_seq OWNER TO postgres;

--
-- Name: projects_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.projects_id_seq OWNED BY public.projects.id;


--
-- Name: proposals; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.proposals (
    id bigint NOT NULL,
    project_id bigint NOT NULL,
    freelance_id bigint NOT NULL,
    cover_letter text NOT NULL,
    proposed_amount numeric(10,2) NOT NULL,
    proposed_duration integer NOT NULL,
    status character varying(255) DEFAULT 'pending'::character varying NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT proposals_status_check CHECK (((status)::text = ANY ((ARRAY['pending'::character varying, 'accepted'::character varying, 'rejected'::character varying])::text[])))
);


ALTER TABLE public.proposals OWNER TO postgres;

--
-- Name: proposals_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.proposals_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.proposals_id_seq OWNER TO postgres;

--
-- Name: proposals_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.proposals_id_seq OWNED BY public.proposals.id;


--
-- Name: service_images; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.service_images (
    id bigint NOT NULL,
    service_id bigint NOT NULL,
    image_path character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.service_images OWNER TO postgres;

--
-- Name: service_images_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.service_images_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.service_images_id_seq OWNER TO postgres;

--
-- Name: service_images_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.service_images_id_seq OWNED BY public.service_images.id;


--
-- Name: service_offers; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.service_offers (
    id bigint NOT NULL,
    service_id bigint NOT NULL,
    title character varying(255) NOT NULL,
    delivery_days integer NOT NULL,
    number_of_revisions integer,
    price numeric(10,2) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT service_offers_title_check CHECK (((title)::text = ANY ((ARRAY['Starter'::character varying, 'Standard'::character varying, 'Advanced'::character varying])::text[])))
);


ALTER TABLE public.service_offers OWNER TO postgres;

--
-- Name: service_offers_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.service_offers_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.service_offers_id_seq OWNER TO postgres;

--
-- Name: service_offers_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.service_offers_id_seq OWNED BY public.service_offers.id;


--
-- Name: services; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.services (
    id bigint NOT NULL,
    freelance_id bigint NOT NULL,
    title character varying(255) NOT NULL,
    description text NOT NULL,
    categorie_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    status character varying(255) DEFAULT 'en_attente'::character varying NOT NULL,
    CONSTRAINT services_status_check CHECK (((status)::text = ANY ((ARRAY['en_attente'::character varying, 'published'::character varying, 'archived'::character varying, 'rejected'::character varying])::text[])))
);


ALTER TABLE public.services OWNER TO postgres;

--
-- Name: services_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.services_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.services_id_seq OWNER TO postgres;

--
-- Name: services_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.services_id_seq OWNED BY public.services.id;


--
-- Name: sessions; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.sessions (
    id character varying(255) NOT NULL,
    user_id bigint,
    ip_address character varying(45),
    user_agent text,
    payload text NOT NULL,
    last_activity integer NOT NULL
);


ALTER TABLE public.sessions OWNER TO postgres;

--
-- Name: skills; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.skills (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.skills OWNER TO postgres;

--
-- Name: skills_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.skills_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.skills_id_seq OWNER TO postgres;

--
-- Name: skills_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.skills_id_seq OWNED BY public.skills.id;


--
-- Name: users; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.users (
    id bigint NOT NULL,
    full_name character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    email_verified_at timestamp(0) without time zone,
    password character varying(255) NOT NULL,
    phone character varying(255),
    avatar character varying(255),
    city character varying(255),
    country character varying(255),
    user_type character varying(255) NOT NULL,
    status character varying(255) DEFAULT 'active'::character varying NOT NULL,
    remember_token character varying(100),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    email_verification_token character varying(255),
    email_verification_token_expires_at timestamp(0) without time zone,
    CONSTRAINT users_status_check CHECK (((status)::text = ANY ((ARRAY['active'::character varying, 'suspended'::character varying, 'inactive'::character varying])::text[]))),
    CONSTRAINT users_user_type_check CHECK (((user_type)::text = ANY ((ARRAY['admin'::character varying, 'client'::character varying, 'freelance'::character varying])::text[])))
);


ALTER TABLE public.users OWNER TO postgres;

--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.users_id_seq OWNER TO postgres;

--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- Name: websockets_statistics_entries; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.websockets_statistics_entries (
    id integer NOT NULL,
    app_id character varying(255) NOT NULL,
    peak_connection_count integer NOT NULL,
    websocket_message_count integer NOT NULL,
    api_message_count integer NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.websockets_statistics_entries OWNER TO postgres;

--
-- Name: websockets_statistics_entries_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.websockets_statistics_entries_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.websockets_statistics_entries_id_seq OWNER TO postgres;

--
-- Name: websockets_statistics_entries_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.websockets_statistics_entries_id_seq OWNED BY public.websockets_statistics_entries.id;


--
-- Name: attachements id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.attachements ALTER COLUMN id SET DEFAULT nextval('public.attachements_id_seq'::regclass);


--
-- Name: categories id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.categories ALTER COLUMN id SET DEFAULT nextval('public.categories_id_seq'::regclass);


--
-- Name: clients id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.clients ALTER COLUMN id SET DEFAULT nextval('public.clients_id_seq'::regclass);


--
-- Name: contracts id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.contracts ALTER COLUMN id SET DEFAULT nextval('public.contracts_id_seq'::regclass);


--
-- Name: conversations id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.conversations ALTER COLUMN id SET DEFAULT nextval('public.conversations_id_seq'::regclass);


--
-- Name: device_tokens id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.device_tokens ALTER COLUMN id SET DEFAULT nextval('public.device_tokens_id_seq'::regclass);


--
-- Name: failed_jobs id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.failed_jobs ALTER COLUMN id SET DEFAULT nextval('public.failed_jobs_id_seq'::regclass);


--
-- Name: freelance_skills id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.freelance_skills ALTER COLUMN id SET DEFAULT nextval('public.freelance_skills_id_seq'::regclass);


--
-- Name: freelances id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.freelances ALTER COLUMN id SET DEFAULT nextval('public.freelances_id_seq'::regclass);


--
-- Name: jobs id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jobs ALTER COLUMN id SET DEFAULT nextval('public.jobs_id_seq'::regclass);


--
-- Name: messages id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.messages ALTER COLUMN id SET DEFAULT nextval('public.messages_id_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- Name: orders id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.orders ALTER COLUMN id SET DEFAULT nextval('public.orders_id_seq'::regclass);


--
-- Name: personal_access_tokens id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.personal_access_tokens ALTER COLUMN id SET DEFAULT nextval('public.personal_access_tokens_id_seq'::regclass);


--
-- Name: project_skills id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.project_skills ALTER COLUMN id SET DEFAULT nextval('public.project_skills_id_seq'::regclass);


--
-- Name: project_tasks id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.project_tasks ALTER COLUMN id SET DEFAULT nextval('public.project_tasks_id_seq'::regclass);


--
-- Name: projects id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.projects ALTER COLUMN id SET DEFAULT nextval('public.projects_id_seq'::regclass);


--
-- Name: proposals id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.proposals ALTER COLUMN id SET DEFAULT nextval('public.proposals_id_seq'::regclass);


--
-- Name: service_images id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.service_images ALTER COLUMN id SET DEFAULT nextval('public.service_images_id_seq'::regclass);


--
-- Name: service_offers id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.service_offers ALTER COLUMN id SET DEFAULT nextval('public.service_offers_id_seq'::regclass);


--
-- Name: services id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.services ALTER COLUMN id SET DEFAULT nextval('public.services_id_seq'::regclass);


--
-- Name: skills id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.skills ALTER COLUMN id SET DEFAULT nextval('public.skills_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- Name: websockets_statistics_entries id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.websockets_statistics_entries ALTER COLUMN id SET DEFAULT nextval('public.websockets_statistics_entries_id_seq'::regclass);


--
-- Data for Name: attachements; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.attachements (id, attachable_type, attachable_id, uploaded_by, file_type, file_name, file_path, url, format, created_at, updated_at) FROM stdin;
1	App\\Models\\Project	1	3	attachment	Exemple-fichier-PDF-1.pdf	attachments/rlnHUhYFpEkQ6JPEyJSqnsfMOlcnYmjSMOoVqBC5.pdf	\N	file	2025-10-10 16:00:22	2025-10-10 16:00:22
2	App\\Models\\Project	1	3	attachment	exemple-1.pdf	attachments/1z5WxJvuvI8vtFqIZ4vZiDuYAhFxNbNueyEwyMmx.pdf	\N	file	2025-10-10 16:00:22	2025-10-10 16:00:22
3	App\\Models\\Project	1	5	deliverable	zp1v56uxy8rdx5ypatb0ockcb9tr6a-oci3--4321--96435430.local-credentialless.webcontainer-api.io_about (1).png	deliverables/wt2DCjHBAlxUIwX0WlQznMn1eTDarhbI9ILerTk0.png	\N	file	2025-10-10 23:57:03	2025-10-10 23:57:03
4	App\\Models\\Project	1	5	deliverable	Contrat_8_Locataire0_test.pdf	deliverables/ftaoyeJFvfeqK5eypbZDRIpiA59B1Ir2HWqjVqDA.pdf	\N	file	2025-10-10 23:57:03	2025-10-10 23:57:03
5	App\\Models\\Project	1	5	deliverable	Site en ligne (version de démo)	\N	https://demo-ecommerce-client.herokuapp.com	link	2025-10-11 00:01:25	2025-10-11 00:01:25
6	App\\Models\\Order	1	9	attachment	Capture d'écran 2025-05-16 165557.png	orders/attachments/4Y1RCyF051hjVqffjPAoUehxhrehER7TxFDy9TLF.png	\N	file	2025-10-11 14:43:30	2025-10-11 14:43:30
7	App\\Models\\Order	2	9	attachment	Capture d'écran 2025-05-16 165557.png	orders/attachments/JdG2qT51790zl42YAwAg9wue4ucCM31RSruqMKBt.png	\N	file	2025-10-11 14:46:27	2025-10-11 14:46:27
8	App\\Models\\Order	1	8	deliverable	Capture d'écran 2025-05-16 165201.png	deliverables/J4VjQb39k5NzRVHMg7ptCi4NclL0ZTeaN18UHM2v.png	\N	file	2025-10-11 16:40:18	2025-10-11 16:40:18
9	App\\Models\\Project	4	4	deliverable	exemple-1.pdf	deliverables/T5VGUUJ3DG1Az7vkgeSkmkXT39VENv5atdglMzls.pdf	\N	file	2025-10-27 01:28:43	2025-10-27 01:28:43
10	App\\Models\\Project	6	3	attachment	Capture d'écran 2025-07-23 140557.png	projects/attachments/CS9aDJHSEgqGH4eHzFx1xExBuxaZG1dWnlvBszCN.png	\N	file	2025-11-05 02:34:58	2025-11-05 02:34:58
11	App\\Models\\Project	7	9	attachment	exemple-1.pdf	projects/attachments/c4LfuB1WCUs6lUC8M9vaYtUtP9y8JHJS9v9d4jjy.pdf	\N	file	2025-11-15 01:43:23	2025-11-15 01:43:23
12	App\\Models\\Project	8	11	attachment	Exemple-fichier-PDF-1.pdf	projects/attachments/jhtiR8fcPP4ZljIe2jQwKyGViGpuDFQJU80oYbSg.pdf	\N	file	2025-11-19 13:56:19	2025-11-19 13:56:19
13	App\\Models\\Order	5	11	attachment	Capture d'écran 2025-06-24 211900.png	orders/attachments/ZYeubDGErVnf1SLttTI0EDAJSNuEkpH44zJMiPDF.png	\N	file	2025-11-28 02:36:11	2025-11-28 02:36:11
14	App\\Models\\Project	9	18	attachment	exemple-1.pdf	projects/attachments/R67j1lPTYm86FzONaJ40ZkbnzRqzwSdDsCQ8MCVk.pdf	\N	file	2025-12-12 12:56:59	2025-12-12 12:56:59
15	App\\Models\\Project	8	15	deliverable	Exemple-fichier-PDF-1.pdf	deliverables/RtgKbCmh0tp4rr29MobjEUAdhmRQpUsQ1zY3wq8K.pdf	\N	file	2025-12-12 19:59:50	2025-12-12 19:59:50
16	App\\Models\\Order	5	15	deliverable	exemple-1.pdf	deliverables/HMfFgJmG2y3KZnJuTjy4puTIXxrheD4KNvqMaGDo.pdf	\N	file	2025-12-12 20:16:55	2025-12-12 20:16:55
\.


--
-- Data for Name: cache; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.cache (key, value, expiration) FROM stdin;
\.


--
-- Data for Name: cache_locks; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.cache_locks (key, owner, expiration) FROM stdin;
\.


--
-- Data for Name: categories; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.categories (id, name, created_at, updated_at) FROM stdin;
3	Marketing Digital	2025-10-08 16:16:42	2025-10-08 16:16:42
4	Rédaction	2025-10-08 16:16:56	2025-10-08 16:16:56
5	Développement Mobile	2025-10-08 16:17:12	2025-10-08 16:17:12
6	Développement Web	2025-10-23 12:06:37	2025-10-23 12:06:37
1	Design Graphique(UI/UX)	2025-10-08 15:45:23	2025-10-23 12:07:13
8	Développement de site web vitrine	2025-12-09 00:52:08	2025-12-09 00:52:08
9	Développement de site e-commerce	2025-12-09 00:52:08	2025-12-09 00:52:08
10	Développement d'application mobile iOS	2025-12-09 00:52:08	2025-12-09 00:52:08
11	Développement d'application mobile Android	2025-12-09 00:52:08	2025-12-09 00:52:08
12	Développement d'application web progressive (PWA)	2025-12-09 00:52:08	2025-12-09 00:52:08
13	Développement de plugin WordPress	2025-12-09 00:52:08	2025-12-09 00:52:08
14	Développement de thème WordPress	2025-12-09 00:52:08	2025-12-09 00:52:08
15	Développement de site Shopify	2025-12-09 00:52:08	2025-12-09 00:52:08
16	Développement de boutique WooCommerce	2025-12-09 00:52:08	2025-12-09 00:52:08
17	Intégration HTML/CSS responsive	2025-12-09 00:52:08	2025-12-09 00:52:08
18	Développement d'API REST	2025-12-09 00:52:08	2025-12-09 00:52:08
19	Développement de landing page	2025-12-09 00:52:08	2025-12-09 00:52:08
20	Développement de tableau de bord admin	2025-12-09 00:52:08	2025-12-09 00:52:08
21	Développement de plateforme SaaS	2025-12-09 00:52:08	2025-12-09 00:52:08
22	Développement de marketplace	2025-12-09 00:52:08	2025-12-09 00:52:08
23	Migration de site web	2025-12-09 00:52:08	2025-12-09 00:52:08
24	Optimisation des performances web	2025-12-09 00:52:08	2025-12-09 00:52:08
25	Développement de chatbot	2025-12-09 00:52:08	2025-12-09 00:52:08
26	Développement d'extension Chrome	2025-12-09 00:52:08	2025-12-09 00:52:08
27	Développement React/Vue/Angular	2025-12-09 00:52:08	2025-12-09 00:52:08
28	Design de logo et identité visuelle	2025-12-09 00:52:08	2025-12-09 00:52:08
29	Design d'interface utilisateur (UI)	2025-12-09 00:52:08	2025-12-09 00:52:08
30	Design d'expérience utilisateur (UX)	2025-12-09 00:52:08	2025-12-09 00:52:08
31	Design de maquettes web (mockups)	2025-12-09 00:52:08	2025-12-09 00:52:08
32	Design d'application mobile	2025-12-09 00:52:08	2025-12-09 00:52:08
33	Design de bannières publicitaires	2025-12-09 00:52:08	2025-12-09 00:52:08
34	Design de posts réseaux sociaux	2025-12-09 00:52:08	2025-12-09 00:52:08
35	Design d'infographies	2025-12-09 00:52:08	2025-12-09 00:52:08
36	Design de présentation PowerPoint	2025-12-09 00:52:08	2025-12-09 00:52:08
37	Design de newsletter email	2025-12-09 00:52:08	2025-12-09 00:52:08
38	Design de packaging produit	2025-12-09 00:52:08	2025-12-09 00:52:08
39	Illustration digitale	2025-12-09 00:52:08	2025-12-09 00:52:08
40	Création d'icônes personnalisées	2025-12-09 00:52:08	2025-12-09 00:52:08
41	Design de brochure digitale	2025-12-09 00:52:08	2025-12-09 00:52:08
42	Design de catalogue produit	2025-12-09 00:52:08	2025-12-09 00:52:08
43	Retouche photo professionnelle	2025-12-09 00:52:08	2025-12-09 00:52:08
44	Création de GIF animés	2025-12-09 00:52:08	2025-12-09 00:52:08
45	Design de signature email	2025-12-09 00:52:08	2025-12-09 00:52:08
46	Design de miniature YouTube	2025-12-09 00:52:08	2025-12-09 00:52:08
47	Design de couverture e-book	2025-12-09 00:52:08	2025-12-09 00:52:08
48	Stratégie de marketing digital	2025-12-09 00:52:08	2025-12-09 00:52:08
49	Gestion de campagne Google Ads	2025-12-09 00:52:08	2025-12-09 00:52:08
50	Gestion de campagne Facebook Ads	2025-12-09 00:52:08	2025-12-09 00:52:08
51	Optimisation du référencement SEO	2025-12-09 00:52:08	2025-12-09 00:52:08
52	Audit SEO complet	2025-12-09 00:52:08	2025-12-09 00:52:08
53	Rédaction de contenu SEO	2025-12-09 00:52:08	2025-12-09 00:52:08
54	Création de stratégie de contenu	2025-12-09 00:52:08	2025-12-09 00:52:08
55	Gestion de réseaux sociaux	2025-12-09 00:52:08	2025-12-09 00:52:08
56	Création de calendrier éditorial	2025-12-09 00:52:08	2025-12-09 00:52:08
57	Analyse de données Google Analytics	2025-12-09 00:52:08	2025-12-09 00:52:08
58	Email marketing et automation	2025-12-09 00:52:08	2025-12-09 00:52:08
59	Marketing d'affiliation	2025-12-09 00:52:08	2025-12-09 00:52:08
60	Growth hacking	2025-12-09 00:52:08	2025-12-09 00:52:08
61	Stratégie d'influence marketing	2025-12-09 00:52:08	2025-12-09 00:52:08
62	Community management	2025-12-09 00:52:08	2025-12-09 00:52:08
63	Publicité LinkedIn Ads	2025-12-09 00:52:08	2025-12-09 00:52:08
64	Remarketing et retargeting	2025-12-09 00:52:08	2025-12-09 00:52:08
65	Optimisation du taux de conversion (CRO)	2025-12-09 00:52:08	2025-12-09 00:52:08
66	Marketing automation	2025-12-09 00:52:08	2025-12-09 00:52:08
67	Analyse de la concurrence	2025-12-09 00:52:08	2025-12-09 00:52:08
68	Montage vidéo professionnel	2025-12-09 00:52:08	2025-12-09 00:52:08
69	Création de vidéo explicative	2025-12-09 00:52:08	2025-12-09 00:52:08
70	Animation motion design	2025-12-09 00:52:08	2025-12-09 00:52:08
71	Création de vidéo publicitaire	2025-12-09 00:52:08	2025-12-09 00:52:08
72	Sous-titrage de vidéo	2025-12-09 00:52:08	2025-12-09 00:52:08
73	Création d'intro et outro vidéo	2025-12-09 00:52:08	2025-12-09 00:52:08
74	Animation de logo	2025-12-09 00:52:08	2025-12-09 00:52:08
75	Création de vidéo de formation	2025-12-09 00:52:08	2025-12-09 00:52:08
76	Post-production vidéo	2025-12-09 00:52:08	2025-12-09 00:52:08
77	Création de story Instagram/TikTok	2025-12-09 00:52:08	2025-12-09 00:52:08
78	Animation 2D	2025-12-09 00:52:08	2025-12-09 00:52:08
79	Animation 3D	2025-12-09 00:52:08	2025-12-09 00:52:08
80	Création de reel Instagram	2025-12-09 00:52:08	2025-12-09 00:52:08
81	Montage vidéo YouTube	2025-12-09 00:52:08	2025-12-09 00:52:08
82	Création de vidéo de présentation	2025-12-09 00:52:08	2025-12-09 00:52:08
83	Rédaction d'articles de blog	2025-12-09 00:52:08	2025-12-09 00:52:08
84	Rédaction de pages web	2025-12-09 00:52:08	2025-12-09 00:52:08
85	Rédaction de fiches produits	2025-12-09 00:52:08	2025-12-09 00:52:08
86	Rédaction de scripts vidéo	2025-12-09 00:52:08	2025-12-09 00:52:08
87	Rédaction de communiqués de presse	2025-12-09 00:52:08	2025-12-09 00:52:08
88	Rédaction de livre blanc (white paper)	2025-12-09 00:52:08	2025-12-09 00:52:08
89	Rédaction de case study	2025-12-09 00:52:08	2025-12-09 00:52:08
90	Traduction de contenu web	2025-12-09 00:52:08	2025-12-09 00:52:08
91	Relecture et correction	2025-12-09 00:52:08	2025-12-09 00:52:08
92	Rédaction de contenu LinkedIn	2025-12-09 00:52:08	2025-12-09 00:52:08
93	Ghostwriting	2025-12-09 00:52:08	2025-12-09 00:52:08
94	Transcription audio/vidéo	2025-12-09 00:52:08	2025-12-09 00:52:08
95	Rédaction de CV et lettre de motivation	2025-12-09 00:52:08	2025-12-09 00:52:08
96	Rédaction technique et documentation	2025-12-09 00:52:08	2025-12-09 00:52:08
97	Création de dashboard Power BI	2025-12-09 00:52:08	2025-12-09 00:52:08
98	Création de dashboard Tableau	2025-12-09 00:52:08	2025-12-09 00:52:08
99	Analyse de données avec Excel	2025-12-09 00:52:08	2025-12-09 00:52:08
100	Web scraping et extraction de données	2025-12-09 00:52:08	2025-12-09 00:52:08
101	Création de rapports analytics	2025-12-09 00:52:08	2025-12-09 00:52:08
102	Nettoyage et préparation de données	2025-12-09 00:52:08	2025-12-09 00:52:08
103	Visualisation de données	2025-12-09 00:52:08	2025-12-09 00:52:08
104	Automatisation avec Google Sheets	2025-12-09 00:52:08	2025-12-09 00:52:08
105	Configuration Google Tag Manager	2025-12-09 00:52:08	2025-12-09 00:52:08
106	Audit de tracking analytics	2025-12-09 00:52:08	2025-12-09 00:52:08
\.


--
-- Data for Name: clients; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.clients (id, user_id, company_name, company_description, created_at, updated_at) FROM stdin;
1	3	TechSolutions SARL	Entreprise de développement digital	2025-10-08 11:00:03	2025-10-08 11:00:03
2	9	Fallou & frère SARL	Entreprise de développement digital	2025-10-11 14:31:27	2025-10-11 14:31:27
7	14	Modou Solution	\N	2025-11-20 13:35:39	2025-11-20 13:35:39
4	11	JD Tech	Startup qui aide les entreprise à intégrer l'IA dans leur quotidien	2025-11-18 01:26:55	2025-11-18 01:26:55
8	18	Les architectes du code	Une startup spécialisé dans la conception et réalisation d'application web, mobile et desktop.	2025-12-10 19:57:08	2025-12-10 19:57:08
9	19	SoftWeb	\N	2025-12-12 15:22:07	2025-12-12 15:22:07
\.


--
-- Data for Name: contracts; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.contracts (id, project_id, proposal_id, client_id, freelance_id, amount, duration, start_date, end_date, terms, status, signed_at, created_at, updated_at) FROM stdin;
3	8	5	4	6	1800.00	60	2025-11-29	2026-01-28	CONTRAT DE PRESTATION DE SERVICES\n\nEntre:\nClient: John Doe\nFreelance: Fatou Ndiaye\n\nObjet du contrat:\nProjet: Plateforme freelance avec paiement intégré\n\nDescription:\nDéveloppement d’une plateforme complète connectant freelances et clients, avec inscription, messagerie, gestion de contrats et paiement sécurisé.\n\nMontant convenu: 1800.00 €\nDurée estimée: 60 jours\n\nProposition du freelance:\nJohn Doe,\nMerci pour l’opportunité. Je serais ravi de vous accompagner dans la réalisation de votre plateforme freelance avec paiement intégré. J’ai déjà développé plusieurs applications web complètes basées sur Laravel et React/Angular, avec authentification sécurisée, gestion des rôles, messagerie temps réel et intégration de solutions de paiement (dont Stripe & escrow).\n\nPour ce projet, je vous propose une approche structurée : analyse précise de vos besoins, conception technique, développement modulaire, tests et mise en production. Mon objectif est de vous livrer une solution fiable, évolutive et conforme à votre vision.\n\nJe reste disponible pour échanger afin de clarifier les détails fonctionnels et commencer rapidement.\n\nCordialement,\nFatou Ndiaye\n\nDate de début: 29/11/2025\nDate de fin prévue: 28/01/2026\n\nLes deux parties s'engagent à respecter les termes de ce contrat.	active	2025-11-29 01:44:47	2025-11-29 01:44:47	2025-11-29 01:44:47
\.


--
-- Data for Name: conversations; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.conversations (id, client_id, freelance_id, status, last_message_at, created_at, updated_at) FROM stdin;
5	4	6	active	2025-12-12 01:48:43	2025-11-29 01:44:47	2025-12-12 01:48:43
3	1	1	active	2025-11-04 02:14:19	2025-11-02 15:25:47	2025-11-04 02:14:19
4	1	2	active	2025-11-13 17:49:55	2025-11-03 14:04:37	2025-11-13 17:49:55
\.


--
-- Data for Name: device_tokens; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.device_tokens (id, user_id, token, device_type, device_name, is_active, last_used_at, created_at, updated_at) FROM stdin;
15	15	cVrf9feYD1J2qnHdphaWgH:APA91bGnSjjh4TGw2_pRDcDT8JNXPEd4oAWduTw5-df8ewaIEB5rkPO8yod4AjA7KXqjY57tZglIqowKkNw9YL4giGdHCFkz6WG3n3si3mx8ptlOXtg41iQ	web	Chrome Desktop	t	2025-11-25 19:45:20	2025-11-20 17:20:13	2025-11-25 19:45:20
\.


--
-- Data for Name: failed_jobs; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.failed_jobs (id, uuid, connection, queue, payload, exception, failed_at) FROM stdin;
\.


--
-- Data for Name: freelance_skills; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.freelance_skills (id, freelance_id, skill_id, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: freelances; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.freelances (id, user_id, title, description, hourly_rate, experience_years, availability, created_at, updated_at) FROM stdin;
5	8	Designer UI/UX, Graphiste & Développeur	Designer créative avec 4 ans d'expérience	15.00	4	available	2025-10-08 11:41:11	2025-10-08 11:41:11
1	4	Développeur Full Stack Laravel/React	Expert en développement web avec 5 ans d'expérience	25.00	5	available	2025-10-08 11:03:15	2025-10-08 11:03:15
2	5	Designer UI/UX & Graphiste	Designer créative avec 4 ans d'expérience	15.00	4	available	2025-10-08 11:33:41	2025-10-08 11:33:41
6	15	Développeuse Fullstack	\N	20.00	3	available	2025-11-20 14:11:00	2025-11-20 14:11:00
7	16	Développeuse Frontend	\N	20.00	2	available	2025-12-10 19:26:17	2025-12-10 19:26:17
8	17	Développeuse Backend	\N	25.00	3	available	2025-12-10 19:44:08	2025-12-10 19:44:08
\.


--
-- Data for Name: jobs; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.jobs (id, queue, payload, attempts, reserved_at, available_at, created_at) FROM stdin;
100	broadcasts	{"uuid":"85ef1a7a-f284-415b-b540-8857342f3120","displayName":"App\\\\Events\\\\MessagesMarkedAsRead","job":"Illuminate\\\\Queue\\\\CallQueuedHandler@call","maxTries":null,"maxExceptions":null,"failOnTimeout":false,"backoff":null,"timeout":null,"retryUntil":null,"data":{"commandName":"Illuminate\\\\Broadcasting\\\\BroadcastEvent","command":"O:38:\\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\\":14:{s:5:\\"event\\";O:31:\\"App\\\\Events\\\\MessagesMarkedAsRead\\":3:{s:12:\\"conversation\\";O:45:\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\":5:{s:5:\\"class\\";s:23:\\"App\\\\Models\\\\Conversation\\";s:2:\\"id\\";i:3;s:9:\\"relations\\";a:0:{}s:10:\\"connection\\";s:5:\\"pgsql\\";s:15:\\"collectionClass\\";N;}s:6:\\"readBy\\";i:4;s:13:\\"messagesCount\\";i:1;}s:5:\\"tries\\";N;s:7:\\"timeout\\";N;s:7:\\"backoff\\";N;s:13:\\"maxExceptions\\";N;s:10:\\"connection\\";N;s:5:\\"queue\\";N;s:15:\\"chainConnection\\";N;s:10:\\"chainQueue\\";N;s:19:\\"chainCatchCallbacks\\";N;s:5:\\"delay\\";N;s:11:\\"afterCommit\\";N;s:10:\\"middleware\\";a:0:{}s:7:\\"chained\\";a:0:{}}"}}	0	\N	1762134646	1762134646
44	broadcasts	{"uuid":"a05f7ace-2069-4c5b-8523-8272944adef0","displayName":"App\\\\Events\\\\MessageSent","job":"Illuminate\\\\Queue\\\\CallQueuedHandler@call","maxTries":null,"maxExceptions":null,"failOnTimeout":false,"backoff":null,"timeout":null,"retryUntil":null,"data":{"commandName":"Illuminate\\\\Broadcasting\\\\BroadcastEvent","command":"O:38:\\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\\":14:{s:5:\\"event\\";O:22:\\"App\\\\Events\\\\MessageSent\\":2:{s:7:\\"message\\";O:45:\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\":5:{s:5:\\"class\\";s:18:\\"App\\\\Models\\\\Message\\";s:2:\\"id\\";i:14;s:9:\\"relations\\";a:2:{i:0;s:6:\\"sender\\";i:1;s:11:\\"attachments\\";}s:10:\\"connection\\";s:5:\\"pgsql\\";s:15:\\"collectionClass\\";N;}s:12:\\"conversation\\";O:45:\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\":5:{s:5:\\"class\\";s:23:\\"App\\\\Models\\\\Conversation\\";s:2:\\"id\\";i:3;s:9:\\"relations\\";a:0:{}s:10:\\"connection\\";s:5:\\"pgsql\\";s:15:\\"collectionClass\\";N;}}s:5:\\"tries\\";N;s:7:\\"timeout\\";N;s:7:\\"backoff\\";N;s:13:\\"maxExceptions\\";N;s:10:\\"connection\\";N;s:5:\\"queue\\";N;s:15:\\"chainConnection\\";N;s:10:\\"chainQueue\\";N;s:19:\\"chainCatchCallbacks\\";N;s:5:\\"delay\\";N;s:11:\\"afterCommit\\";N;s:10:\\"middleware\\";a:0:{}s:7:\\"chained\\";a:0:{}}"}}	0	\N	1762134504	1762134504
139	broadcasts	{"uuid":"275f9b97-a399-4b90-85e0-f3677aee223e","displayName":"App\\\\Events\\\\MessageSent","job":"Illuminate\\\\Queue\\\\CallQueuedHandler@call","maxTries":null,"maxExceptions":null,"failOnTimeout":false,"backoff":null,"timeout":null,"retryUntil":null,"data":{"commandName":"Illuminate\\\\Broadcasting\\\\BroadcastEvent","command":"O:38:\\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\\":14:{s:5:\\"event\\";O:22:\\"App\\\\Events\\\\MessageSent\\":2:{s:7:\\"message\\";O:45:\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\":5:{s:5:\\"class\\";s:18:\\"App\\\\Models\\\\Message\\";s:2:\\"id\\";i:16;s:9:\\"relations\\";a:2:{i:0;s:6:\\"sender\\";i:1;s:11:\\"attachments\\";}s:10:\\"connection\\";s:5:\\"pgsql\\";s:15:\\"collectionClass\\";N;}s:12:\\"conversation\\";O:45:\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\":5:{s:5:\\"class\\";s:23:\\"App\\\\Models\\\\Conversation\\";s:2:\\"id\\";i:3;s:9:\\"relations\\";a:0:{}s:10:\\"connection\\";s:5:\\"pgsql\\";s:15:\\"collectionClass\\";N;}}s:5:\\"tries\\";N;s:7:\\"timeout\\";N;s:7:\\"backoff\\";N;s:13:\\"maxExceptions\\";N;s:10:\\"connection\\";N;s:5:\\"queue\\";N;s:15:\\"chainConnection\\";N;s:10:\\"chainQueue\\";N;s:19:\\"chainCatchCallbacks\\";N;s:5:\\"delay\\";N;s:11:\\"afterCommit\\";N;s:10:\\"middleware\\";a:0:{}s:7:\\"chained\\";a:0:{}}"}}	0	\N	1762135030	1762135030
47	broadcasts	{"uuid":"e2dd1675-5336-4053-9895-ff33af894b94","displayName":"App\\\\Events\\\\MessagesMarkedAsRead","job":"Illuminate\\\\Queue\\\\CallQueuedHandler@call","maxTries":null,"maxExceptions":null,"failOnTimeout":false,"backoff":null,"timeout":null,"retryUntil":null,"data":{"commandName":"Illuminate\\\\Broadcasting\\\\BroadcastEvent","command":"O:38:\\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\\":14:{s:5:\\"event\\";O:31:\\"App\\\\Events\\\\MessagesMarkedAsRead\\":3:{s:12:\\"conversation\\";O:45:\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\":5:{s:5:\\"class\\";s:23:\\"App\\\\Models\\\\Conversation\\";s:2:\\"id\\";i:3;s:9:\\"relations\\";a:0:{}s:10:\\"connection\\";s:5:\\"pgsql\\";s:15:\\"collectionClass\\";N;}s:6:\\"readBy\\";i:3;s:13:\\"messagesCount\\";i:1;}s:5:\\"tries\\";N;s:7:\\"timeout\\";N;s:7:\\"backoff\\";N;s:13:\\"maxExceptions\\";N;s:10:\\"connection\\";N;s:5:\\"queue\\";N;s:15:\\"chainConnection\\";N;s:10:\\"chainQueue\\";N;s:19:\\"chainCatchCallbacks\\";N;s:5:\\"delay\\";N;s:11:\\"afterCommit\\";N;s:10:\\"middleware\\";a:0:{}s:7:\\"chained\\";a:0:{}}"}}	0	\N	1762134512	1762134512
141	broadcasts	{"uuid":"56cb3293-56cd-472c-8688-158de5fac7ce","displayName":"App\\\\Events\\\\MessagesMarkedAsRead","job":"Illuminate\\\\Queue\\\\CallQueuedHandler@call","maxTries":null,"maxExceptions":null,"failOnTimeout":false,"backoff":null,"timeout":null,"retryUntil":null,"data":{"commandName":"Illuminate\\\\Broadcasting\\\\BroadcastEvent","command":"O:38:\\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\\":14:{s:5:\\"event\\";O:31:\\"App\\\\Events\\\\MessagesMarkedAsRead\\":3:{s:12:\\"conversation\\";O:45:\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\":5:{s:5:\\"class\\";s:23:\\"App\\\\Models\\\\Conversation\\";s:2:\\"id\\";i:3;s:9:\\"relations\\";a:0:{}s:10:\\"connection\\";s:5:\\"pgsql\\";s:15:\\"collectionClass\\";N;}s:6:\\"readBy\\";i:3;s:13:\\"messagesCount\\";i:1;}s:5:\\"tries\\";N;s:7:\\"timeout\\";N;s:7:\\"backoff\\";N;s:13:\\"maxExceptions\\";N;s:10:\\"connection\\";N;s:5:\\"queue\\";N;s:15:\\"chainConnection\\";N;s:10:\\"chainQueue\\";N;s:19:\\"chainCatchCallbacks\\";N;s:5:\\"delay\\";N;s:11:\\"afterCommit\\";N;s:10:\\"middleware\\";a:0:{}s:7:\\"chained\\";a:0:{}}"}}	0	\N	1762135048	1762135048
98	broadcasts	{"uuid":"03401314-cab5-47f0-b730-dee3d4eae0ed","displayName":"App\\\\Events\\\\MessageSent","job":"Illuminate\\\\Queue\\\\CallQueuedHandler@call","maxTries":null,"maxExceptions":null,"failOnTimeout":false,"backoff":null,"timeout":null,"retryUntil":null,"data":{"commandName":"Illuminate\\\\Broadcasting\\\\BroadcastEvent","command":"O:38:\\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\\":14:{s:5:\\"event\\";O:22:\\"App\\\\Events\\\\MessageSent\\":2:{s:7:\\"message\\";O:45:\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\":5:{s:5:\\"class\\";s:18:\\"App\\\\Models\\\\Message\\";s:2:\\"id\\";i:15;s:9:\\"relations\\";a:2:{i:0;s:6:\\"sender\\";i:1;s:11:\\"attachments\\";}s:10:\\"connection\\";s:5:\\"pgsql\\";s:15:\\"collectionClass\\";N;}s:12:\\"conversation\\";O:45:\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\":5:{s:5:\\"class\\";s:23:\\"App\\\\Models\\\\Conversation\\";s:2:\\"id\\";i:3;s:9:\\"relations\\";a:0:{}s:10:\\"connection\\";s:5:\\"pgsql\\";s:15:\\"collectionClass\\";N;}}s:5:\\"tries\\";N;s:7:\\"timeout\\";N;s:7:\\"backoff\\";N;s:13:\\"maxExceptions\\";N;s:10:\\"connection\\";N;s:5:\\"queue\\";N;s:15:\\"chainConnection\\";N;s:10:\\"chainQueue\\";N;s:19:\\"chainCatchCallbacks\\";N;s:5:\\"delay\\";N;s:11:\\"afterCommit\\";N;s:10:\\"middleware\\";a:0:{}s:7:\\"chained\\";a:0:{}}"}}	0	\N	1762134576	1762134576
\.


--
-- Data for Name: messages; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.messages (id, conversation_id, sender_id, message_type, content, is_read, read_at, created_at, updated_at) FROM stdin;
1	3	3	text	Salut	t	2025-11-02 15:28:39	2025-11-02 15:26:20	2025-11-02 15:28:39
2	3	4	text	oui salut cv	t	2025-11-02 15:30:24	2025-11-02 15:29:14	2025-11-02 15:30:24
4	3	3	text	Comment se passe votre journée?	t	2025-11-02 15:54:17	2025-11-02 15:54:06	2025-11-02 15:54:17
5	3	4	text	ça se passe bien et la votre?	t	2025-11-02 15:55:53	2025-11-02 15:55:29	2025-11-02 15:55:53
6	3	3	text	tranquille okl	t	2025-11-02 19:28:59	2025-11-02 19:22:02	2025-11-02 19:28:59
8	3	4	text	Vous avez consulter les livrables	t	2025-11-02 19:33:39	2025-11-02 19:33:14	2025-11-02 19:33:39
9	3	3	text	Oui je les ai consulter	t	2025-11-02 19:45:40	2025-11-02 19:44:59	2025-11-02 19:45:40
10	3	4	text	Vos impressions?	t	2025-11-03 01:24:48	2025-11-03 01:23:29	2025-11-03 01:24:48
11	3	3	text	Vous avez respecté certains points important mais la partie paiement c'est pas bien configuré.	t	2025-11-03 01:35:26	2025-11-03 01:34:18	2025-11-03 01:35:26
12	3	4	text	Je vais y jeter un coup d'oeuil	t	2025-11-03 01:37:52	2025-11-03 01:37:19	2025-11-03 01:37:52
14	3	4	text	Dès que vous terminer vous me faites signe	t	2025-11-03 01:48:32	2025-11-03 01:48:24	2025-11-03 01:48:32
15	3	3	text	D'accord tu peux corriger ça après tu me revient	t	2025-11-03 01:50:46	2025-11-03 01:49:36	2025-11-03 01:50:46
16	3	4	text	D'accord je mis met tout de suite	t	2025-11-03 01:57:28	2025-11-03 01:57:10	2025-11-03 01:57:28
17	3	3	text	D'accord bonne chance	t	2025-11-03 02:18:15	2025-11-03 02:17:04	2025-11-03 02:18:15
18	3	4	text	🙏	t	2025-11-03 02:40:53	2025-11-03 02:21:05	2025-11-03 02:40:53
19	3	3	text	ok	t	2025-11-03 17:30:24	2025-11-03 02:40:59	2025-11-03 17:30:24
21	3	4	text	J'ai quelques chose	t	2025-11-03 17:31:18	2025-11-03 17:30:56	2025-11-03 17:31:18
22	3	3	text	je vous écoute	t	2025-11-03 17:31:58	2025-11-03 17:31:29	2025-11-03 17:31:58
23	3	4	text	Je vais vous envoyer un lien sur lequel vous pourrez voir les modifications.	t	2025-11-03 17:40:09	2025-11-03 17:39:07	2025-11-03 17:40:09
24	3	3	text	D'accord	t	2025-11-03 18:01:45	2025-11-03 17:49:48	2025-11-03 18:01:45
25	3	4	text	Comment vous voudriez que je fasse l'autre partie concernant le paiement?	t	2025-11-03 18:43:03	2025-11-03 18:39:46	2025-11-03 18:43:03
26	3	3	text	Je vous reviens tout suite	t	2025-11-03 19:07:18	2025-11-03 19:06:53	2025-11-03 19:07:18
27	3	4	text	D'accord je suis à votre écoute	t	2025-11-03 19:43:06	2025-11-03 19:41:42	2025-11-03 19:43:06
28	3	4	text	c'était à propos des api à utiliser	t	2025-11-03 19:53:01	2025-11-03 19:52:27	2025-11-03 19:53:01
29	3	3	text	Oui c'est ça	t	2025-11-03 19:55:15	2025-11-03 19:54:22	2025-11-03 19:55:15
30	3	4	text	Je pensais à utiliser les api de OM	t	2025-11-03 19:57:27	2025-11-03 19:55:42	2025-11-03 19:57:27
31	3	3	text	Oui c'est excellent même celui de wave tu peux l'utiliser	t	2025-11-04 01:13:52	2025-11-04 01:13:06	2025-11-04 01:13:52
32	3	4	text	D'accord	t	2025-11-04 01:15:34	2025-11-04 01:15:01	2025-11-04 01:15:34
33	3	3	text	ok	t	2025-11-04 01:16:12	2025-11-04 01:15:47	2025-11-04 01:16:12
34	3	4	text	J'ai fait des recherches mais apparemment ça demande un peu de procédures qui demandes vos documents concernant votre entreprise	t	2025-11-04 01:25:24	2025-11-04 01:25:00	2025-11-04 01:25:24
35	3	3	text	Tu peux m'énumérer les documents pour que je sache si ce ne sont pas documents trop confidentiels	t	2025-11-04 02:10:20	2025-11-04 01:26:23	2025-11-04 02:10:20
37	3	4	text	Ce sont le ninea et le registre de commerce	t	2025-11-04 02:14:08	2025-11-04 02:11:43	2025-11-04 02:14:08
38	3	3	text	Ahh d'accord	f	\N	2025-11-04 02:14:19	2025-11-04 02:14:19
20	4	3	text	Bonsoir j'espère que vous allez bien. Je suis ravi de vous avoir comme freelance pour ce mission.	t	2025-11-06 02:28:17	2025-11-03 14:58:27	2025-11-06 02:28:17
36	4	3	text	Rebonsoir	t	2025-11-06 02:28:17	2025-11-04 01:28:59	2025-11-06 02:28:17
39	4	5	text	Bonsoir monsieur je m'excuses vraiment du retard de réponse	t	2025-11-06 02:30:59	2025-11-06 02:28:48	2025-11-06 02:30:59
40	4	3	text	d'accord je comprends j'espère que vous allez bien?	t	2025-11-06 02:32:06	2025-11-06 02:31:21	2025-11-06 02:32:06
41	4	5	text	oui cv bien merci	t	2025-11-06 02:45:31	2025-11-06 02:45:14	2025-11-06 02:45:31
42	4	3	text	Pour le travail qu'est ce que vous en pensez?	t	2025-11-13 12:58:55	2025-11-13 12:57:36	2025-11-13 12:58:55
43	4	5	text	vous avez un intéressant projet	t	2025-11-13 13:00:32	2025-11-13 12:59:23	2025-11-13 13:00:32
44	4	3	text	Merci. Je voudrais quand vous pourrez commencez?	t	2025-11-13 13:59:24	2025-11-13 13:58:48	2025-11-13 13:59:24
45	4	5	text	bientôt	t	2025-11-13 15:42:07	2025-11-13 15:41:14	2025-11-13 15:42:07
46	4	3	text	D'accord	t	2025-11-13 15:54:17	2025-11-13 15:53:45	2025-11-13 15:54:17
47	4	5	text	Dès que je suis dispo je vous fait signe	t	2025-11-13 16:46:06	2025-11-13 16:45:31	2025-11-13 16:46:06
48	4	3	text	Ok je suis en écoute	t	2025-11-13 16:49:24	2025-11-13 16:49:00	2025-11-13 16:49:24
49	4	5	text	Ne vous en faites pas	t	2025-11-13 17:50:34	2025-11-13 17:49:55	2025-11-13 17:50:34
52	5	11	text	bonsoir madame	t	2025-12-12 01:48:03	2025-12-11 19:09:36	2025-12-12 01:48:03
53	5	15	text	oui bonsoir	t	2025-12-12 01:49:26	2025-12-12 01:48:43	2025-12-12 01:49:26
\.


--
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.migrations (id, migration, batch) FROM stdin;
1	2014_10_12_000000_create_users_table	1
2	2014_10_12_100000_create_password_reset_tokens_table	1
3	2019_08_19_000000_create_failed_jobs_table	1
4	2019_12_14_000001_create_personal_access_tokens_table	1
5	2025_09_30_151212_create_freelances_table	1
6	2025_09_30_153827_create_clients_table	1
7	2025_09_30_175455_create_categories_table	1
8	2025_09_30_175505_create_skills_table	1
9	2025_09_30_180109_create_freelance_skills_table	1
10	2025_09_30_181838_create_services_table	1
11	2025_09_30_182442_create_service_images_table	1
12	2025_09_30_182823_create_projects_table	1
13	2025_09_30_183050_create_project_skills_table	1
14	2025_09_30_183157_create_proposals_table	1
15	2025_10_04_114539_create_attachements_table	1
16	2025_10_06_091632_create_contracts_table	1
17	2025_10_06_092351_create_conversations_table	1
18	2025_10_06_093007_create_messages_table	1
19	2025_10_09_003640_create_projects_table	2
20	2025_10_09_091637_create_skills_table	3
21	2025_10_10_113514_create_orders_table	4
22	2025_10_24_162303_create_orders_table	5
23	2025_10_24_162614_create_services_table	5
24	2025_10_31_203048_create_conversations_table	6
25	2025_11_02_121020_create_conversations_table	7
26	2025_11_03_014651_create_jobs_table	8
27	0000_00_00_000000_create_websockets_statistics_entries_table	9
28	2025_11_06_005442_create_device_tokens_table	10
29	2025_11_14_160449_create_projects_table	11
30	2025_11_15_133153_create_project_tasks_table	12
31	2025_11_15_133326_create_projects_table	12
32	2025_11_16_013621_create_service_offers_table	13
33	2025_11_16_014010_create_remove_columns_from_services_table	13
34	2025_11_16_021222_create_add_service_offer_id_to_orders_table	14
35	2025_11_17_003050_create_services_table	15
36	2025_11_18_005847_create_users_table	16
37	2025_12_13_025726_create_sessions_table	17
38	2025_12_13_025741_create_cache_table	17
\.


--
-- Data for Name: orders; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.orders (id, client_id, amount, due_date, delivered_at, requirements, status, created_at, updated_at, service_offer_id) FROM stdin;
6	7	500.00	2025-12-05	\N	Bonjour,\r\nJe souhaite créer un site web vitrine pour mon entreprise.\r\nObjectifs du site :\r\n\r\nPrésenter mes services et mon expertise\r\nAttirer de nouveaux clients potentiels\r\nÉtablir ma crédibilité professionnelle en ligne\r\n\r\nPages souhaitées :\r\n\r\nAccueil avec présentation de l'entreprise\r\nServices (détail de mes offres)\r\nÀ propos / Mon parcours\r\nPortfolio ou réalisations\r\nTémoignages clients\r\nContact avec formulaire\r\n\r\nDesign et fonctionnalités :\r\n\r\nDesign moderne et professionnel\r\nResponsive (adapté mobile et tablette)\r\nOptimisation SEO de base\r\nFormulaire de contact fonctionnel\r\nIntégration de mes réseaux sociaux\r\n\r\nCouleurs et style : [Vos préférences : ex: bleu et blanc, style minimaliste]\r\nContenu : J'ai déjà mes textes et quelques images. J'aurai besoin d'aide pour optimiser le contenu.\r\nRéférence : J'aime le style de [exemple de site web que vous appréciez]\r\nMerci de me confirmer si ces éléments sont inclus dans votre offre.	pending	2025-11-28 02:48:17	2025-11-28 02:48:17	14
5	4	2500.00	2025-12-28	\N	Bonjour,\r\nJe souhaite créer un site web vitrine pour mon entreprise de e commerce\r\nObjectifs du site :\r\n\r\nPrésenter mes services et mon expertise\r\nAttirer de nouveaux clients potentiels\r\nÉtablir ma crédibilité professionnelle en ligne\r\n\r\nPages souhaitées :\r\n\r\nAccueil avec présentation de l'entreprise\r\nServices (détail de mes offres)\r\nÀ propos / Mon parcours\r\nPortfolio ou réalisations\r\nTémoignages clients\r\nContact avec formulaire\r\n\r\nDesign et fonctionnalités :\r\n\r\nDesign moderne et professionnel\r\nResponsive (adapté mobile et tablette)\r\nOptimisation SEO de base\r\nFormulaire de contact fonctionnel\r\nIntégration de mes réseaux sociaux\r\n\r\nContenu : J'ai déjà mes textes et quelques images. J'aurai besoin d'aide pour optimiser le contenu.\r\nMerci de me confirmer si ces éléments sont inclus dans votre offre.	in_progress	2025-11-28 02:36:09	2025-11-28 16:14:46	17
\.


--
-- Data for Name: password_reset_tokens; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.password_reset_tokens (email, token, created_at) FROM stdin;
\.


--
-- Data for Name: personal_access_tokens; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.personal_access_tokens (id, tokenable_type, tokenable_id, name, token, abilities, last_used_at, expires_at, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: project_skills; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.project_skills (id, project_id, skill_id, created_at, updated_at) FROM stdin;
15	8	2	\N	\N
16	8	8	\N	\N
17	9	32	\N	\N
18	9	57	\N	\N
19	9	60	\N	\N
20	9	100	\N	\N
21	9	115	\N	\N
22	10	8	\N	\N
23	10	109	\N	\N
24	10	84	\N	\N
25	11	143	\N	\N
26	11	144	\N	\N
27	11	150	\N	\N
28	11	151	\N	\N
29	11	153	\N	\N
30	12	32	2025-12-12 17:50:33	2025-12-12 17:50:33
31	12	57	2025-12-12 17:50:33	2025-12-12 17:50:33
32	12	60	2025-12-12 17:50:33	2025-12-12 17:50:33
33	12	100	2025-12-12 17:50:33	2025-12-12 17:50:33
34	12	115	2025-12-12 17:50:33	2025-12-12 17:50:33
35	13	8	2025-12-12 17:58:19	2025-12-12 17:58:19
36	13	25	2025-12-12 17:58:19	2025-12-12 17:58:19
37	13	109	2025-12-12 17:58:19	2025-12-12 17:58:19
38	13	84	2025-12-12 17:58:19	2025-12-12 17:58:19
39	14	143	2025-12-12 17:58:19	2025-12-12 17:58:19
40	14	144	2025-12-12 17:58:19	2025-12-12 17:58:19
41	14	156	2025-12-12 17:58:19	2025-12-12 17:58:19
42	14	157	2025-12-12 17:58:19	2025-12-12 17:58:19
43	14	151	2025-12-12 17:58:19	2025-12-12 17:58:19
44	14	153	2025-12-12 17:58:19	2025-12-12 17:58:19
45	15	6	2025-12-12 17:58:19	2025-12-12 17:58:19
46	15	7	2025-12-12 17:58:19	2025-12-12 17:58:19
47	15	74	2025-12-12 17:58:19	2025-12-12 17:58:19
48	16	1	2025-12-12 17:58:19	2025-12-12 17:58:19
49	16	137	2025-12-12 17:58:19	2025-12-12 17:58:19
50	16	138	2025-12-12 17:58:19	2025-12-12 17:58:19
51	16	139	2025-12-12 17:58:19	2025-12-12 17:58:19
52	16	140	2025-12-12 17:58:19	2025-12-12 17:58:19
53	16	141	2025-12-12 17:58:19	2025-12-12 17:58:19
54	17	5	2025-12-12 17:58:19	2025-12-12 17:58:19
55	17	7	2025-12-12 17:58:19	2025-12-12 17:58:19
56	18	88	2025-12-12 17:58:19	2025-12-12 17:58:19
57	18	89	2025-12-12 17:58:19	2025-12-12 17:58:19
58	18	26	2025-12-12 17:58:19	2025-12-12 17:58:19
59	18	84	2025-12-12 17:58:19	2025-12-12 17:58:19
60	18	154	2025-12-12 17:58:19	2025-12-12 17:58:19
61	19	166	2025-12-12 17:58:19	2025-12-12 17:58:19
62	19	170	2025-12-12 17:58:19	2025-12-12 17:58:19
63	19	37	2025-12-12 17:58:19	2025-12-12 17:58:19
64	19	164	2025-12-12 17:58:19	2025-12-12 17:58:19
65	20	167	2025-12-12 17:58:19	2025-12-12 17:58:19
66	20	168	2025-12-12 17:58:19	2025-12-12 17:58:19
67	20	169	2025-12-12 17:58:19	2025-12-12 17:58:19
68	20	171	2025-12-12 17:58:19	2025-12-12 17:58:19
\.


--
-- Data for Name: project_tasks; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.project_tasks (id, project_id, title, description, "order", status, priority, completed_at, created_at, updated_at) FROM stdin;
1	8	Créer la maquette UI/UX	Réaliser les écrans principaux (inscription, tableau de bord, messagerie, projets, paiement).	0	pending	moyenne	\N	2025-11-19 20:05:24	2025-11-19 20:05:24
3	8	Mettre en place l’inscription client & freelance	Formulaire, validation, stockage sécurisé.	2	pending	haute	\N	2025-11-19 20:06:46	2025-11-19 20:06:46
5	8	Implémenter la gestion des rôles (Admin, Freelance, Client)	Middleware, autorisations et dashboards dédiés.	4	pending	haute	\N	2025-11-19 20:07:50	2025-11-19 20:07:50
6	8	Créer le module de publication de projets	Titre, description, budget, compétences et pièces jointes.	5	pending	haute	\N	2025-11-19 20:08:20	2025-11-19 20:08:20
4	8	Ajouter la vérification email	Envoi d’email de confirmation et activation du compte.	3	pending	urgente	\N	2025-11-19 20:07:12	2025-11-19 20:10:23
2	8	Concevoir le design système	Définir les couleurs, typographies, composants globaux et layout.	1	completed	basse	2025-11-29 03:13:44	2025-11-19 20:06:11	2025-11-29 03:13:44
7	9	Créer la maquette de l'interface utilisateur	Concevoir les maquettes UI/UX pour toutes les écrans de l'application (Dashboard, Suivi nutrition, Suivi entraînement, Profil utilisateur, Paramètres). Inclure les wireframes et le prototype interactif sur Figma.	0	pending	haute	\N	2025-12-12 15:14:03	2025-12-12 15:14:03
8	9	Développer le système d'authentification	Implémenter le système de connexion/inscription avec email et mot de passe, OAuth (Google, Apple), gestion des tokens JWT, et intégration Firebase Authentication.	1	pending	urgente	\N	2025-12-12 15:14:42	2025-12-12 15:14:42
9	9	Intégrer HealthKit pour le suivi d'activité	Développer l'intégration avec HealthKit d'Apple pour synchroniser automatiquement les données de santé (pas, calories brûlées, fréquence cardiaque). Gérer les permissions utilisateur.	2	pending	haute	\N	2025-12-12 15:15:14	2025-12-12 15:15:14
10	9	Créer le module de suivi nutritionnel	Développer le système de journal alimentaire avec base de données d'aliments, scan de code-barres, calcul automatique des macros (protéines, glucides, lipides), et graphiques de progression.	3	pending	moyenne	\N	2025-12-12 15:15:40	2025-12-12 15:15:40
11	9	Implémenter le système de notifications push	Configurer les notifications push pour rappeler les repas, les entraînements, les objectifs quotidiens. Permettre à l'utilisateur de personnaliser les horaires et types de notifications.	4	pending	moyenne	\N	2025-12-12 15:16:13	2025-12-12 15:16:13
12	9	Développer le mode hors ligne	Implémenter Core Data pour le stockage local des données, synchronisation automatique quand la connexion est rétablie, gestion des conflits de données.	5	pending	haute	\N	2025-12-12 15:16:58	2025-12-12 15:16:58
13	10	Configurer l'environnement de développement	Installer Laravel, configurer la base de données MySQL, mettre en place Git, créer la structure MVC de base, installer les dépendances (Tailwind CSS, Livewire si nécessaire).	0	pending	urgente	\N	2025-12-12 15:27:08	2025-12-12 15:27:08
14	10	Développer le système de gestion des produits	Créer le CRUD complet pour les produits (nom, description, prix, images multiples, tailles, couleurs, stock), système de variations produits, gestion des catégories et sous-catégories.	1	pending	haute	\N	2025-12-12 15:27:37	2025-12-12 15:27:37
15	10	Implémenter le système de recommandation de taille	Développer un questionnaire interactif (mensurations utilisateur), algorithme de recommandation de taille basé sur les mesures, guide des tailles par produit, historique des tailles achetées.	2	pending	moyenne	\N	2025-12-12 15:28:15	2025-12-12 15:28:15
16	10	Intégrer le système de paiement Stripe	Configurer Stripe, implémenter le tunnel de paiement sécurisé, gérer les webhooks, afficher les confirmations de paiement, envoyer les emails de confirmation automatiques.	3	pending	haute	\N	2025-12-12 15:28:43	2025-12-12 15:28:43
17	10	Créer le système de panier et checkout	Développer le panier d'achat avec ajout/suppression/modification, calcul du total et frais de livraison, codes promo, sauvegarde du panier pour utilisateurs connectés.	4	pending	haute	\N	2025-12-12 15:29:14	2025-12-12 15:29:14
18	10	Développer l'espace client	Créer le dashboard client avec historique des commandes, suivi de livraison, gestion du profil, adresses de livraison multiples, wishlist de produits favoris.	5	pending	moyenne	\N	2025-12-12 15:29:38	2025-12-12 15:29:38
19	10	Implémenter le système de filtres avancés	Développer les filtres de recherche (prix, taille, couleur, marque, catégorie), tri des résultats (popularité, prix, nouveautés), pagination, recherche textuelle avec autocomplétion.	6	pending	moyenne	\N	2025-12-12 15:30:06	2025-12-12 15:30:06
20	11	Définir la stratégie publicitaire	Analyser le produit et la cible, définir les objectifs de campagne (notoriété, trafic, conversions), créer les personas détaillés, planifier le budget par audience, établir les KPI à suivre.	0	pending	urgente	\N	2025-12-12 15:35:17	2025-12-12 15:35:17
21	11	Créer les audiences ciblées	Configurer les audiences Facebook (démographie, intérêts, comportements), créer des audiences personnalisées (visiteurs site, clients existants), développer des audiences similaires (lookalike), tester 3-5 audiences différentes.	1	pending	haute	\N	2025-12-12 15:35:42	2025-12-12 15:35:42
22	11	Designer les visuels publicitaires	Créer 5 visuels attractifs conformes aux guidelines Facebook/Instagram, décliner en formats (carré, vertical, story), rédiger les textes accrocheurs avec CTA clairs, préparer 2-3 variations par visuel pour A/B testing.	2	pending	haute	\N	2025-12-12 15:36:15	2025-12-12 15:36:15
23	11	Lancer et optimiser les campagnes	Configurer les campagnes dans Ads Manager, implémenter le pixel Facebook, lancer les tests A/B sur visuels et audiences, analyser les performances quotidiennes, réallouer le budget vers les meilleures campagnes.	3	pending	urgente	\N	2025-12-12 15:36:37	2025-12-12 15:36:37
24	11	Effectuer le suivi et reporting hebdomadaire	Créer un dashboard de suivi (impressions, clics, CTR, conversions, CPA, ROAS), analyser les métriques chaque semaine, ajuster les enchères et audiences, produire un rapport PDF avec recommandations.	4	pending	moyenne	\N	2025-12-12 15:37:04	2025-12-12 15:37:04
25	13	Configurer l'environnement de développement	Installer Laravel, configurer la base de données MySQL, mettre en place Git, créer la structure MVC de base, installer les dépendances (Tailwind CSS, Livewire si nécessaire).	1	pending	urgente	\N	2025-12-12 17:58:19	2025-12-12 17:58:19
26	13	Développer le système de gestion des produits	Créer le CRUD complet pour les produits (nom, description, prix, images multiples, tailles, couleurs, stock), système de variations produits, gestion des catégories et sous-catégories.	2	pending	haute	\N	2025-12-12 17:58:19	2025-12-12 17:58:19
27	13	Implémenter le système de recommandation de taille	Développer un questionnaire interactif (mensurations utilisateur), algorithme de recommandation de taille basé sur les mesures, guide des tailles par produit, historique des tailles achetées.	3	pending	moyenne	\N	2025-12-12 17:58:19	2025-12-12 17:58:19
28	13	Intégrer le système de paiement Stripe	Configurer Stripe, implémenter le tunnel de paiement sécurisé, gérer les webhooks, afficher les confirmations de paiement, envoyer les emails de confirmation automatiques.	4	pending	haute	\N	2025-12-12 17:58:19	2025-12-12 17:58:19
29	13	Créer le système de panier et checkout	Développer le panier d'achat avec ajout/suppression/modification, calcul du total et frais de livraison, codes promo, sauvegarde du panier pour utilisateurs connectés.	5	pending	haute	\N	2025-12-12 17:58:19	2025-12-12 17:58:19
30	14	Définir la stratégie publicitaire	Analyser le produit et la cible, définir les objectifs de campagne (notoriété, trafic, conversions), créer les personas détaillés, planifier le budget par audience, établir les KPI à suivre.	1	pending	urgente	\N	2025-12-12 17:58:19	2025-12-12 17:58:19
31	14	Créer les audiences ciblées	Configurer les audiences Facebook (démographie, intérêts, comportements), créer des audiences personnalisées (visiteurs site, clients existants), développer des audiences similaires (lookalike), tester 3-5 audiences différentes.	2	pending	haute	\N	2025-12-12 17:58:19	2025-12-12 17:58:19
32	14	Designer les visuels publicitaires	Créer 5 visuels attractifs conformes aux guidelines Facebook/Instagram, décliner en formats (carré, vertical, story), rédiger les textes accrocheurs avec CTA clairs, préparer 2-3 variations par visuel pour A/B testing.	3	pending	haute	\N	2025-12-12 17:58:19	2025-12-12 17:58:19
33	14	Lancer et optimiser les campagnes	Configurer les campagnes dans Ads Manager, implémenter le pixel Facebook, lancer les tests A/B sur visuels et audiences, analyser les performances quotidiennes, réallouer le budget vers les meilleures campagnes.	4	pending	urgente	\N	2025-12-12 17:58:19	2025-12-12 17:58:19
34	14	Effectuer le suivi et reporting hebdomadaire	Créer un dashboard de suivi (impressions, clics, CTR, conversions, CPA, ROAS), analyser les métriques chaque semaine, ajuster les enchères et audiences, produire un rapport PDF avec recommandations.	5	pending	moyenne	\N	2025-12-12 17:58:19	2025-12-12 17:58:19
35	15	Recherche et brainstorming créatif	Analyser la concurrence, définir les valeurs de la marque, créer un mood board avec inspirations visuelles, proposer 3 directions artistiques différentes basées sur le brief client.	1	pending	urgente	\N	2025-12-12 17:58:19	2025-12-12 17:58:19
36	15	Créer 3 propositions de logo	Concevoir 3 concepts de logo différents (typographique, pictogramme, combiné), chacun décliné en couleur et noir & blanc, avec variations (horizontal, vertical, icône seule).	2	pending	haute	\N	2025-12-12 17:58:19	2025-12-12 17:58:19
37	15	Développer la charte graphique complète	Créer le document de charte graphique incluant : palette de couleurs (primaire, secondaire, nuances), typographies (titres, corps de texte, web), règles d'utilisation du logo, espacements minimaux.	3	pending	haute	\N	2025-12-12 17:58:19	2025-12-12 17:58:19
38	15	Designer les cartes de visite	Concevoir le design des cartes de visite recto-verso en appliquant l'identité visuelle, préparer les fichiers pour l'impression (format PDF avec traits de coupe, CMJN, 300 DPI).	4	pending	moyenne	\N	2025-12-12 17:58:19	2025-12-12 17:58:19
39	15	Créer les templates réseaux sociaux	Designer 10 templates pour Instagram/Facebook/LinkedIn (posts, stories, couvertures) facilement personnalisables, livrés en formats Photoshop et Canva.	5	pending	moyenne	\N	2025-12-12 17:58:19	2025-12-12 17:58:19
40	16	Effectuer l'audit SEO technique complet	Analyser la structure du site, vitesse de chargement, mobile-friendliness, erreurs 404, redirections, sitemap XML, robots.txt, données structurées Schema.org, HTTPS, Core Web Vitals.	1	pending	urgente	\N	2025-12-12 17:58:19	2025-12-12 17:58:19
41	16	Recherche et analyse des mots-clés	Identifier 50-100 mots-clés pertinents pour le secteur de l'architecture, analyser le volume de recherche, la concurrence, l'intention de recherche, créer une stratégie de ciblage par page.	2	pending	haute	\N	2025-12-12 17:58:19	2025-12-12 17:58:19
42	16	Optimiser le SEO on-page	Réécrire les balises title et meta descriptions, optimiser les H1-H6, améliorer le maillage interne, optimiser les URLs, ajouter du texte alternatif aux images, améliorer le contenu existant.	3	pending	haute	\N	2025-12-12 17:58:19	2025-12-12 17:58:19
43	16	Rédiger 10 articles de blog optimisés SEO	Créer 10 articles de 1500-2000 mots sur des sujets liés à l'architecture (tendances, conseils, projets), optimisés avec mots-clés, images optimisées, liens internes/externes.	4	pending	moyenne	\N	2025-12-12 17:58:19	2025-12-12 17:58:19
44	16	Développer la stratégie de backlinks	Identifier 20-30 sites pertinents pour obtenir des backlinks, créer une stratégie de guest posting, soumettre le site aux annuaires de qualité, établir des partenariats.	5	pending	moyenne	\N	2025-12-12 17:58:19	2025-12-12 17:58:19
45	16	Configurer Google Analytics et Search Console	Installer GA4, configurer les objectifs de conversion, créer un dashboard personnalisé, vérifier Search Console, soumettre le sitemap, créer un rapport mensuel automatisé.	6	pending	haute	\N	2025-12-12 17:58:19	2025-12-12 17:58:19
46	17	Organiser et trier les rushes vidéo	Visionner tous les rushes fournis, créer une bibliothèque organisée par propriété, sélectionner les meilleurs plans, noter les timecodes importants, créer un document de planning des vidéos.	1	pending	urgente	\N	2025-12-12 17:58:19	2025-12-12 17:58:19
47	17	Créer le template graphique des vidéos	Designer les éléments graphiques réutilisables (intro animée, lower thirds, transitions, animations texte, outro avec logo), définir la palette de couleurs et les polices conformes à la charte.	2	pending	haute	\N	2025-12-12 17:58:19	2025-12-12 17:58:19
48	17	Monter les 10 premières vidéos	Assembler les clips, ajouter les transitions, intégrer les animations texte, synchroniser la musique, ajouter les sous-titres, exporter en format vertical (1080x1920) et carré (1080x1080).	3	pending	haute	\N	2025-12-12 17:58:19	2025-12-12 17:58:19
49	17	Monter les 10 dernières vidéos	Continuer le montage des 10 vidéos restantes en respectant le même style graphique, ajuster le rythme selon chaque propriété, optimiser pour l'engagement sur réseaux sociaux.	4	pending	moyenne	\N	2025-12-12 17:58:19	2025-12-12 17:58:19
50	17	Ajouter sous-titres et finalisation	Créer les sous-titres en français pour toutes les vidéos (format SRT + incrustés), effectuer la correction colorimétrique, équilibrer l'audio, ajouter les logos animés, livrer les fichiers finaux.	5	pending	moyenne	\N	2025-12-12 17:58:19	2025-12-12 17:58:19
51	18	Créer la maquette de la landing page	Designer une maquette complète sur Figma incluant : hero section avec vidéo, section bénéfices, témoignages, programme de formation, FAQ, formulaire d'inscription, footer. Versions desktop et mobile.	1	pending	haute	\N	2025-12-12 17:58:19	2025-12-12 17:58:19
52	18	Intégrer le HTML/CSS responsive	Coder la landing page en HTML5/CSS3, utiliser Flexbox/Grid, assurer la compatibilité cross-browser, optimiser pour mobile-first, animations CSS au scroll.	2	pending	haute	\N	2025-12-12 17:58:19	2025-12-12 17:58:19
53	18	Développer le formulaire d'inscription	Créer un formulaire avec validation JavaScript (nom, email, téléphone), messages d'erreur personnalisés, intégration avec Mailchimp API, page de remerciement avec redirection automatique.	3	pending	urgente	\N	2025-12-12 17:58:19	2025-12-12 17:58:19
54	18	Intégrer la vidéo de présentation	Optimiser et intégrer la vidéo de présentation (YouTube ou hébergement direct), créer un player personnalisé avec overlay, tracking des vues, bouton CTA dans la vidéo.	4	pending	moyenne	\N	2025-12-12 17:58:19	2025-12-12 17:58:19
55	18	Optimiser pour la conversion	Placer stratégiquement les CTA, optimiser les titres et textes persuasifs, ajouter des éléments de preuve sociale (compteur d'inscrits, badges de confiance), implémenter Google Analytics avec tracking des conversions.	5	pending	haute	\N	2025-12-12 17:58:19	2025-12-12 17:58:19
56	19	Analyser les besoins et sources de données	Rencontrer l'équipe pour définir les KPI prioritaires, identifier les sources de données (SQL Server, Excel, etc.), documenter la structure des tables, définir les relations entre les données.	1	pending	urgente	\N	2025-12-12 17:58:19	2025-12-12 17:58:19
57	19	Préparer et nettoyer les données	Se connecter à SQL Server, importer les tables nécessaires, nettoyer les données (doublons, valeurs manquantes), créer les relations entre tables, développer les mesures DAX pour les calculs.	2	pending	haute	\N	2025-12-12 17:58:19	2025-12-12 17:58:19
58	19	Créer les visualisations principales	Développer les graphiques clés (CA par région, évolution mensuelle, top produits, performance par vendeur), utiliser des visuels interactifs (cartes, graphiques en cascade, jauges), appliquer un thème cohérent.	3	pending	haute	\N	2025-12-12 17:58:19	2025-12-12 17:58:19
59	19	Développer les filtres et interactivité	Ajouter des slicers (période, région, produit, vendeur), configurer les interactions entre visuels, créer des drill-through pour détails, implémenter des tooltips personnalisés.	4	pending	moyenne	\N	2025-12-12 17:58:19	2025-12-12 17:58:19
60	19	Automatiser les mises à jour	Configurer l'actualisation automatique des données (quotidienne/hebdomadaire), paramétrer Power BI Service pour le partage, tester la mise à jour automatique, documenter le processus.	5	pending	haute	\N	2025-12-12 17:58:19	2025-12-12 17:58:19
61	19	Former l'équipe et livrer la documentation	Organiser une session de formation de 2h avec l'équipe, créer un guide utilisateur PDF, démontrer comment utiliser les filtres et interpréter les données, recueillir les feedbacks.	6	pending	moyenne	\N	2025-12-12 17:58:19	2025-12-12 17:58:19
62	20	Analyser les processus RH actuels	Rencontrer l'équipe RH pour comprendre les workflows manuels, identifier les points de friction, documenter les processus actuels avec flowcharts, définir les priorités d'automatisation.	1	pending	urgente	\N	2025-12-12 17:58:19	2025-12-12 17:58:19
63	20	Automatiser le processus d'onboarding	Créer un flow déclenché lors de l'ajout d'un nouvel employé dans SharePoint, envoi automatique des documents de bienvenue, création des comptes utilisateur, ajout aux groupes Teams appropriés, notification au manager.	2	pending	haute	\N	2025-12-12 17:58:19	2025-12-12 17:58:19
64	20	Développer le workflow de gestion des congés	Créer un formulaire de demande de congé dans SharePoint/Forms, workflow d'approbation automatique vers le manager, mise à jour du calendrier partagé, envoi d'emails de confirmation, gestion des rejets avec commentaires.	3	pending	haute	\N	2025-12-12 17:58:19	2025-12-12 17:58:19
65	20	Automatiser la validation des notes de frais	Développer un workflow multi-niveaux (manager > finance > direction si > 1000€), notifications par email et Teams, suivi du statut en temps réel, archivage automatique des documents validés.	4	pending	moyenne	\N	2025-12-12 17:58:19	2025-12-12 17:58:19
66	20	Créer les rappels d'évaluation annuelle	Flow automatique envoyant des rappels 30, 15 et 7 jours avant les échéances d'évaluation, notifications aux managers et employés, mise à jour du statut dans SharePoint.	5	pending	moyenne	\N	2025-12-12 17:58:19	2025-12-12 17:58:19
67	20	Former l'équipe et livrer la documentation	Créer une documentation complète pour chaque workflow (avec captures d'écran), organiser 2 sessions de formation de 2h pour l'équipe RH, créer des guides de dépannage.	6	pending	haute	\N	2025-12-12 17:58:19	2025-12-12 17:58:19
\.


--
-- Data for Name: projects; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.projects (id, client_id, title, description, budget, deadline, created_at, updated_at, category_id, duration, start_date, progress, status) FROM stdin;
8	4	Plateforme freelance avec paiement intégré	Développement d’une plateforme complète connectant freelances et clients, avec inscription, messagerie, gestion de contrats et paiement sécurisé.	2000.00	2026-01-28	2025-11-19 13:56:16	2025-11-29 03:13:44	6	30	2025-11-29	17	in_progress
9	8	Application de suivi de fitness et nutrition	Je recherche un développeur iOS pour créer une application mobile permettant aux utilisateurs de suivre leurs entraînements, leur alimentation et leurs progrès. L'app doit inclure un tableau de bord, un système de notifications, l'intégration avec HealthKit, et un mode hors ligne.	8000.00	\N	2025-12-12 12:56:58	2025-12-12 12:56:58	10	90	\N	0	en_attente
10	9	Boutique en ligne de vêtements avec système de tailles personnalisé	Besoin d'un site e-commerce complet pour vendre des vêtements. Fonctionnalités requises : catalogue produits, filtres avancés, système de recommandation de taille, panier, paiement sécurisé (Stripe), gestion des stocks, espace client, système de suivi de commande.	5000.00	\N	2025-12-12 15:26:22	2025-12-12 15:26:22	9	45	\N	0	en_attente
11	9	Campagne publicitaire Facebook/Instagram pour e-commerce	Lancement d'une campagne publicitaire de 30 jours pour notre boutique de cosmétiques. Budget pub : 3000$. Besoin de : stratégie complète, création des audiences, design de 5 visuels publicitaires, rédaction des textes, A/B testing, optimisation quotidienne, rapport hebdomadaire détaillé.	1000.00	\N	2025-12-12 15:34:08	2025-12-12 15:34:08	50	30	\N	0	en_attente
12	1	Développement d'une application mobile de fitness	Je recherche un développeur iOS expérimenté pour créer une application mobile de suivi de fitness et nutrition. L'application doit permettre aux utilisateurs de suivre leurs entraînements, leur alimentation et leurs progrès. Fonctionnalités requises : Dashboard personnalisé, système de notifications push, intégration HealthKit, journal alimentaire avec scan de code-barres, graphiques de progression, mode hors ligne avec synchronisation automatique. L'interface doit être moderne, intuitive et respecter les guidelines Apple.	8000.00	\N	2025-12-12 17:50:33	2025-12-12 17:50:33	10	60	\N	0	open
14	1	Campagne publicitaire Facebook et Instagram pour lancement produit	Nous lançons une nouvelle gamme de produits cosmétiques bio et avons besoin d'un expert en publicité Facebook/Instagram. Budget publicitaire : 3000$ sur 30 jours. Mission : Créer et gérer une campagne complète de A à Z. Définir la stratégie et les audiences cibles, créer 5 visuels publicitaires professionnels conformes à notre charte graphique, rédiger les textes accrocheurs avec appels à l'action, mettre en place des tests A/B sur visuels et audiences, optimiser quotidiennement les performances, fournir des rapports hebdomadaires détaillés avec recommandations. Objectif : Maximiser les conversions et le ROAS.	1000.00	\N	2025-12-12 17:58:19	2025-12-12 17:58:19	50	30	\N	0	open
15	2	Design d'identité visuelle complète pour startup tech	Notre startup technologique en phase de lancement recherche un designer talentueux pour créer notre identité visuelle de A à Z. Nous développons une solution SaaS B2B et avons besoin d'une image professionnelle, moderne et innovante. Livrables attendus : 3 propositions de logo différentes (typographique, pictogramme, combiné), charte graphique complète (palette de couleurs primaire et secondaire, typographies pour titres et corps de texte, règles d'utilisation du logo avec espacements minimaux), cartes de visite recto-verso prêtes pour l'impression, 10 templates pour réseaux sociaux (Instagram, Facebook, LinkedIn) personnalisables. Nous recherchons un style épuré, tech, avec une touche d'originalité.	1500.00	\N	2025-12-12 17:58:19	2025-12-12 17:58:19	28	15	\N	0	open
16	2	Audit SEO et optimisation complète pour site d'architecture	Cabinet d'architecture cherche expert SEO pour améliorer drastiquement notre visibilité sur Google. Notre site web manque de trafic organique malgré la qualité de nos projets. Mission complète : Réaliser un audit SEO technique approfondi (vitesse, mobile, erreurs 404, structure, Schema.org), recherche et analyse de 50-100 mots-clés pertinents dans le secteur de l'architecture, optimisation on-page de toutes les pages (balises title, meta descriptions, H1-H6, URLs, images), rédaction de 10 articles de blog optimisés SEO (1500-2000 mots chacun) sur des thématiques architecture, développement d'une stratégie de backlinks avec identification de 20-30 sites partenaires, configuration Google Analytics 4 et Search Console avec rapport mensuel. Délai : 30 jours.	2500.00	\N	2025-12-12 17:58:19	2025-12-12 17:58:19	51	30	\N	0	open
17	2	Montage de 20 vidéos promotionnelles immobilières pour réseaux sociaux	Agence immobilière en pleine expansion cherche monteur vidéo professionnel pour créer 20 vidéos courtes et percutantes. Nous fournissons tous les rushes (visites virtuelles, photos, plans). Spécifications techniques : Vidéos de 30 à 60 secondes, animations texte dynamiques avec informations clés (prix, surface, localisation), transitions fluides et professionnelles, musique libre de droits adaptée, sous-titres en français, logo animé en intro et outro. Formats requis : Vertical 1080x1920 pour Instagram/TikTok et Carré 1080x1080 pour Facebook. Style moderne, élégant, donnant envie. Les vidéos doivent capter l'attention dans les 3 premières secondes.	1200.00	\N	2025-12-12 17:58:19	2025-12-12 17:58:19	68	20	\N	0	open
18	7	Landing page haute conversion pour formation en marketing digital	Nous lançons une formation en ligne sur le marketing digital et avons besoin d'une landing page qui convertit vraiment ! La page doit être optimisée pour la conversion avec un design moderne et professionnel. Éléments requis : Hero section impactante avec vidéo de présentation (que nous fournirons), section bénéfices avec icônes, témoignages clients avec photos et notes, programme détaillé de la formation, FAQ complète, formulaire d'inscription optimisé avec intégration Mailchimp, boutons CTA stratégiquement placés, page de remerciement après inscription, design 100% responsive (mobile-first). Technologies : HTML5, CSS3, JavaScript vanilla. Optimisation vitesse de chargement obligatoire.	800.00	\N	2025-12-12 17:58:19	2025-12-12 17:58:19	19	10	\N	0	open
19	7	Dashboard Power BI interactif pour suivi des KPI de ventes	Entreprise de distribution cherche expert Power BI pour créer un tableau de bord interactif et visuel de nos performances commerciales. Nous avons une base de données SQL Server avec toutes nos données de ventes, clients et produits. Le dashboard doit présenter : Chiffre d'affaires global et par période (jour, semaine, mois, année), analyse par région géographique avec carte interactive, performance par produit et catégorie, analyse par commercial avec classement, graphiques d'évolution des ventes, prévisions basées sur l'historique, indicateurs KPI principaux (taux de croissance, panier moyen, nombre de clients), filtres dynamiques (période, région, produit, vendeur). Connexion automatique à SQL Server avec rafraîchissement quotidien. Formation de 2h pour notre équipe prévue.	1800.00	\N	2025-12-12 17:58:19	2025-12-12 17:58:19	97	20	\N	0	open
20	7	Automatisation des processus RH avec Power Automate	Notre département RH gère manuellement de nombreux processus répétitifs et chronophages. Nous cherchons un expert Power Automate pour automatiser nos workflows principaux. Processus à automatiser : Onboarding des nouveaux employés (envoi automatique des documents, création des comptes, ajout aux groupes Teams), gestion des demandes de congés (notification managers, mise à jour calendrier partagé, email de confirmation), processus de validation des notes de frais (workflow d'approbation multi-niveaux), rappels automatiques pour les évaluations annuelles, synchronisation des données RH entre systèmes (SharePoint, Excel, Outlook). Intégrations requises : SharePoint Online, Outlook, Teams, Excel Online. Le freelance devra également créer une documentation complète et former 3 personnes de notre équipe RH.	2200.00	\N	2025-12-12 17:58:19	2025-12-12 17:58:19	98	25	\N	0	open
13	4	Refonte complète du site e-commerce avec système de recommandation	Notre boutique en ligne de vêtements nécessite une refonte complète. Nous vendons des vêtements haut de gamme et avons besoin d'un site moderne et performant. Le projet comprend : Catalogue produits avec filtres avancés (prix, taille, couleur, marque), système intelligent de recommandation de taille basé sur les mensurations, panier d'achat avec codes promo, intégration Stripe pour les paiements, gestion multi-adresses de livraison, espace client avec historique des commandes, système de wishlist, suivi de livraison en temps réel. Design responsive obligatoire.	5000.00	\N	2025-12-12 17:58:19	2025-12-12 17:58:19	9	45	\N	0	open
\.


--
-- Data for Name: proposals; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.proposals (id, project_id, freelance_id, cover_letter, proposed_amount, proposed_duration, status, created_at, updated_at) FROM stdin;
5	8	6	John Doe,\nMerci pour l’opportunité. Je serais ravi de vous accompagner dans la réalisation de votre plateforme freelance avec paiement intégré. J’ai déjà développé plusieurs applications web complètes basées sur Laravel et React/Angular, avec authentification sécurisée, gestion des rôles, messagerie temps réel et intégration de solutions de paiement (dont Stripe & escrow).\n\nPour ce projet, je vous propose une approche structurée : analyse précise de vos besoins, conception technique, développement modulaire, tests et mise en production. Mon objectif est de vous livrer une solution fiable, évolutive et conforme à votre vision.\n\nJe reste disponible pour échanger afin de clarifier les détails fonctionnels et commencer rapidement.\n\nCordialement,\nFatou Ndiaye	1800.00	60	accepted	2025-11-28 20:17:06	2025-11-29 01:44:47
\.


--
-- Data for Name: service_images; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.service_images (id, service_id, image_path, created_at, updated_at) FROM stdin;
7	9	services/mG3STJHZSTF41MyyP5gXrWdu6CJb9B6PLOy6LFy4.png	2025-11-25 18:00:45	2025-11-25 18:00:45
8	10	services/UUdI895S17HZvitzovbITF1h3xfd2x3wtj6HkFXG.jpg	2025-11-26 00:24:36	2025-11-26 00:24:36
9	10	services/30LA7acB2Mra4Ql8ThjW9t6uVKvQXfViqS4GlGFH.jpg	2025-11-26 00:24:36	2025-11-26 00:24:36
10	11	services/wOkEB0PQ8nZIXsQJcg6MNHolgl5QqoWE6s1G0KAx.png	2025-12-12 18:12:05	2025-12-12 18:12:05
11	12	services/11aNOcDXTf6s6fDMpcAXD0ULU2Zq20hyAtkIBf7b.png	2025-12-12 18:19:42	2025-12-12 18:19:42
\.


--
-- Data for Name: service_offers; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.service_offers (id, service_id, title, delivery_days, number_of_revisions, price, created_at, updated_at) FROM stdin;
14	9	Starter	7	1	500.00	2025-11-25 18:00:44	2025-11-25 18:00:44
15	10	Starter	7	1	500.00	2025-11-26 00:24:34	2025-11-26 00:24:34
16	10	Standard	15	2	1000.00	2025-11-26 00:24:34	2025-11-26 00:24:34
17	10	Advanced	30	3	2500.00	2025-11-26 00:24:34	2025-11-26 00:24:34
18	11	Starter	15	2	500.00	2025-12-12 18:12:04	2025-12-12 18:12:04
19	12	Starter	10	2	200.00	2025-12-12 18:19:42	2025-12-12 18:19:42
20	12	Standard	25	4	300.00	2025-12-12 18:19:42	2025-12-12 18:19:42
21	12	Advanced	45	6	500.00	2025-12-12 18:19:42	2025-12-12 18:19:42
\.


--
-- Data for Name: services; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.services (id, freelance_id, title, description, categorie_id, created_at, updated_at, status) FROM stdin;
9	6	Développement d'API REST sécurisée avec Laravel	Je développe une API REST complète, sécurisée, documentée et performante, adaptée à vos applications web ou mobiles.	6	2025-11-25 18:00:43	2025-11-26 17:54:37	published
10	6	Création de site web vitrine professionnel	Je développe un site web vitrine moderne, responsive et optimisé SEO pour présenter votre activité et attirer vos clients.	6	2025-11-26 00:24:34	2025-11-28 02:04:28	published
11	8	Déploiement et configuration serveur	Configuration de serveurs cloud (AWS, Google Cloud, Azure)\r\nMise en place de CI/CD avec GitHub Actions/GitLab\r\nConteneurisation avec Docker	23	2025-12-12 18:12:04	2025-12-12 18:12:04	en_attente
12	7	Optimisation et architecture de bases de données	Conception de schémas SQL/NoSQL optimisés\r\nAmélioration des performances et indexation\r\nMigration de données entre systèmes	18	2025-12-12 18:19:42	2025-12-12 18:19:42	en_attente
\.


--
-- Data for Name: sessions; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.sessions (id, user_id, ip_address, user_agent, payload, last_activity) FROM stdin;
\.


--
-- Data for Name: skills; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.skills (id, name, created_at, updated_at) FROM stdin;
2	React	2025-10-09 09:47:41	2025-10-09 09:47:41
3	Vue.js	2025-10-09 09:48:59	2025-10-09 09:48:59
4	Node.js	2025-10-09 09:49:16	2025-10-09 09:49:16
7	Figma	2025-10-09 09:49:58	2025-10-09 09:49:58
1	SEO	2025-10-09 09:47:26	2025-10-09 09:51:14
8	Laravel	2025-10-09 10:26:57	2025-10-09 10:26:57
6	Adobe Illustrator	2025-10-09 09:49:41	2025-10-23 17:07:43
5	Adobe Photoshop	2025-10-09 09:49:30	2025-10-09 09:49:30
25	PHP	2025-12-09 00:57:42	2025-12-09 00:57:42
26	JavaScript	2025-12-09 00:57:42	2025-12-09 00:57:42
27	Python	2025-12-09 00:57:42	2025-12-09 00:57:42
28	Java	2025-12-09 00:57:42	2025-12-09 00:57:42
29	C#	2025-12-09 00:57:42	2025-12-09 00:57:42
30	Ruby	2025-12-09 00:57:42	2025-12-09 00:57:42
31	Go	2025-12-09 00:57:42	2025-12-09 00:57:42
32	Swift	2025-12-09 00:57:42	2025-12-09 00:57:42
33	Kotlin	2025-12-09 00:57:42	2025-12-09 00:57:42
34	TypeScript	2025-12-09 00:57:42	2025-12-09 00:57:42
35	Rust	2025-12-09 00:57:42	2025-12-09 00:57:42
36	Dart	2025-12-09 00:57:42	2025-12-09 00:57:42
37	SQL	2025-12-09 00:57:42	2025-12-09 00:57:42
38	C++	2025-12-09 00:57:42	2025-12-09 00:57:42
39	Scala	2025-12-09 00:57:42	2025-12-09 00:57:42
40	Symfony	2025-12-09 00:57:42	2025-12-09 00:57:42
41	CodeIgniter	2025-12-09 00:57:42	2025-12-09 00:57:42
42	Angular	2025-12-09 00:57:42	2025-12-09 00:57:42
43	Next.js	2025-12-09 00:57:42	2025-12-09 00:57:42
44	Nuxt.js	2025-12-09 00:57:42	2025-12-09 00:57:42
45	Svelte	2025-12-09 00:57:42	2025-12-09 00:57:42
46	jQuery	2025-12-09 00:57:42	2025-12-09 00:57:42
47	Express.js	2025-12-09 00:57:42	2025-12-09 00:57:42
48	Django	2025-12-09 00:57:42	2025-12-09 00:57:42
49	Flask	2025-12-09 00:57:42	2025-12-09 00:57:42
50	FastAPI	2025-12-09 00:57:42	2025-12-09 00:57:42
51	Spring Boot	2025-12-09 00:57:42	2025-12-09 00:57:42
52	ASP.NET	2025-12-09 00:57:42	2025-12-09 00:57:42
53	Ruby on Rails	2025-12-09 00:57:42	2025-12-09 00:57:42
54	React Native	2025-12-09 00:57:42	2025-12-09 00:57:42
55	Flutter	2025-12-09 00:57:42	2025-12-09 00:57:42
56	Ionic	2025-12-09 00:57:42	2025-12-09 00:57:42
57	SwiftUI	2025-12-09 00:57:42	2025-12-09 00:57:42
58	Xamarin	2025-12-09 00:57:42	2025-12-09 00:57:42
59	Android SDK	2025-12-09 00:57:42	2025-12-09 00:57:42
60	iOS Development	2025-12-09 00:57:42	2025-12-09 00:57:42
61	WordPress	2025-12-09 00:57:42	2025-12-09 00:57:42
62	WooCommerce	2025-12-09 00:57:42	2025-12-09 00:57:42
63	Shopify	2025-12-09 00:57:42	2025-12-09 00:57:42
64	Magento	2025-12-09 00:57:42	2025-12-09 00:57:42
65	PrestaShop	2025-12-09 00:57:42	2025-12-09 00:57:42
66	Drupal	2025-12-09 00:57:42	2025-12-09 00:57:42
67	Joomla	2025-12-09 00:57:42	2025-12-09 00:57:42
68	Webflow	2025-12-09 00:57:42	2025-12-09 00:57:42
69	Wix	2025-12-09 00:57:42	2025-12-09 00:57:42
70	Squarespace	2025-12-09 00:57:42	2025-12-09 00:57:42
71	Adobe XD	2025-12-09 00:57:42	2025-12-09 00:57:42
72	Sketch	2025-12-09 00:57:42	2025-12-09 00:57:42
73	InVision	2025-12-09 00:57:42	2025-12-09 00:57:42
74	Canva	2025-12-09 00:57:42	2025-12-09 00:57:42
75	CorelDRAW	2025-12-09 00:57:42	2025-12-09 00:57:42
76	Affinity Designer	2025-12-09 00:57:42	2025-12-09 00:57:42
77	Adobe InDesign	2025-12-09 00:57:42	2025-12-09 00:57:42
78	UI Design	2025-12-09 00:57:42	2025-12-09 00:57:42
79	UX Design	2025-12-09 00:57:42	2025-12-09 00:57:42
80	Wireframing	2025-12-09 00:57:42	2025-12-09 00:57:42
81	Prototyping	2025-12-09 00:57:42	2025-12-09 00:57:42
82	User Research	2025-12-09 00:57:42	2025-12-09 00:57:42
83	Design Systems	2025-12-09 00:57:42	2025-12-09 00:57:42
84	Responsive Design	2025-12-09 00:57:42	2025-12-09 00:57:42
85	Mobile Design	2025-12-09 00:57:42	2025-12-09 00:57:42
86	Web Design	2025-12-09 00:57:42	2025-12-09 00:57:42
87	Interaction Design	2025-12-09 00:57:42	2025-12-09 00:57:42
88	HTML	2025-12-09 00:57:42	2025-12-09 00:57:42
89	CSS	2025-12-09 00:57:42	2025-12-09 00:57:42
90	Sass	2025-12-09 00:57:42	2025-12-09 00:57:42
91	Less	2025-12-09 00:57:42	2025-12-09 00:57:42
92	Bootstrap	2025-12-09 00:57:42	2025-12-09 00:57:42
93	Tailwind CSS	2025-12-09 00:57:42	2025-12-09 00:57:42
94	Material UI	2025-12-09 00:57:42	2025-12-09 00:57:42
95	Ant Design	2025-12-09 00:57:42	2025-12-09 00:57:42
96	Styled Components	2025-12-09 00:57:42	2025-12-09 00:57:42
97	Webpack	2025-12-09 00:57:42	2025-12-09 00:57:42
98	Vite	2025-12-09 00:57:42	2025-12-09 00:57:42
99	Babel	2025-12-09 00:57:42	2025-12-09 00:57:42
100	REST API	2025-12-09 00:57:42	2025-12-09 00:57:42
101	GraphQL	2025-12-09 00:57:42	2025-12-09 00:57:42
102	Microservices	2025-12-09 00:57:42	2025-12-09 00:57:42
103	API Development	2025-12-09 00:57:42	2025-12-09 00:57:42
104	WebSockets	2025-12-09 00:57:42	2025-12-09 00:57:42
105	JSON	2025-12-09 00:57:42	2025-12-09 00:57:42
106	XML	2025-12-09 00:57:42	2025-12-09 00:57:42
107	OAuth	2025-12-09 00:57:42	2025-12-09 00:57:42
108	JWT	2025-12-09 00:57:42	2025-12-09 00:57:42
109	MySQL	2025-12-09 00:57:42	2025-12-09 00:57:42
110	PostgreSQL	2025-12-09 00:57:42	2025-12-09 00:57:42
111	MongoDB	2025-12-09 00:57:42	2025-12-09 00:57:42
112	Redis	2025-12-09 00:57:42	2025-12-09 00:57:42
113	SQLite	2025-12-09 00:57:42	2025-12-09 00:57:42
114	MariaDB	2025-12-09 00:57:42	2025-12-09 00:57:42
115	Firebase	2025-12-09 00:57:42	2025-12-09 00:57:42
116	Oracle	2025-12-09 00:57:42	2025-12-09 00:57:42
117	Microsoft SQL Server	2025-12-09 00:57:42	2025-12-09 00:57:42
118	Elasticsearch	2025-12-09 00:57:42	2025-12-09 00:57:42
119	DynamoDB	2025-12-09 00:57:42	2025-12-09 00:57:42
120	AWS	2025-12-09 00:57:42	2025-12-09 00:57:42
121	Google Cloud	2025-12-09 00:57:42	2025-12-09 00:57:42
122	Azure	2025-12-09 00:57:42	2025-12-09 00:57:42
123	Docker	2025-12-09 00:57:42	2025-12-09 00:57:42
124	Kubernetes	2025-12-09 00:57:42	2025-12-09 00:57:42
125	CI/CD	2025-12-09 00:57:42	2025-12-09 00:57:42
126	Jenkins	2025-12-09 00:57:42	2025-12-09 00:57:42
127	Git	2025-12-09 00:57:42	2025-12-09 00:57:42
128	GitHub	2025-12-09 00:57:42	2025-12-09 00:57:42
129	GitLab	2025-12-09 00:57:42	2025-12-09 00:57:42
130	Bitbucket	2025-12-09 00:57:42	2025-12-09 00:57:42
131	Linux	2025-12-09 00:57:42	2025-12-09 00:57:42
132	Ubuntu	2025-12-09 00:57:42	2025-12-09 00:57:42
133	Nginx	2025-12-09 00:57:42	2025-12-09 00:57:42
134	Apache	2025-12-09 00:57:42	2025-12-09 00:57:42
135	Terraform	2025-12-09 00:57:42	2025-12-09 00:57:42
136	Ansible	2025-12-09 00:57:42	2025-12-09 00:57:42
137	SEO On-Page	2025-12-09 00:57:42	2025-12-09 00:57:42
138	SEO Off-Page	2025-12-09 00:57:42	2025-12-09 00:57:42
139	SEO Technique	2025-12-09 00:57:42	2025-12-09 00:57:42
140	Google Analytics	2025-12-09 00:57:42	2025-12-09 00:57:42
141	Google Search Console	2025-12-09 00:57:42	2025-12-09 00:57:42
142	Google Ads	2025-12-09 00:57:42	2025-12-09 00:57:42
143	Facebook Ads	2025-12-09 00:57:42	2025-12-09 00:57:42
144	Instagram Ads	2025-12-09 00:57:42	2025-12-09 00:57:42
145	LinkedIn Ads	2025-12-09 00:57:42	2025-12-09 00:57:42
146	TikTok Ads	2025-12-09 00:57:42	2025-12-09 00:57:42
147	Email Marketing	2025-12-09 00:57:42	2025-12-09 00:57:42
148	Marketing Automation	2025-12-09 00:57:42	2025-12-09 00:57:42
149	Content Marketing	2025-12-09 00:57:42	2025-12-09 00:57:42
150	Social Media Marketing	2025-12-09 00:57:42	2025-12-09 00:57:42
151	Copywriting	2025-12-09 00:57:42	2025-12-09 00:57:42
152	Growth Hacking	2025-12-09 00:57:42	2025-12-09 00:57:42
153	A/B Testing	2025-12-09 00:57:42	2025-12-09 00:57:42
154	Conversion Optimization	2025-12-09 00:57:42	2025-12-09 00:57:42
155	SEM	2025-12-09 00:57:42	2025-12-09 00:57:42
156	Facebook Marketing	2025-12-09 00:57:42	2025-12-09 00:57:42
157	Instagram Marketing	2025-12-09 00:57:42	2025-12-09 00:57:42
158	LinkedIn Marketing	2025-12-09 00:57:42	2025-12-09 00:57:42
159	Twitter Marketing	2025-12-09 00:57:42	2025-12-09 00:57:42
160	YouTube Marketing	2025-12-09 00:57:42	2025-12-09 00:57:42
161	TikTok Marketing	2025-12-09 00:57:42	2025-12-09 00:57:42
162	Pinterest Marketing	2025-12-09 00:57:42	2025-12-09 00:57:42
163	Community Management	2025-12-09 00:57:42	2025-12-09 00:57:42
164	Data Analysis	2025-12-09 00:57:42	2025-12-09 00:57:42
165	Google Analytics 4	2025-12-09 00:57:42	2025-12-09 00:57:42
166	Power BI	2025-12-09 00:57:42	2025-12-09 00:57:42
167	Tableau	2025-12-09 00:57:42	2025-12-09 00:57:42
168	Excel Avancé	2025-12-09 00:57:42	2025-12-09 00:57:42
169	Google Sheets	2025-12-09 00:57:42	2025-12-09 00:57:42
170	Data Visualization	2025-12-09 00:57:42	2025-12-09 00:57:42
171	Web Scraping	2025-12-09 00:57:42	2025-12-09 00:57:42
172	Python Data Science	2025-12-09 00:57:42	2025-12-09 00:57:42
173	Pandas	2025-12-09 00:57:42	2025-12-09 00:57:42
174	NumPy	2025-12-09 00:57:42	2025-12-09 00:57:42
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.users (id, full_name, email, email_verified_at, password, phone, avatar, city, country, user_type, status, remember_token, created_at, updated_at, email_verification_token, email_verification_token_expires_at) FROM stdin;
17	Mamy Laye	laahiim22@yopmail.com	2025-12-10 19:44:32	$2y$12$CVvrjZc9EkMUlSLmMCinyuoSsd4nDlWz.TbSbNNJ5RveR3kmkQC1O	+22177654321	1765395848_6939cd883e7cc.jpg	Malika	Sénégal	freelance	active	\N	2025-12-10 19:44:08	2025-12-10 19:44:32	\N	\N
18	Lamine Diop	dlamine22@yopmail.com	2025-12-10 19:57:30	$2y$12$F0O0.oDtZ9DnIDkMkJTG9OZvrMlPc4bq7eReTZNTPnwE9XE5wExoe	+221770254568	1765396628_6939d0946782e.jpg	Keur Massar	Sénégal	client	active	\N	2025-12-10 19:57:08	2025-12-10 19:57:30	\N	\N
14	Modou Ndiaye	ndiayem22@yopmail.com	2025-11-20 13:37:04	$2y$12$nVXf1UsrNa7QhNjmmipwW.CCCPnEFenzKvFib0ZQzVUoqmgPXoKaK	+221771234568	1763645739_691f192b09b94.jpg	Rufisque	ndiayem22@yopmail.com	client	active	\N	2025-11-20 13:35:39	2025-11-20 13:37:04	\N	\N
15	Fatou Ndiaye	fatoun22@yopmail.com	2025-11-20 14:11:35	$2y$12$x17EPIllLEEznpmlE61pResUoTSZEkOPf91QW7Lfznda/DmTCmXfW	+221771234568	\N	Dakar	Sénégal	freelance	active	\N	2025-11-20 14:11:00	2025-11-20 14:11:35	\N	\N
1	Admin Principal	admin@digitalconnect.com	2025-11-20 16:11:35	$2y$12$CfmEfhKYjQLLDzw0Sc3CH.mELlYNa2MFYvrYHsoudVreAHv9WLLVC	+221771234567	\N	Dakar	Sénégal	admin	active	\N	2025-10-08 10:34:51	2025-10-08 10:34:51	\N	\N
11	John Doe	digitalconnect@yopmail.com	2025-11-18 01:32:32	$2y$12$WqsQVqO/xSjfc73CFnDvTefDgSQLLT3e0qaBBelabZvIH41Zq/ROu	\N	\N	Paris	France	client	active	\N	2025-11-18 01:26:55	2025-11-18 01:32:32	\N	\N
16	Rokhaya Sylla	rs88@yopmail.com	2025-12-10 19:28:37	$2y$12$NZc/if8bTumbCXV8j6y9tucxPiFevHFtqmkjt5yVBz02oCj.F35Xu	+221771234568	1765394775_6939c9578b618.jpg	dakar	Sénégal	freelance	active	\N	2025-12-10 19:26:17	2025-12-10 19:28:37	\N	\N
19	Ousseynou Faye	fousseynou22@yopmail.com	2025-12-12 15:23:28	$2y$12$thpCPvP/u1DRjcM9b15GmOBGXo88CzEvJmSHZaF/Ijyg5c7OfpYE2	+221771234568	1765554222_693c382ec0359.jpg	Dakar	Sénégal	client	active	\N	2025-12-12 15:22:07	2025-12-12 15:43:42	\N	\N
4	Moussa Diop	freelance00@digitalconnect.com	2025-12-12 15:21:32	$2y$12$I7k7kbSoVw4U7Q7Ys1AjU.xQFlptCG57d7ur2R0.3ywcLtyG7F1nS	+221771234569	\N	Thiès	Sénégal	freelance	active	\N	2025-10-08 11:03:15	2025-10-08 11:03:15	\N	\N
3	Modou Ndiaye	client01@digitalconnect.com	2025-12-12 14:44:32	$2y$12$N7SOWJKRVMhyoBlqwHT41OoAGtmoJuPVOHAIn8bqVPO9z0RKq840i	+221771234568	\N	Rufisque	Sénégal	client	active	\N	2025-10-08 11:00:03	2025-11-05 13:53:40	\N	\N
8	fatou faye	fatouf@freelance.sn	2025-12-06 19:29:32	$2y$12$wI9MW17tfdcpSIQhfgWZWeVIA9jm7NwFPfPYeT0pzBmPFlobBAe/W	+221776543210	1759923671_68e64dd705489.png	Dakar	Sénégal	freelance	active	\N	2025-10-08 11:41:11	2025-10-08 11:41:11	\N	\N
2	Admin secondaire	adminsecondaire@digitalconnect.com	2025-12-01 22:39:32	$2y$12$PfbDVWsev5O2pmqlhGAs2eDakvkX/O2YDdEQ9KCKpf0UuC8F6isea	+221771234567	\N	Dakar	Sénégal	admin	active	\N	2025-10-08 10:51:36	2025-10-08 10:51:36	\N	\N
5	Aminata Touré	aminata@freelance.sn	2025-12-03 20:44:32	$2y$12$QIT5efFKo3lOi8NmyFwBX.TfCdyFv1wggOLLshAqMUfUzls3QQut2	+221776543210	\N	Dakar	Sénégal	freelance	active	\N	2025-10-08 11:33:41	2025-10-08 11:33:41	\N	\N
9	Fallou ndiaye	fallo@digitalconnect.com	2025-12-12 16:50:32	$2y$12$OvfYbN6xVAw/RIcRl06DtO39yTBmqyFb5NXPOSndW5u76oPU1vfse	+221771234568	\N	Dakar	Sénégal	client	active	\N	2025-10-11 14:31:27	2025-10-11 14:31:27	\N	\N
\.


--
-- Data for Name: websockets_statistics_entries; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.websockets_statistics_entries (id, app_id, peak_connection_count, websocket_message_count, api_message_count, created_at, updated_at) FROM stdin;
\.


--
-- Name: attachements_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.attachements_id_seq', 16, true);


--
-- Name: categories_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.categories_id_seq', 106, true);


--
-- Name: clients_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.clients_id_seq', 9, true);


--
-- Name: contracts_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.contracts_id_seq', 3, true);


--
-- Name: conversations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.conversations_id_seq', 5, true);


--
-- Name: device_tokens_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.device_tokens_id_seq', 72, true);


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.failed_jobs_id_seq', 1, false);


--
-- Name: freelance_skills_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.freelance_skills_id_seq', 1, false);


--
-- Name: freelances_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.freelances_id_seq', 8, true);


--
-- Name: jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.jobs_id_seq', 337, true);


--
-- Name: messages_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.messages_id_seq', 53, true);


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.migrations_id_seq', 38, true);


--
-- Name: orders_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.orders_id_seq', 6, true);


--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.personal_access_tokens_id_seq', 1, false);


--
-- Name: project_skills_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.project_skills_id_seq', 68, true);


--
-- Name: project_tasks_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.project_tasks_id_seq', 67, true);


--
-- Name: projects_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.projects_id_seq', 11, true);


--
-- Name: proposals_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.proposals_id_seq', 5, true);


--
-- Name: service_images_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.service_images_id_seq', 11, true);


--
-- Name: service_offers_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.service_offers_id_seq', 21, true);


--
-- Name: services_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.services_id_seq', 12, true);


--
-- Name: skills_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.skills_id_seq', 199, true);


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.users_id_seq', 19, true);


--
-- Name: websockets_statistics_entries_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.websockets_statistics_entries_id_seq', 1, false);


--
-- Name: attachements attachements_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.attachements
    ADD CONSTRAINT attachements_pkey PRIMARY KEY (id);


--
-- Name: cache_locks cache_locks_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cache_locks
    ADD CONSTRAINT cache_locks_pkey PRIMARY KEY (key);


--
-- Name: cache cache_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cache
    ADD CONSTRAINT cache_pkey PRIMARY KEY (key);


--
-- Name: categories categories_name_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.categories
    ADD CONSTRAINT categories_name_unique UNIQUE (name);


--
-- Name: categories categories_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.categories
    ADD CONSTRAINT categories_pkey PRIMARY KEY (id);


--
-- Name: clients clients_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.clients
    ADD CONSTRAINT clients_pkey PRIMARY KEY (id);


--
-- Name: contracts contracts_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.contracts
    ADD CONSTRAINT contracts_pkey PRIMARY KEY (id);


--
-- Name: conversations conversations_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.conversations
    ADD CONSTRAINT conversations_pkey PRIMARY KEY (id);


--
-- Name: device_tokens device_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.device_tokens
    ADD CONSTRAINT device_tokens_pkey PRIMARY KEY (id);


--
-- Name: device_tokens device_tokens_token_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.device_tokens
    ADD CONSTRAINT device_tokens_token_unique UNIQUE (token);


--
-- Name: failed_jobs failed_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_uuid_unique UNIQUE (uuid);


--
-- Name: freelance_skills freelance_skills_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.freelance_skills
    ADD CONSTRAINT freelance_skills_pkey PRIMARY KEY (id);


--
-- Name: freelances freelances_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.freelances
    ADD CONSTRAINT freelances_pkey PRIMARY KEY (id);


--
-- Name: jobs jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jobs
    ADD CONSTRAINT jobs_pkey PRIMARY KEY (id);


--
-- Name: messages messages_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.messages
    ADD CONSTRAINT messages_pkey PRIMARY KEY (id);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: orders orders_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.orders
    ADD CONSTRAINT orders_pkey PRIMARY KEY (id);


--
-- Name: password_reset_tokens password_reset_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.password_reset_tokens
    ADD CONSTRAINT password_reset_tokens_pkey PRIMARY KEY (email);


--
-- Name: personal_access_tokens personal_access_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_pkey PRIMARY KEY (id);


--
-- Name: personal_access_tokens personal_access_tokens_token_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_token_unique UNIQUE (token);


--
-- Name: project_skills project_skills_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.project_skills
    ADD CONSTRAINT project_skills_pkey PRIMARY KEY (id);


--
-- Name: project_tasks project_tasks_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.project_tasks
    ADD CONSTRAINT project_tasks_pkey PRIMARY KEY (id);


--
-- Name: projects projects_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.projects
    ADD CONSTRAINT projects_pkey PRIMARY KEY (id);


--
-- Name: proposals proposals_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.proposals
    ADD CONSTRAINT proposals_pkey PRIMARY KEY (id);


--
-- Name: service_images service_images_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.service_images
    ADD CONSTRAINT service_images_pkey PRIMARY KEY (id);


--
-- Name: service_offers service_offers_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.service_offers
    ADD CONSTRAINT service_offers_pkey PRIMARY KEY (id);


--
-- Name: service_offers service_offers_service_id_title_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.service_offers
    ADD CONSTRAINT service_offers_service_id_title_unique UNIQUE (service_id, title);


--
-- Name: services services_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.services
    ADD CONSTRAINT services_pkey PRIMARY KEY (id);


--
-- Name: sessions sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT sessions_pkey PRIMARY KEY (id);


--
-- Name: skills skills_name_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.skills
    ADD CONSTRAINT skills_name_unique UNIQUE (name);


--
-- Name: skills skills_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.skills
    ADD CONSTRAINT skills_pkey PRIMARY KEY (id);


--
-- Name: conversations unique_conversation_participants; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.conversations
    ADD CONSTRAINT unique_conversation_participants UNIQUE (client_id, freelance_id);


--
-- Name: users users_email_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_unique UNIQUE (email);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: websockets_statistics_entries websockets_statistics_entries_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.websockets_statistics_entries
    ADD CONSTRAINT websockets_statistics_entries_pkey PRIMARY KEY (id);


--
-- Name: attachements_attachable_type_attachable_id_file_type_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX attachements_attachable_type_attachable_id_file_type_index ON public.attachements USING btree (attachable_type, attachable_id, file_type);


--
-- Name: attachements_attachable_type_attachable_id_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX attachements_attachable_type_attachable_id_index ON public.attachements USING btree (attachable_type, attachable_id);


--
-- Name: conversations_last_message_at_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX conversations_last_message_at_index ON public.conversations USING btree (last_message_at);


--
-- Name: conversations_status_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX conversations_status_index ON public.conversations USING btree (status);


--
-- Name: device_tokens_user_id_is_active_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX device_tokens_user_id_is_active_index ON public.device_tokens USING btree (user_id, is_active);


--
-- Name: idx_conversation_status; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_conversation_status ON public.conversations USING btree (status);


--
-- Name: jobs_queue_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX jobs_queue_index ON public.jobs USING btree (queue);


--
-- Name: personal_access_tokens_tokenable_type_tokenable_id_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX personal_access_tokens_tokenable_type_tokenable_id_index ON public.personal_access_tokens USING btree (tokenable_type, tokenable_id);


--
-- Name: project_tasks_project_id_order_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX project_tasks_project_id_order_index ON public.project_tasks USING btree (project_id, "order");


--
-- Name: project_tasks_project_id_priority_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX project_tasks_project_id_priority_index ON public.project_tasks USING btree (project_id, priority);


--
-- Name: sessions_last_activity_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sessions_last_activity_index ON public.sessions USING btree (last_activity);


--
-- Name: sessions_user_id_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sessions_user_id_index ON public.sessions USING btree (user_id);


--
-- Name: attachements attachements_uploaded_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.attachements
    ADD CONSTRAINT attachements_uploaded_by_foreign FOREIGN KEY (uploaded_by) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: clients clients_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.clients
    ADD CONSTRAINT clients_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: contracts contracts_client_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.contracts
    ADD CONSTRAINT contracts_client_id_foreign FOREIGN KEY (client_id) REFERENCES public.clients(id) ON DELETE CASCADE;


--
-- Name: contracts contracts_freelance_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.contracts
    ADD CONSTRAINT contracts_freelance_id_foreign FOREIGN KEY (freelance_id) REFERENCES public.freelances(id) ON DELETE CASCADE;


--
-- Name: contracts contracts_project_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.contracts
    ADD CONSTRAINT contracts_project_id_foreign FOREIGN KEY (project_id) REFERENCES public.projects(id) ON DELETE CASCADE;


--
-- Name: contracts contracts_proposal_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.contracts
    ADD CONSTRAINT contracts_proposal_id_foreign FOREIGN KEY (proposal_id) REFERENCES public.proposals(id) ON DELETE CASCADE;


--
-- Name: conversations conversations_client_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.conversations
    ADD CONSTRAINT conversations_client_id_foreign FOREIGN KEY (client_id) REFERENCES public.clients(id) ON DELETE CASCADE;


--
-- Name: conversations conversations_freelance_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.conversations
    ADD CONSTRAINT conversations_freelance_id_foreign FOREIGN KEY (freelance_id) REFERENCES public.freelances(id) ON DELETE CASCADE;


--
-- Name: device_tokens device_tokens_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.device_tokens
    ADD CONSTRAINT device_tokens_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: freelance_skills freelance_skills_freelance_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.freelance_skills
    ADD CONSTRAINT freelance_skills_freelance_id_foreign FOREIGN KEY (freelance_id) REFERENCES public.freelances(id) ON DELETE CASCADE;


--
-- Name: freelance_skills freelance_skills_skill_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.freelance_skills
    ADD CONSTRAINT freelance_skills_skill_id_foreign FOREIGN KEY (skill_id) REFERENCES public.skills(id) ON DELETE CASCADE;


--
-- Name: freelances freelances_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.freelances
    ADD CONSTRAINT freelances_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: messages messages_conversation_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.messages
    ADD CONSTRAINT messages_conversation_id_foreign FOREIGN KEY (conversation_id) REFERENCES public.conversations(id) ON DELETE CASCADE;


--
-- Name: messages messages_sender_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.messages
    ADD CONSTRAINT messages_sender_id_foreign FOREIGN KEY (sender_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: orders orders_client_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.orders
    ADD CONSTRAINT orders_client_id_foreign FOREIGN KEY (client_id) REFERENCES public.clients(id) ON DELETE CASCADE;


--
-- Name: orders orders_service_offer_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.orders
    ADD CONSTRAINT orders_service_offer_id_foreign FOREIGN KEY (service_offer_id) REFERENCES public.service_offers(id) ON DELETE CASCADE;


--
-- Name: project_skills project_skills_project_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.project_skills
    ADD CONSTRAINT project_skills_project_id_foreign FOREIGN KEY (project_id) REFERENCES public.projects(id) ON DELETE CASCADE;


--
-- Name: project_skills project_skills_skill_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.project_skills
    ADD CONSTRAINT project_skills_skill_id_foreign FOREIGN KEY (skill_id) REFERENCES public.skills(id) ON DELETE CASCADE;


--
-- Name: project_tasks project_tasks_project_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.project_tasks
    ADD CONSTRAINT project_tasks_project_id_foreign FOREIGN KEY (project_id) REFERENCES public.projects(id) ON DELETE CASCADE;


--
-- Name: projects projects_category_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.projects
    ADD CONSTRAINT projects_category_id_foreign FOREIGN KEY (category_id) REFERENCES public.categories(id) ON DELETE CASCADE;


--
-- Name: projects projects_client_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.projects
    ADD CONSTRAINT projects_client_id_foreign FOREIGN KEY (client_id) REFERENCES public.clients(id) ON DELETE CASCADE;


--
-- Name: proposals proposals_freelance_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.proposals
    ADD CONSTRAINT proposals_freelance_id_foreign FOREIGN KEY (freelance_id) REFERENCES public.freelances(id) ON DELETE CASCADE;


--
-- Name: proposals proposals_project_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.proposals
    ADD CONSTRAINT proposals_project_id_foreign FOREIGN KEY (project_id) REFERENCES public.projects(id) ON DELETE CASCADE;


--
-- Name: service_images service_images_service_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.service_images
    ADD CONSTRAINT service_images_service_id_foreign FOREIGN KEY (service_id) REFERENCES public.services(id) ON DELETE CASCADE;


--
-- Name: service_offers service_offers_service_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.service_offers
    ADD CONSTRAINT service_offers_service_id_foreign FOREIGN KEY (service_id) REFERENCES public.services(id) ON DELETE CASCADE;


--
-- Name: services services_categorie_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.services
    ADD CONSTRAINT services_categorie_id_foreign FOREIGN KEY (categorie_id) REFERENCES public.categories(id) ON DELETE CASCADE;


--
-- Name: services services_freelance_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.services
    ADD CONSTRAINT services_freelance_id_foreign FOREIGN KEY (freelance_id) REFERENCES public.freelances(id) ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--

