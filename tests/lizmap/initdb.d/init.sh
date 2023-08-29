#!/bin/bash

# Create user lizmap which will create and own the netads database & schema
psql --username postgres --no-password <<-EOSQL
    CREATE ROLE lizmap WITH LOGIN CREATEDB PASSWORD 'lizmap1234!';
    CREATE DATABASE lizmap WITH OWNER lizmap;
EOSQL

# Create extensions postgis
psql --username postgres --no-password -d lizmap <<-EOSQL
    CREATE EXTENSION IF NOT EXISTS postgis SCHEMA public;
EOSQL
