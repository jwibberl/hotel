--
-- PostgreSQL database dump
--

\restrict TAU2iohme7f70UsSDDGDo3rndrb0YYDicLG1HKDpB9VLTTiRpMN42JerltMY6au

-- Dumped from database version 13.22 (Debian 13.22-0+deb11u1)
-- Dumped by pg_dump version 13.22 (Debian 13.22-0+deb11u1)

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
-- Name: booking; Type: TABLE; Schema: public; Owner: jwibberl
--

CREATE TABLE public.booking (
    bookingid integer NOT NULL,
    customerid integer,
    roomid integer,
    datefrom date,
    dateto date
);


ALTER TABLE public.booking OWNER TO jwibberl;

--
-- Name: booking_bookingid_seq; Type: SEQUENCE; Schema: public; Owner: jwibberl
--

CREATE SEQUENCE public.booking_bookingid_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.booking_bookingid_seq OWNER TO jwibberl;

--
-- Name: booking_bookingid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: jwibberl
--

ALTER SEQUENCE public.booking_bookingid_seq OWNED BY public.booking.bookingid;


--
-- Name: customer; Type: TABLE; Schema: public; Owner: jwibberl
--

CREATE TABLE public.customer (
    customerid integer NOT NULL,
    customername character varying,
    customerpostcode character varying
);


ALTER TABLE public.customer OWNER TO jwibberl;

--
-- Name: customer_customerid_seq; Type: SEQUENCE; Schema: public; Owner: jwibberl
--

CREATE SEQUENCE public.customer_customerid_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.customer_customerid_seq OWNER TO jwibberl;

--
-- Name: customer_customerid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: jwibberl
--

ALTER SEQUENCE public.customer_customerid_seq OWNED BY public.customer.customerid;


--
-- Name: room; Type: TABLE; Schema: public; Owner: jwibberl
--

CREATE TABLE public.room (
    roomid integer NOT NULL,
    roomnumber integer,
    roomname character varying
);


ALTER TABLE public.room OWNER TO jwibberl;

--
-- Name: room_roomid_seq; Type: SEQUENCE; Schema: public; Owner: jwibberl
--

CREATE SEQUENCE public.room_roomid_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.room_roomid_seq OWNER TO jwibberl;

--
-- Name: room_roomid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: jwibberl
--

ALTER SEQUENCE public.room_roomid_seq OWNED BY public.room.roomid;


--
-- Name: booking bookingid; Type: DEFAULT; Schema: public; Owner: jwibberl
--

ALTER TABLE ONLY public.booking ALTER COLUMN bookingid SET DEFAULT nextval('public.booking_bookingid_seq'::regclass);


--
-- Name: customer customerid; Type: DEFAULT; Schema: public; Owner: jwibberl
--

ALTER TABLE ONLY public.customer ALTER COLUMN customerid SET DEFAULT nextval('public.customer_customerid_seq'::regclass);


--
-- Name: room roomid; Type: DEFAULT; Schema: public; Owner: jwibberl
--

ALTER TABLE ONLY public.room ALTER COLUMN roomid SET DEFAULT nextval('public.room_roomid_seq'::regclass);


--
-- Name: booking booking_pkey; Type: CONSTRAINT; Schema: public; Owner: jwibberl
--

ALTER TABLE ONLY public.booking
    ADD CONSTRAINT booking_pkey PRIMARY KEY (bookingid);


--
-- Name: customer customer_pkey; Type: CONSTRAINT; Schema: public; Owner: jwibberl
--

ALTER TABLE ONLY public.customer
    ADD CONSTRAINT customer_pkey PRIMARY KEY (customerid);


--
-- Name: room room_pkey; Type: CONSTRAINT; Schema: public; Owner: jwibberl
--

ALTER TABLE ONLY public.room
    ADD CONSTRAINT room_pkey PRIMARY KEY (roomid);


--
-- Name: booking booking_customerid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: jwibberl
--

ALTER TABLE ONLY public.booking
    ADD CONSTRAINT booking_customerid_fkey FOREIGN KEY (customerid) REFERENCES public.customer(customerid);


--
-- Name: booking booking_roomid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: jwibberl
--

ALTER TABLE ONLY public.booking
    ADD CONSTRAINT booking_roomid_fkey FOREIGN KEY (roomid) REFERENCES public.room(roomid);


--
-- PostgreSQL database dump complete
--

\unrestrict TAU2iohme7f70UsSDDGDo3rndrb0YYDicLG1HKDpB9VLTTiRpMN42JerltMY6au

