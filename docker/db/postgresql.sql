DO
$body$
BEGIN
   IF NOT EXISTS (
      SELECT *
      FROM   pg_catalog.pg_user
      WHERE  usename = 'travelbook'
   )
   THEN
      CREATE ROLE travelbook SUPERUSER LOGIN;
   END IF;
END
$body$;
