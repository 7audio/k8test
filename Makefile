init:
	rm -f db/db.sqlite && touch db/db.sqlite
	sqlite3 db/db.sqlite < db/init.sql

seed-users:
	php tools/SeedUsers.php

populate-check-emails:
	php tools/PopulateCheckEmailQueue.php

check-emails:
	php tools/CheckEmails.php

send-emails:
	php tools/SendEmails.php
