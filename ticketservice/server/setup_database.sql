--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

--
-- Name: plpgsql; Type: PROCEDURAL LANGUAGE; Schema: -; Owner: postgres
--

CREATE PROCEDURAL LANGUAGE plpgsql;


ALTER PROCEDURAL LANGUAGE plpgsql OWNER TO postgres;

SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: apiKey; Type: TABLE; Schema: public; Owner: ts; Tablespace: 
--

CREATE TABLE "apiKey" (
    "clientId" integer NOT NULL,
    key character(32) NOT NULL,
    secret character(32) NOT NULL
);


ALTER TABLE public."apiKey" OWNER TO ts;

--
-- Name: client; Type: TABLE; Schema: public; Owner: ts; Tablespace: 
--

CREATE TABLE client (
    id integer NOT NULL
);


ALTER TABLE public.client OWNER TO ts;

--
-- Name: reservation; Type: TABLE; Schema: public; Owner: ts; Tablespace: 
--

CREATE TABLE reservation (
    seat smallint NOT NULL,
    "clientId" integer NOT NULL,
    name character varying(32) NOT NULL
);


ALTER TABLE public.reservation OWNER TO ts;

--
-- Data for Name: apiKey; Type: TABLE DATA; Schema: public; Owner: ts
--

COPY "apiKey" ("clientId", key, secret) FROM stdin;
1	056172e185b8045a4e037ff4ce4ae80b	c3f154ee8f4f6735d8f75384825472a8
\.


--
-- Data for Name: client; Type: TABLE DATA; Schema: public; Owner: ts
--

COPY client (id) FROM stdin;
1
\.


--
-- Data for Name: reservation; Type: TABLE DATA; Schema: public; Owner: ts
--

COPY reservation (seat, "clientId", name) FROM stdin;
\.


--
-- Name: apiKey_pkey; Type: CONSTRAINT; Schema: public; Owner: ts; Tablespace: 
--

ALTER TABLE ONLY "apiKey"
    ADD CONSTRAINT "apiKey_pkey" PRIMARY KEY (key);


--
-- Name: client_pkey; Type: CONSTRAINT; Schema: public; Owner: ts; Tablespace: 
--

ALTER TABLE ONLY client
    ADD CONSTRAINT client_pkey PRIMARY KEY (id);


--
-- Name: reservation_pkey; Type: CONSTRAINT; Schema: public; Owner: ts; Tablespace: 
--

ALTER TABLE ONLY reservation
    ADD CONSTRAINT reservation_pkey PRIMARY KEY (seat);


--
-- Name: fki_; Type: INDEX; Schema: public; Owner: ts; Tablespace: 
--

CREATE INDEX fki_ ON "apiKey" USING btree ("clientId");


--
-- Name: apiKey_clientId_fkey; Type: FK CONSTRAINT; Schema: public; Owner: ts
--

ALTER TABLE ONLY "apiKey"
    ADD CONSTRAINT "apiKey_clientId_fkey" FOREIGN KEY ("clientId") REFERENCES client(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: reservation_clientId_fkey; Type: FK CONSTRAINT; Schema: public; Owner: ts
--

ALTER TABLE ONLY reservation
    ADD CONSTRAINT "reservation_clientId_fkey" FOREIGN KEY ("clientId") REFERENCES client(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: public; Type: ACL; Schema: -; Owner: ts
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM ts;
GRANT ALL ON SCHEMA public TO ts;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- Name: apiKey; Type: ACL; Schema: public; Owner: ts
--

REVOKE ALL ON TABLE "apiKey" FROM PUBLIC;
REVOKE ALL ON TABLE "apiKey" FROM ts;
GRANT ALL ON TABLE "apiKey" TO ts;
GRANT ALL ON TABLE "apiKey" TO PUBLIC;


--
-- Name: client; Type: ACL; Schema: public; Owner: ts
--

REVOKE ALL ON TABLE client FROM PUBLIC;
REVOKE ALL ON TABLE client FROM ts;
GRANT ALL ON TABLE client TO ts;
GRANT ALL ON TABLE client TO PUBLIC;


--
-- Name: reservation; Type: ACL; Schema: public; Owner: ts
--

REVOKE ALL ON TABLE reservation FROM PUBLIC;
REVOKE ALL ON TABLE reservation FROM ts;
GRANT ALL ON TABLE reservation TO ts;
GRANT ALL ON TABLE reservation TO PUBLIC;


--
-- PostgreSQL database dump complete
--

